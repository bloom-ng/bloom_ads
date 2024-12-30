<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Services\FlutterwaveService;
use App\Services\PayPalService;
use Illuminate\Support\Facades\Auth;
use App\Services\PaystackService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\WalletNotification;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $currentOrgId = $user->settings->current_organization_id ?? null;

        if (!$currentOrgId) {
            return redirect()->route('settings.index')
                ->with('error', 'Please select a current organization first.');
        }

        $organization = Organization::with(['users' => function ($query) use ($user) {
            $query->where('users.id', $user->id);
        }])->findOrFail($currentOrgId);

        // Load wallets with calculated balances
        $wallets = Wallet::withBalances()
            ->where('organization_id', $organization->id)
            ->get();

        $organization->setRelation('wallets', $wallets);
        $userRole = $organization->users->first()->pivot->role;

        return view('dashboard.wallet.index', [
            'organization' => $organization,
            'userRole' => $userRole,
            'currentOrganization' => $organization
        ]);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'currency' => 'required|in:NGN,USD,GBP',
        ]);

        $organization = Organization::findOrFail($validated['organization_id']);

        // Check if user has permission
        $userRole = $organization->users()->where('user_id', auth()->id())->first()->pivot->role;
        if (!in_array($userRole, ['owner', 'admin', 'finance'])) {
            return back()->with('error', 'You do not have permission to create wallets.');
        }

        // Check if wallet already exists
        if ($organization->wallets()->where('currency', $validated['currency'])->exists()) {
            return back()->with('error', 'A wallet with this currency already exists.');
        }

        $organization->wallets()->create([
            'currency' => $validated['currency'],
            'balance' => 0
        ]);

        return back()->with('success', 'Wallet created successfully.');
    }

    public function fundWithFlutterwave(Request $request)
    {
        $validated = $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|in:NGN',
            'wallet_currency' => 'required|in:NGN,USD,GBP'
        ]);

        $wallet = Wallet::findOrFail($validated['wallet_id']);

        // Calculate converted amount if wallet currency is different
        $conversionRate = $wallet->getConversionRate($validated['currency']);
        $convertedAmount = $validated['amount'] * $conversionRate;

        // Initialize Flutterwave payment
        $flutterwave = new FlutterwaveService();
        $user = auth()->user();

        $response = $flutterwave->initializePayment([
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'reference' => 'WALLET-' . uniqid(),
            'callback_url' => route('wallet.fund.flutterwave.callback'),
            'customer_email' => $user->email,
            'customer_name' => $user->name,
            'organization_id' => $wallet->organization_id,
            'wallet_id' => $wallet->id,
            'converted_amount' => $convertedAmount,
            'wallet_currency' => $validated['wallet_currency']
        ]);

        if (isset($response['data']['link'])) {
            return redirect($response['data']['link']);
        }

        return back()->with('error', 'Unable to initialize payment. Please try again.');
    }

    public function fundWithPaystack(Request $request)
    {
        $validated = $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|in:NGN',
            'wallet_currency' => 'required|in:NGN,USD,GBP'
        ]);

        $wallet = Wallet::findOrFail($validated['wallet_id']);

        // Calculate converted amount if wallet currency is different
        $conversionRate = $wallet->getConversionRate($validated['currency']);
        $convertedAmount = $validated['amount'] * $conversionRate;

        // Initialize Paystack payment
        $paystack = new PaystackService();
        $user = auth()->user();

        $response = $paystack->initializePayment([
            'amount' => $validated['amount'], // Paystack expects amount in kobo
            'currency' => $validated['currency'],
            'reference' => 'WALLET-' . uniqid(),
            'callback_url' => route('wallet.fund.paystack.callback'),
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'organization_id' => $wallet->organization_id,
                'wallet_id' => $wallet->id,
                'converted_amount' => $convertedAmount,
                'wallet_currency' => $validated['wallet_currency']
            ]
        ]);

        if (isset($response['data']['authorization_url'])) {
            return redirect($response['data']['authorization_url']);
        }

        return back()->with('error', 'Unable to initialize payment. Please try again.');
    }

    public function fundWithPaypal(Request $request)
    {
        $validated = $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|in:USD,GBP'
        ]);

        $wallet = Wallet::findOrFail($validated['wallet_id']);

        // Initialize PayPal payment
        $paypal = new PayPalService();

        $response = $paypal->createOrder([
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'reference' => 'WALLET-' . uniqid(),
            'success_url' => route('wallet.fund.paypal.success'),
            'cancel_url' => route('wallet.fund.paypal.cancel'),
            'organization_id' => $wallet->organization_id,
            'wallet_id' => $wallet->id,
        ]);

        if (isset($response['links'])) {
            $approvalLink = collect($response['links'])->firstWhere('rel', 'approve');
            if ($approvalLink) {
                return redirect($approvalLink['href']);
            }
        }

        return back()->with('error', 'Unable to initialize payment. Please try again.');
    }

    public function handleFlutterwaveCallback(Request $request)
    {
        try {
            $transactionId = $request->query('transaction_id');
            $status = $request->query('status');

            if (!$transactionId || $status !== 'completed') {
                return redirect()->route('wallet.index')
                    ->with('error', 'Payment was not successful.');
            }

            $flutterwave = new FlutterwaveService();
            $response = $flutterwave->verifyTransaction($transactionId);

            if ($response['status'] === 'success' && $response['data']['status'] === 'successful') {
                $data = $response['data'];

                // Find the wallet
                $walletId = $data['meta']['wallet_id'] ?? null;
                $wallet = Wallet::findOrFail($walletId);

                // Get the conversion rate and calculate converted amount
                $rate = $wallet->getConversionRate('NGN');
                $convertedAmount = $data['amount'] * $rate;

                // Create transaction record with rate and source currency
                $transaction = $wallet->transactions()->create([
                    'amount' => $convertedAmount,
                    'currency' => $wallet->currency,
                    'type' => 'credit',
                    'description' => 'Wallet funding via Flutterwave',
                    'reference' => $data['tx_ref'],
                    'status' => 'completed',
                    'rate' => $rate,
                    'source_currency' => 'NGN'
                ]);

                // Update wallet balance with converted amount
                // $wallet->increment('balance', $convertedAmount);

                if ($transaction->status === 'successful') {
                    // After successful funding
                    auth()->user()->notify(new WalletNotification([
                        'subject' => 'Wallet Funded Successfully',
                        'message' => "Your wallet has been credited with {$convertedAmount}",
                        'type' => 'wallet_funded',
                        'amount' => $convertedAmount
                    ]));
                }

                return redirect()->route('wallet.index')
                    ->with('success', 'Payment successful! Your wallet has been credited.');
            }

            return redirect()->route('wallet.index')
                ->with('error', 'Payment verification failed.');
        } catch (\Exception $e) {
            Log::error('Flutterwave callback error: ' . $e->getMessage());
            return redirect()->route('wallet.index')
                ->with('error', 'An error occurred while processing your payment.');
        }
    }

    public function handlePaystackCallback(Request $request)
    {
        try {
            $reference = $request->query('reference');

            if (!$reference) {
                return redirect()->route('wallet.index')
                    ->with('error', 'Payment was not successful.');
            }

            $paystack = new PaystackService();
            $response = $paystack->verifyTransaction($reference);

            if ($response['status'] && $response['data']['status'] === 'success') {
                $data = $response['data'];

                // Get metadata from the transaction
                $metadata = $data['metadata'];
                $walletId = $metadata['wallet_id'] ?? null;
                $wallet = Wallet::findOrFail($walletId);

                // Calculate the converted amount
                // Amount from Paystack is in kobo, so divide by 100
                $ngnAmount = $data['amount'] / 100;
                $rate = $wallet->getConversionRate('NGN');
                $convertedAmount = $ngnAmount * $rate;

                // Create transaction record
                $transaction = $wallet->transactions()->create([
                    'amount' => $convertedAmount,
                    'currency' => $wallet->currency,
                    'type' => 'credit',
                    'description' => 'Wallet funding via Paystack',
                    'reference' => $data['reference'],
                    'status' => 'completed',
                    'rate' => $rate,
                    'source_currency' => 'NGN'
                ]);

                // Update wallet balance
                // $wallet->increment('balance', $convertedAmount);

                // After successful funding
                auth()->user()->notify(new WalletNotification([
                    'subject' => 'Wallet Funded Successfully',
                    'message' => "Your wallet has been credited with {$convertedAmount}",
                    'type' => 'wallet_funded',
                    'amount' => $convertedAmount
                ]));

                return redirect()->route('wallet.index')
                    ->with('success', 'Payment successful! Your wallet has been credited.');
            }

            return redirect()->route('wallet.index')
                ->with('error', 'Payment verification failed.');
        } catch (\Exception $e) {
            Log::error('Paystack callback error: ' . $e->getMessage());
            return redirect()->route('wallet.index')
                ->with('error', 'An error occurred while processing your payment.');
        }
    }

    public function handlePaypalSuccess(Request $request)
    {
        // Handle PayPal success
        // Verify transaction and update wallet balance
    }

    public function handlePaypalCancel(Request $request)
    {
        return redirect()->route('wallet.index')
            ->with('error', 'Payment was cancelled.');
    }

    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'source_wallet_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric|min:0.01',
            'destination_currency' => 'required|in:NGN,USD,GBP',
            'source_currency' => 'required|in:NGN,USD,GBP'
        ]);

        try {
            $sourceWallet = Wallet::findOrFail($validated['source_wallet_id']);

            // Check if user has access to this wallet
            $organization = $sourceWallet->organization;
            if (!$organization->users()->where('user_id', Auth::id())->exists()) {
                return back()->with('error', 'Unauthorized access to wallet.');
            }

            // Check sufficient balance using the new getBalance method
            if ($sourceWallet->getBalance() < $validated['amount']) {
                return back()->with('error', 'Insufficient balance.');
            }

            // Get or create destination wallet
            $destinationWallet = $organization->wallets()
                ->firstOrCreate(
                    ['currency' => $validated['destination_currency']],
                    ['balance' => 0]
                );

            // Calculate conversion rate and converted amount
            $rate = 1;
            if ($sourceWallet->currency === 'NGN') {
                if ($destinationWallet->currency === 'USD') {
                    $rate = 1 / Wallet::getRate('usd');
                } elseif ($destinationWallet->currency === 'GBP') {
                    $rate = 1 / Wallet::getRate('gbp');
                }
            } elseif ($destinationWallet->currency === 'NGN') {
                if ($sourceWallet->currency === 'USD') {
                    $rate = Wallet::getRate('usd');
                } elseif ($sourceWallet->currency === 'GBP') {
                    $rate = Wallet::getRate('gbp');
                }
            } elseif ($sourceWallet->currency === 'USD' && $destinationWallet->currency === 'GBP') {
                $rate = Wallet::getRate('usd') / Wallet::getRate('gbp');
            } elseif ($sourceWallet->currency === 'GBP' && $destinationWallet->currency === 'USD') {
                $rate = Wallet::getRate('gbp') / Wallet::getRate('usd');
            }

            $convertedAmount = $validated['amount'] * $rate;

            DB::transaction(function () use ($sourceWallet, $destinationWallet, $validated, $convertedAmount, $rate) {
                // Deduct from source wallet
                $sourceWallet->transactions()->create([
                    'amount' => $validated['amount'],
                    'currency' => $sourceWallet->currency,
                    'type' => 'debit',
                    'description' => "Transfer to {$destinationWallet->currency} wallet",
                    'reference' => 'TRF-' . uniqid(),
                    'status' => 'completed',
                    'rate' => $rate,
                    'source_currency' => $sourceWallet->currency
                ]);
                // $sourceWallet->decrement('balance', $validated['amount']);

                // Add to destination wallet
                $destinationWallet->transactions()->create([
                    'amount' => $convertedAmount,
                    'currency' => $destinationWallet->currency,
                    'type' => 'credit',
                    'description' => "Transfer from {$sourceWallet->currency} wallet",
                    'reference' => 'TRF-' . uniqid(),
                    'status' => 'completed',
                    'rate' => 1 / $rate, // Store inverse rate for the receiving transaction
                    'source_currency' => $sourceWallet->currency
                ]);
                // $destinationWallet->increment('balance', $convertedAmount);
            });

            try {
                auth()->user()->notify(new WalletNotification([
                    'subject' => 'Wallet Transfer',
                    'message' => "You have transferred {$validated['amount']} from your wallet",
                    'type' => 'wallet_transfer',
                    'amount' => $validated['amount']
                ]));
            } catch (\Exception $e) {
                Log::error('Wallet transfer notification error: ' . $e->getMessage());
            }

            return back()->with('success', 'Transfer completed successfully.');
        } catch (\Exception $e) {
            Log::error('Wallet transfer error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred during the transfer.');
        }
    }
}
