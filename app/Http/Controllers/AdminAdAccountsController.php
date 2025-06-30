<?php

namespace App\Http\Controllers;

use App\Models\AdAccount;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FilteredAdAccountsExport;
use Illuminate\Support\Facades\Log;
use App\Models\Wallet;
use App\Models\AdAccountTransaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Notifications\AdAccountNotification;
use App\Models\BusinessManager;
use App\Services\Meta\AdAccountService as MetaAdAccountService;

class AdminAdAccountsController extends Controller
{
    public function index(Request $request)
    {
        $query = AdAccount::with(['user', 'organization']);

        // Filter by name if provided
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $adAccounts = $query->latest()->paginate(15);

        // Debug logging
        Log::info('Ad Accounts Query Results:', [
            'count' => $adAccounts->count(),
            'filters' => [
                'name' => $request->input('name'),
                'status' => $request->input('status')
            ]
        ]);

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
                session()->flash('info', $message);
                Log::info('Setting flash message:', ['message' => $message]);
            } else {
                // If no filters were applied
                session()->flash('info', 'No ad accounts found.');
                Log::info('Setting flash message: No ad accounts found.');
            }
        }

        return view('admin-dashboard.adaccounts.index', compact('adAccounts'));
    }

    public function edit(AdAccount $adAccount)
    {
        return view('admin-dashboard.adaccounts.edit', compact('adAccount'));
    }

    public function update(Request $request, AdAccount $adAccount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:meta,google,tiktok',
            'status' => 'required|in:processing,pending,approved,banned,deleted,rejected',
            'provider' => 'nullable|string',
            'provider_id' => 'nullable|string',
            'business_manager_id' => 'nullable|string|max:35',
            'landing_page' => 'nullable|url',
        ]);

        $adAccount->update($validated);

        return redirect()
            ->route('admin.adaccounts.index')
            ->with('success', 'Ad Account updated successfully');
    }

    public function destroy(AdAccount $adAccount)
    {
        $adAccount->delete();
        return redirect()
            ->back()
            ->with('success', 'Ad Account deleted successfully');
    }

    public function exportFilteredAccounts(Request $request)
    {
        $query = AdAccount::with(['user', 'organization']);

        // Apply the same filters as the index method
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $count = $query->count();

        if ($count === 0) {
            return redirect()->route('admin.adaccounts.index')
                ->with('warning', 'No accounts found to export.');
        }

        return Excel::download(
            new FilteredAdAccountsExport($query),
            'ad-accounts-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function linkMeta(Request $request)
    {
        $validated = $request->validate([
            'ad_account_id' => 'required|exists:ad_accounts,id',
            'meta_account_id' => 'required|string'
        ]);

        try {
            $adAccount = AdAccount::findOrFail($validated['ad_account_id']);

            $adAccount->update([
                'provider' => 'meta',
                'provider_id' => $validated['meta_account_id']
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to link account'], 500);
        }
    }

    public function unlinkMeta(Request $request)
    {
        $validated = $request->validate([
            'ad_account_id' => 'required|exists:ad_accounts,id'
        ]);

        try {
            $adAccount = AdAccount::findOrFail($validated['ad_account_id']);

            $adAccount->update([
                'provider' => null,
                'provider_id' => null,
                'provider_account_name' => null,
                'provider_bm_id' => null,
                'status' => AdAccount::STATUS_PENDING
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to unlink account'], 500);
        }
    }

    public function fund(Request $request, AdAccount $adAccount)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'wallet_id' => 'required|exists:wallets,id',
        ]);

        $wallet = Wallet::findOrFail($validated['wallet_id']);

        // Check if currencies match
        if ($wallet->currency !== $adAccount->currency) {
            return back()->with('error', 'Wallet currency must match ad account currency');
        }

        // Check if wallet has sufficient balance
        if ($wallet->getBalance() < $validated['amount']) {
            return back()->with('error', 'Insufficient wallet balance');
        }

        // Calculate fees
        $fees = $adAccount->calculateFees($validated['amount']);
        $totalAmount = $fees['total_amount'];

        try {
            DB::transaction(function () use ($adAccount, $wallet, $validated, $fees, $totalAmount) {
                // Create ad account transaction
                AdAccountTransaction::create([
                    'ad_account_id' => $adAccount->id,
                    'wallet_id' => $wallet->id,
                    'amount' => $validated['amount'],
                    'type' => 'deposit',
                    'vat' => $fees['vat'],
                    'service_fee' => $fees['service_fee'],
                    'total_amount' => $totalAmount,
                    'reference' => 'ADMIN-' . Str::random(20),
                    'status' => 'completed',
                    'description' => 'Admin deposit from wallet'
                ]);

                // Create wallet transaction
                $wallet->transactions()->create([
                    'amount' => $totalAmount,
                    'currency' => $wallet->currency,
                    'type' => 'debit',
                    'description' => 'Admin deposit to ad account',
                    'reference' => 'ADMIN-' . Str::random(20),
                    'status' => 'completed',
                    'rate' => 1,
                    'source_currency' => $wallet->currency
                ]);
            });

            try {
                // Notify the user who owns the ad account
                $adAccount->user->notify(new AdAccountNotification([
                    'subject' => 'Ad Account Funded',
                    'message' => "Your ad account {$adAccount->name} has been funded with {$validated['amount']} {$adAccount->currency} by admin",
                    'type' => 'ad_account_funded',
                    'amount' => $validated['amount'],
                    'currency' => $adAccount->currency,
                    'ad_account_id' => $adAccount->id
                ]));
            } catch (\Exception $e) {
                Log::error('Ad account funding notification error: ' . $e->getMessage());
            }

            // Notify organization owner if different from ad account owner
            $organizationOwner = $adAccount->organization->users()
                ->wherePivot('role', 'owner')
                ->first();

            if ($organizationOwner && $organizationOwner->id !== $adAccount->user_id) {
                try {
                    $organizationOwner->notify(new AdAccountNotification([
                        'subject' => 'Ad Account Funded',
                        'message' => "Ad account {$adAccount->name} has been funded with {$validated['amount']} {$adAccount->currency} by admin",
                        'type' => 'ad_account_funded',
                        'amount' => $validated['amount'],
                        'currency' => $adAccount->currency,
                        'ad_account_id' => $adAccount->id
                    ]));
                } catch (\Exception $e) {
                    Log::error('Ad account funding notification error: ' . $e->getMessage());
                }
            }

            return back()->with('success', 'Ad account funded successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fund ad account: ' . $e->getMessage());
        }
    }

    public function show(AdAccount $adAccount)
    {
        // Load the organization with its wallets
        $adAccount->load('organization.wallets');
        $wallets = $adAccount->organization->wallets;
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

        // Return view with adaccounts layout
        return view('admin-dashboard.adaccounts.show', [
            'adAccount' => $adAccount,
            'providerInfo' => $providerInfo,
            'wallets' => $wallets,
            'page' => 'organizations'
        ]);
    }

    public function withdraw(Request $request, AdAccount $adAccount)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'wallet_id' => 'required|exists:wallets,id',
        ]);

        $wallet = Wallet::findOrFail($validated['wallet_id']);

        // Check if currencies match
        if ($wallet->currency !== $adAccount->currency) {
            return back()->with('error', 'Wallet currency must match ad account currency');
        }

        // Calculate fees
        $fees = $adAccount->calculateFees($validated['amount']);
        $totalAmount = $fees['total_amount'];

        // Check if ad account has sufficient balance
        if ($adAccount->getBalance() < $totalAmount) {
            return back()->with('error', 'Insufficient ad account balance');
        }


        try {
            DB::transaction(function () use ($adAccount, $wallet, $fees, $totalAmount) {
                // Create ad account transaction
                AdAccountTransaction::create([
                    'ad_account_id' => $adAccount->id,
                    'wallet_id' => $wallet->id,
                    'amount' => $totalAmount,
                    'type' => 'withdrawal',
                    'vat' => $fees['vat'],
                    'service_fee' => $fees['service_fee'],
                    'total_amount' => $totalAmount,
                    'reference' => 'ADMIN-' . Str::random(20),
                    'status' => 'completed',
                    'description' => 'Admin withdrawal to wallet'
                ]);

                // Create wallet transaction
                $wallet->transactions()->create([
                    'amount' => $totalAmount, // Credit the base amount to wallet
                    'currency' => $wallet->currency,
                    'type' => 'credit',
                    'description' => 'Admin withdrawal from ad account',
                    'reference' => 'ADMIN-' . Str::random(20),
                    'status' => 'completed',
                    'rate' => 1,
                    'source_currency' => $wallet->currency
                ]);
            });

            try {
                // Notify the user who owns the ad account
                $adAccount->user->notify(new AdAccountNotification([
                    'subject' => 'Ad Account Withdrawal',
                    'message' => "Amount {$validated['amount']} {$adAccount->currency} has been withdrawn from your ad account {$adAccount->name} by admin",
                    'type' => 'ad_account_withdrawal',
                    'amount' => $validated['amount'],
                    'currency' => $adAccount->currency,
                    'ad_account_id' => $adAccount->id
                ]));
            } catch (\Exception $e) {
                Log::error('Ad account withdrawal notification error: ' . $e->getMessage());
            }

            // Notify organization owner if different from ad account owner
            $organizationOwner = $adAccount->organization->users()
                ->wherePivot('role', 'owner')
                ->first();

            if ($organizationOwner && $organizationOwner->id !== $adAccount->user_id) {
                try {
                    $organizationOwner->notify(new AdAccountNotification([
                        'subject' => 'Ad Account Withdrawal',
                        'message' => "Amount {$validated['amount']} {$adAccount->currency} has been withdrawn from ad account {$adAccount->name} by admin",
                        'type' => 'ad_account_withdrawal',
                        'amount' => $validated['amount'],
                        'currency' => $adAccount->currency,
                        'ad_account_id' => $adAccount->id
                    ]));
                } catch (\Exception $e) {
                    Log::error('Ad account withdrawal notification error: ' . $e->getMessage());
                }
            }

            return back()->with('success', 'Funds withdrawn successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to withdraw funds: ' . $e->getMessage());
        }
    }
}
