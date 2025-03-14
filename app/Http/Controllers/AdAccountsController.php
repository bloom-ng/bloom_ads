<?php

namespace App\Http\Controllers;

use App\Models\AdAccount;
use App\Services\RockAds;
use Illuminate\Http\Request;
use Illuminate\Http\Client\RequestException;
use App\Models\Wallet;
use App\Models\AdAccountTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\FacadesLog;
use App\Notifications\AdAccountNotification;
use App\Models\BusinessManager;
use App\Services\Meta\AdAccountService as MetaAdAccountService;
use App\Models\Admin;
use App\Mail\AdAccountRequestMail;
use Illuminate\Support\Facades\Mail;

class AdAccountsController extends Controller
{
    protected $rockAds;

    public function __construct()
    {
        $this->rockAds = new RockAds();
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $currentOrganizationId = $user->settings?->current_organization_id;

        $query = AdAccount::with(['organization'])
            ->where('organization_id', $currentOrganizationId);

        // Filter by name if provided
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $adAccounts = $query->paginate(10);

        // Add flash message if no results found
        if ($adAccounts->isEmpty()) {
            if ($request->filled('name') || $request->filled('status')) {
                // If filters were applied
                $message = 'No ad accounts found matching your filters.';
                if ($request->filled('name')) {
                    $message .= " Name: '" . $request->input('name') . "'";
                }
                if ($request->filled('status')) {
                    $message .= " Status: '" . $request->input('status') . "'";
                }
                return redirect()->back()->with('info', $message);
            }
        }

        return view('dashboard.adaccounts.index', compact('adAccounts'));
    }

    public function create()
    {
        try {
            // Get platforms from RockAds API
            $platformsResponse = $this->rockAds->getAdPlatforms();

            Log::info('Initial platforms response:', [
                'platforms_count' => count($platformsResponse->platforms),
                'platforms' => array_map(function ($p) {
                    return ['id' => $p->id, 'name' => $p->name];
                }, $platformsResponse->platforms)
            ]);

            // Transform the data for the view
            $platforms = collect($platformsResponse->platforms)
                ->filter(function ($platform) {
                    $hasRequired = $platform->id && $platform->name;
                    Log::info('Filtering platform:', [
                        'id' => $platform->id,
                        'name' => $platform->name,
                        'passes_filter' => $hasRequired
                    ]);
                    return $hasRequired;
                })
                ->map(function ($platform) {
                    $data = [
                        'id' => $platform->getCode(),
                        'name' => $platform->name
                    ];
                    Log::info('Mapping platform:', $data);
                    return $data;
                })
                ->values();

            Log::info('Final platforms collection:', [
                'count' => $platforms->count(),
                'data' => $platforms->toArray()
            ]);

            $timezonesResponse = $this->rockAds->getTimezones();

            Log::info('Timezones Response Object:', [
                'raw' => $timezonesResponse,
                'timezones_count' => count($timezonesResponse->timezones)
            ]);

            $timezones = collect($timezonesResponse->timezones)
                ->filter(function ($timezone) {
                    $hasRequired = $timezone->id && $timezone->name;
                    Log::info('Filtering timezone:', [
                        'id' => $timezone->id,
                        'name' => $timezone->name,
                        'offset_str' => $timezone->offset_str,
                        'passes_filter' => $hasRequired
                    ]);
                    return $hasRequired;
                })
                ->map(function ($timezone) {
                    $data = [
                        'id' => $timezone->id,
                        'name' => $timezone->name . ' (' . $timezone->offset_str . ')'
                    ];
                    Log::info('Mapping timezone:', $data);
                    return $data;
                })
                ->values();

            Log::info('Final timezones collection:', [
                'count' => $timezones->count(),
                'data' => $timezones->toArray()
            ]);

            // Convert to array before passing to view and add debug
            $timezonesArray = $timezones->toArray();
            Log::info('Timezones being passed to view:', $timezonesArray);

            return view('dashboard.adaccounts.create', [
                'platforms' => $platforms->toArray(),
                'timezones' => $timezonesArray,
                'currencies' => ['USD', 'NGN']
            ]);
        } catch (RequestException $e) {
            Log::error('RockAds API Error:', [
                'message' => $e->getMessage(),
                'response' => $e->response?->json(),
                'status' => $e->response?->status()
            ]);
            return redirect()->back()
                ->with('error', 'Unable to fetch ad platforms data. Please check your API configuration.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:meta,google,tiktok',
                'timezone' => 'required|string',
                'currency' => 'required|in:USD,NGN',
                'business_manager_id' => 'nullable|string|max:35',
                'landing_page' => 'nullable|url',
                'facebook_page_url' => 'required|url',
            ]);

            $user = Auth::user();

            $validated['user_id'] = $user->id;
            $validated['organization_id'] = $user->settings?->current_organization_id;
            $validated['status'] = AdAccount::STATUS_PROCESSING;

            $adAccount = AdAccount::create($validated);

            // Notify all admins about the new ad account request
            $admins = Admin::getAllAdmins();
            foreach ($admins as $admin) {
                try {
                    Mail::to($admin->email)->send(new AdAccountRequestMail($user, [
                        'account_name' => $validated['name'],
                        'account_type' => $validated['type'],
                        'timezone' => $validated['timezone'],
                        'currency' => $validated['currency'],
                        'business_manager_id' => $validated['business_manager_id'] ?? 'Not provided',
                        'landing_page' => $validated['landing_page'] ?? 'Not provided',
                        'facebook_page_url' => $validated['facebook_page_url'],
                    ]));
                    Log::info('Ad account request notification sent to admin', [
                        'admin_id' => $admin->id,
                        'admin_email' => $admin->email
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send ad account request notification to admin', [
                        'admin_id' => $admin->id,
                        'admin_email' => $admin->email,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return redirect()->route('adaccounts.index')
                ->with('success', 'Ad Account created successfully');
        } catch (\Exception $e) {
            Log::error('Ad Account Creation Error:', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'organization' => auth()->user()->currentOrganization
            ]);

            return redirect()->back()
                ->with('error', 'Error creating ad account: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(AdAccount $adAccount)
    {
        // Get the authenticated user with their organizations
        $user = auth()->user()->load('organizations');

        // Check if user owns this ad account or is part of the organization
        if ($adAccount->user_id !== $user->id && !$user->organizations->contains($adAccount->organization_id)) {
            abort(403);
        }

        return view('dashboard.adaccounts.edit', [
            'adAccount' => $adAccount,
            'currencies' => ['USD', 'NGN', 'GBP']
        ]);
    }

    public function update(Request $request, AdAccount $adAccount)
    {
        // Get the authenticated user with their organizations
        $user = auth()->user()->load('organizations');

        // Check if user owns this ad account or is part of the organization
        if ($adAccount->user_id !== $user->id && !$user->organizations->contains($adAccount->organization_id)) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:meta,google,tiktok',
            'currency' => 'required|in:USD,NGN',
            'business_manager_id' => 'nullable|string|max:35',
            'landing_page' => 'nullable|url',
            'facebook_page_url' => 'required|url',
        ]);

        $adAccount->update($validated);

        return redirect()
            ->route('adaccounts.index')
            ->with('success', 'Ad Account updated successfully');
    }

    public function destroy(AdAccount $adAccount)
    {
        // Get the authenticated user with their organizations
        $user = auth()->user()->load('organizations');

        // Check if user owns this ad account or is part of the organization
        if ($adAccount->user_id !== $user->id && !$user->organizations->contains($adAccount->organization_id)) {
            abort(403);
        }

        // Check if status is processing
        if ($adAccount->status !== 'processing') {
            return redirect()
                ->back()
                ->with('error', 'Only ad accounts in processing status can be deleted.');
        }

        $adAccount->delete();

        return redirect()
            ->back()
            ->with('success', 'Ad Account deleted successfully');
    }

    public function deposit(Request $request, AdAccount $adAccount)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:1',
                'wallet_id' => 'required|exists:wallets,id',
            ]);

            $businessManager = BusinessManager::find($adAccount->provider_bm_id);

            // Check if account is approved
            if ($adAccount->status !== AdAccount::STATUS_APPROVED) {
                return redirect()->back()
                    ->with('error', 'Ad account must be approved to deposit funds');
            }

            $wallet = Wallet::findOrFail($validated['wallet_id']);

            // Check if currencies match
            if ($wallet->currency !== $adAccount->currency) {
                return redirect()->back()
                    ->with('error', 'Wallet currency must match ad account currency');
            }

            // For Meta ad accounts, check if account has provider_id
            if ($adAccount->type === 'meta') {
                if (!$adAccount->provider_id) {
                    return redirect()->back()
                        ->with('error', 'Meta ad account must be linked to deposit funds');
                }
            }

            // Calculate fees
            $fees = $adAccount->calculateFees($validated['amount']);
            $totalAmount = $fees['total_amount'];

            // Check if wallet has sufficient balance using getBalance()
            if ($wallet->getBalance() < $totalAmount) {
                return redirect()->back()
                    ->with('error', 'Insufficient wallet balance');
            }

            // Create transaction record
            $transaction = new AdAccountTransaction([
                'ad_account_id' => $adAccount->id,
                'wallet_id' => $wallet->id,
                'amount' => $validated['amount'],
                'type' => 'deposit',
                'vat' => $fees['vat'],
                'service_fee' => $fees['service_fee'],
                'total_amount' => $totalAmount,
                'reference' => 'AD-' . Str::random(20),
                'description' => 'Deposit to ad account'
            ]);

            DB::transaction(function () use ($wallet, $transaction, $totalAmount, $adAccount, $validated, $businessManager, $fees) {
                // Save transaction
                $transaction->save();

                // Create a wallet transaction for the base amount
                $wallet->transactions()->create([
                    'amount' => $validated['amount'], // Just the base amount
                    'type' => 'debit',
                    'currency' => $wallet->currency,
                    'description' => 'Ad Account Deposit - ' . $transaction->reference,
                    'reference' => $transaction->reference,
                    'status' => 'completed'
                ]);

                // Create a wallet transaction for VAT
                $wallet->transactions()->create([
                    'amount' => $fees['vat'],
                    'type' => 'vat',
                    'currency' => $wallet->currency,
                    'description' => 'VAT for Ad Account Deposit - ' . $transaction->reference,
                    'reference' => $transaction->reference . '-VAT',
                    'status' => 'completed'
                ]);

                // Create a wallet transaction for service fee
                $wallet->transactions()->create([
                    'amount' => $fees['service_fee'],
                    'type' => 'service_charge',
                    'currency' => $wallet->currency,
                    'description' => 'Service Fee for Ad Account Deposit - ' . $transaction->reference,
                    'reference' => $transaction->reference . '-FEE',
                    'status' => 'completed'
                ]);

                // For Meta ad accounts, update spend cap
                if ($adAccount->provider === 'meta' && $adAccount->provider_id) {
                    try {
                        $metaService = new MetaAdAccountService($businessManager->portfolio_id, $businessManager->token);
                        $metaService->fundAccount($adAccount->provider_id, $validated['amount']);
                    } catch (\Exception $e) {
                        Log::error('Meta API Error during deposit:', [
                            'message' => $e->getMessage(),
                            'ad_account_id' => $adAccount->id
                        ]);
                        throw $e; // Re-throw to rollback transaction
                    }
                }

                // Mark transaction as completed
                $transaction->update(['status' => 'completed']);
            });

            try {
                // After successful deposit
                $user = auth()->user();
                $user->notify(new AdAccountNotification([
                    'subject' => 'Ad Account Deposit',
                    'message' => "You have deposited {$validated['amount']} {$adAccount->currency} to ad account {$adAccount->name}",
                    'type' => 'ad_account_deposit',
                    'amount' => $validated['amount'],
                    'currency' => $adAccount->currency,
                    'ad_account_id' => $adAccount->id
                ]));
            } catch (\Exception $e) {
                Log::error('Ad account deposit notification error: ' . $e->getMessage());
            }

            return redirect()->back()
                ->with('success', 'Funds deposited successfully');
        } catch (\Exception $e) {
            Log::error('Ad Account Deposit Error:', [
                'message' => $e->getMessage(),
                'ad_account_id' => $adAccount->id
            ]);

            return redirect()->back()
                ->with('error', 'Error processing deposit: ' . $e->getMessage());
        }
    }

    public function withdraw(Request $request, AdAccount $adAccount)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:1',
                'wallet_id' => 'required|exists:wallets,id',
            ]);

            $businessManager = BusinessManager::find($adAccount->provider_bm_id);

            // Check if account is approved
            if ($adAccount->status !== AdAccount::STATUS_APPROVED) {
                return redirect()->back()
                    ->with('error', 'Ad account must be approved to withdraw funds');
            }

            $wallet = Wallet::findOrFail($validated['wallet_id']);

            // Check if currencies match
            if ($wallet->currency !== $adAccount->currency) {
                return redirect()->back()
                    ->with('error', 'Wallet currency must match ad account currency');
            }

            // For Meta ad accounts, check if account has provider_id
            if ($adAccount->type === 'meta') {
                if (!$adAccount->provider_id) {
                    return redirect()->back()
                        ->with('error', 'Meta ad account must be linked to withdraw funds');
                }

                // Check available funds in Meta account
                try {
                    $metaService = new MetaAdAccountService($businessManager->portfolio_id, $businessManager->token);
                    $account = $metaService->getAdAccount($adAccount->provider_id);
                    $spendCap = $account['spend_cap'] ?? 0;
                    $amountSpent = $account['amount_spent'] ?? 0;
                    $availableToWithdraw = $spendCap - $amountSpent;

                    if ($validated['amount'] > $availableToWithdraw) {
                        return redirect()->back()
                            ->with('error', 'Insufficient funds available in Meta ad account');
                    }
                } catch (\Exception $e) {
                    Log::error('Meta API Error checking withdrawal availability:', [
                        'message' => $e->getMessage(),
                        'ad_account_id' => $adAccount->id
                    ]);
                    return redirect()->back()
                        ->with('error', 'Error checking Meta ad account balance: ' . $e->getMessage());
                }
            }

            // Calculate fees
            $fees = $adAccount->calculateFees($validated['amount']);
            $totalAmount = $fees['total_amount'];

            // Check if ad account has sufficient balance
            if ($adAccount->getBalance() < $validated['amount']) {
                return redirect()->back()
                    ->with('error', 'Insufficient ad account balance.');
            }

            // Create transaction record
            $transaction = new AdAccountTransaction([
                'ad_account_id' => $adAccount->id,
                'wallet_id' => $wallet->id,
                'amount' => $validated['amount'],
                'type' => 'withdrawal',
                'vat' => $fees['vat'],
                'service_fee' => $fees['service_fee'],
                'total_amount' => $totalAmount,
                'reference' => 'AD-' . Str::random(20),
                'description' => 'Withdrawal from ad account'
            ]);

            DB::transaction(function () use ($wallet, $transaction, $totalAmount, $adAccount, $validated, $businessManager) {
                // Save transaction
                $transaction->save();

                // Create a wallet transaction to add the amount (only base amount, not fees)
                $wallet->transactions()->create([
                    'amount' => $totalAmount, // Positive amount for credit
                    'type' => 'credit',
                    'currency' => $wallet->currency,
                    'description' => 'Ad Account Withdrawal - ' . $transaction->reference,
                    'reference' => $transaction->reference,
                    'status' => 'completed'
                ]);

                // For Meta ad accounts, update spend cap
                if ($adAccount->provider === 'meta' && $adAccount->provider_id) {
                    try {
                        $metaService = new MetaAdAccountService($businessManager->portfolio_id, $businessManager->token);
                        $metaService->withdrawFunds($adAccount->provider_id, $validated['amount']);
                    } catch (\Exception $e) {
                        Log::error('Meta API Error during withdrawal:', [
                            'message' => $e->getMessage(),
                            'ad_account_id' => $adAccount->id
                        ]);
                        throw $e; // Re-throw to rollback transaction
                    }
                }

                // Mark transaction as completed
                $transaction->update(['status' => 'completed']);
            });

            try {
                // After successful withdrawal
                $user = auth()->user();
                $user->notify(new AdAccountNotification([
                    'subject' => 'Ad Account Withdrawal',
                    'message' => "You have withdrawn {$validated['amount']} {$adAccount->currency} from ad account {$adAccount->name}",
                    'type' => 'ad_account_withdrawal',
                    'amount' => $validated['amount'],
                    'currency' => $adAccount->currency,
                    'ad_account_id' => $adAccount->id
                ]));
            } catch (\Exception $e) {
                Log::error('Ad account withdrawal notification error: ' . $e->getMessage());
            }

            return redirect()->back()
                ->with('success', 'Funds withdrawn successfully');
        } catch (\Exception $e) {
            Log::error('Ad Account Withdrawal Error:', [
                'message' => $e->getMessage(),
                'ad_account_id' => $adAccount->id
            ]);

            return redirect()->back()
                ->with('error', 'Error processing withdrawal: ' . $e->getMessage());
        }
    }

    public function show(AdAccount $adAccount)
    {
        $user = auth()->user();
        $currentOrganizationId = $user->settings?->current_organization_id;

        // Ensure the ad account belongs to the current organization
        if ($adAccount->organization_id !== $currentOrganizationId) {
            abort(403, 'You do not have access to this ad account.');
        }

        // Get organization's wallets with their balances
        $wallets = $adAccount->organization->wallets()->with('transactions')->get();

        // Calculate balance for each wallet if needed
        $wallets->each(function ($wallet) {
            $wallet->calculated_balance = $wallet->getBalance();
        });

        $providerInfo = ["_provider" => null, "_meta_ad_account" => null];
        if (
            $adAccount->provider == "meta"
            && !empty($adAccount->provider_bm_id)
            && !empty($adAccount->provider_id)
        ) {
            $providerInfo["_provider"] = "meta";
            $businessManager = BusinessManager::find($adAccount->provider_bm_id);
            $adAccountService = new MetaAdAccountService($businessManager->portfolio_id, $businessManager->token);
            $metaAdAccount = $adAccountService->getAdAccount($adAccount->provider_id);
            $providerInfo["_meta_ad_account"] = $metaAdAccount;
        }

        return view('dashboard.adaccounts.show', compact('adAccount', 'wallets', 'providerInfo'));
    }
}
