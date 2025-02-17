<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
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
use App\Models\WalletTransaction;
use PDF;
use Illuminate\Support\Str;

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

        // Get all wallet IDs for the organization
        $walletIds = $wallets->pluck('id');

        // Load transactions for all wallets, paginated
        $transactions = WalletTransaction::whereIn('wallet_id', $walletIds)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $organization->setRelation('wallets', $wallets);
        $userRole = $organization->users->first()->pivot->role;

        return view('dashboard.wallet.index', [
            'organization' => $organization,
            'userRole' => $userRole,
            'currentOrganization' => $organization,
            'transactions' => $transactions
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
            'amount' => 'required|numeric|min:100000',
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

                if ($transaction->status === 'completed') {
                    // After successful funding
                    try {
                        $user = auth()->user();
                        $amount = number_format($convertedAmount, 2);

                        $user->notify(new WalletNotification([
                            'subject' => 'Wallet Funded Successfully',
                            'message' => "Your wallet has been funded with {$wallet->currency} {$amount} via Flutterwave",
                            'type' => 'wallet_funded',
                            'amount' => $convertedAmount,
                            'currency' => $wallet->currency,
                            'wallet_id' => $wallet->id
                        ]));
                    } catch (\Exception $e) {
                        Log::error('Failed to send wallet funding notification: ' . $e->getMessage());
                    }
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
                try {
                    $user = auth()->user();
                    $amount = number_format($convertedAmount, 2);

                    $user->notify(new WalletNotification([
                        'subject' => 'Wallet Funded Successfully',
                        'message' => "Your wallet has been funded with {$wallet->currency} {$amount} via Paystack",
                        'type' => 'wallet_funded',
                        'amount' => $convertedAmount,
                        'currency' => $wallet->currency,
                        'wallet_id' => $wallet->id
                    ]));
                } catch (\Exception $e) {
                    Log::error('Failed to send wallet funding notification: ' . $e->getMessage());
                }

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
            $convertedAmount = $validated['amount'];

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
                    'source_currency' => $destinationWallet->currency
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
                // Notify source wallet owner
                auth()->user()->notify(new WalletNotification([
                    'subject' => 'Wallet Transfer',
                    'message' => "You have transferred {$validated['amount']} {$sourceWallet->currency} from your wallet to your {$destinationWallet->currency} wallet",
                    'type' => 'wallet_transfer_debit',
                    'amount' => $validated['amount'],
                    'currency' => $sourceWallet->currency,
                    'converted_amount' => $convertedAmount,
                    'converted_currency' => $destinationWallet->currency
                ]));

                // Notify destination wallet owner (if different)
                if ($destinationWallet->organization_id !== $sourceWallet->organization_id) {
                    $destinationWallet->organization->users()
                        ->wherePivot('role', 'owner')
                        ->first()
                        ->notify(new WalletNotification([
                            'subject' => 'Wallet Transfer Received',
                            'message' => "Your wallet has been credited with {$convertedAmount} {$destinationWallet->currency}",
                            'type' => 'wallet_transfer_credit',
                            'amount' => $convertedAmount,
                            'currency' => $destinationWallet->currency
                        ]));
                }
            } catch (\Exception $e) {
                Log::error('Wallet transfer notification error: ' . $e->getMessage());
            }

            return back()->with('success', 'Transfer completed successfully.');
        } catch (\Exception $e) {
            Log::error('Wallet transfer error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred during the transfer.');
        }
    }

    public function viewTransactionReceipt(WalletTransaction $transaction)
    {
        // Ensure user has access to this transaction
        $user = Auth::user();
        $currentOrgId = $user->settings->current_organization_id;

        if ($transaction->wallet->organization_id !== $currentOrgId) {
            abort(403);
        }

        return view('dashboard.wallet.receipt', [
            'transaction' => $transaction,
            'organization' => $transaction->wallet->organization
        ]);
    }

    public function downloadTransactionReceipt(WalletTransaction $transaction)
    {
        // Ensure user has access to this transaction
        $user = Auth::user();
        $currentOrgId = $user->settings->current_organization_id;

        if ($transaction->wallet->organization_id !== $currentOrgId) {
            abort(403);
        }

        // Check if this is an ad account transaction by looking for AD- prefix in reference
        $adTransaction = null;
        $vat = 0;
        $serviceFee = 0;
        $subTotal = $transaction->amount;

        if (Str::startsWith($transaction->reference, 'AD-')) {
            // Try to find matching ad account transaction
            $adTransaction = DB::table('ad_account_transactions')
                ->where('reference', $transaction->reference)
                ->first();

            if ($adTransaction) {
                // If found, calculate the original amount before fees
                $vat = $adTransaction->vat ?? 0;
                $serviceFee = $adTransaction->service_fee ?? 0;
                $subTotal = $transaction->amount - ($vat + $serviceFee);
            }
        }

        return view('dashboard.wallet.receipt-pdf', [
            'transaction' => $transaction,
            'organization' => $transaction->wallet->organization,
            'subTotal' => $subTotal,
            'vat' => $vat,
            'serviceFee' => $serviceFee
        ]);
    }

    public function generateInvoice(Request $request)
    {
        $validated = $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric|min:500001',
            'currency' => 'required|in:NGN,USD,GBP'
        ]);

        $wallet = Wallet::findOrFail($validated['wallet_id']);
        $organization = Organization::findOrFail($wallet->organization_id);

        // Get rates from admin settings
        $vatRate = AdminSetting::where('key', 'ad_account_vat')->first()->value ?? 7.5;
        $serviceFeeRate = AdminSetting::where('key', 'ad_account_service_charge')->first()->value ?? 1.5;

        // Get bank details from admin settings
        $accountName = AdminSetting::where('key', 'invoice_account_name')->first()->value ?? 'Company Name';
        $bankName = AdminSetting::where('key', 'invoice_bank_name')->first()->value ?? 'Bank Name';
        $accountNumber = AdminSetting::where('key', 'invoice_account_number')->first()->value ?? 'Account Number';

        // Calculate fees
        $vat = ($validated['amount'] * $vatRate) / 100;
        $serviceFee = ($validated['amount'] * $serviceFeeRate) / 100;
        $total = $validated['amount'] + $vat + $serviceFee;

        // Generate unique invoice number
        $invoiceNumber = 'INV-' . strtoupper(Str::random(8));

        return view('dashboard.wallet.invoice', [
            'organization' => $organization,
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'vatRate' => $vatRate,
            'serviceFeeRate' => $serviceFeeRate,
            'vat' => $vat,
            'serviceFee' => $serviceFee,
            'total' => $total,
            'invoiceNumber' => $invoiceNumber,
            'accountName' => $accountName,
            'bankName' => $bankName,
            'accountNumber' => $accountNumber
        ]);
    }

    public function downloadInvoice(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'currency' => 'required|in:NGN,USD,GBP'
        ]);

        // Get current organization
        $organization = Organization::findOrFail(Auth::user()->settings->current_organization_id);

        // Get rates from admin settings
        $vatRate = AdminSetting::where('key', 'ad_account_vat')->first()->value ?? 7.5;
        $serviceFeeRate = AdminSetting::where('key', 'ad_account_service_charge')->first()->value ?? 1.5;

        // Get bank details from admin settings
        $accountName = AdminSetting::where('key', 'invoice_account_name')->first()->value ?? 'Company Name';
        $bankName = AdminSetting::where('key', 'invoice_bank_name')->first()->value ?? 'Bank Name';
        $accountNumber = AdminSetting::where('key', 'invoice_account_number')->first()->value ?? 'Account Number';

        // Calculate fees
        $vat = ($validated['amount'] * $vatRate) / 100;
        $serviceFee = ($validated['amount'] * $serviceFeeRate) / 100;
        $total = $validated['amount'] + $vat + $serviceFee;

        // Return view instead of PDF download
        return view('dashboard.wallet.invoice-pdf', [
            'organization' => $organization,
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'vatRate' => $vatRate,
            'serviceFeeRate' => $serviceFeeRate,
            'vat' => $vat,
            'serviceFee' => $serviceFee,
            'total' => $total,
            'invoiceNumber' => 'INV-' . strtoupper(Str::random(8)),
            'accountName' => $accountName,
            'bankName' => $bankName,
            'accountNumber' => $accountNumber,
            'description' => 'Wallet Funding'
        ]);
    }

    public function getBanks(FlutterwaveService $flutterwaveService)
    {
        try {
            $response = $flutterwaveService->getBanks();
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch banks'], 500);
        }
    }

    public function withdraw(Request $request)
    {
        $validated = $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric|min:1',
            'account_number' => 'required|string',
            'bank_code' => 'required|string',
        ]);

        $wallet = Wallet::findOrFail($validated['wallet_id']);

        // Verify bank account first
        $flutterwaveService = new FlutterwaveService();
        $accountVerification = $flutterwaveService->verifyBankAccount(
            $validated['account_number'],
            $validated['bank_code']
        );

        $accountName = $accountVerification['data']['account_name'];

        // Calculate fees
        $fees = $wallet->calculateWithdrawalFees($validated['amount']);
        $totalAmount = $fees['total_amount'];

        // Check if user has permission
        $organization = $wallet->organization;
        $userRole = $organization->users()->where('user_id', auth()->id())->first()->pivot->role;
        if (!in_array($userRole, ['owner'])) {
            throw new \Exception('You do not have permission to withdraw funds.');
        }
        // Check if wallet has sufficient balance
        if ($wallet->getBalance() < $totalAmount) {
            return back()->with('error', 'Insufficient wallet balance including fees');
        }

        if ($accountVerification['status'] !== 'success') {
            throw new \Exception($accountVerification['message']);
        }

        $reference = 'WD-' . Str::random(20);

        try {
            DB::transaction(function () use ($wallet, $validated, $accountName, $reference, $fees, $flutterwaveService) {
                // Create main withdrawal transaction
                $transaction = $wallet->transactions()->create([
                    'amount' => $validated['amount'],
                    'currency' => $wallet->currency,
                    'type' => 'debit',
                    'status' => 'pending',
                    'description' => "Withdrawal to {$accountName}",
                    'reference' => $reference,
                    'rate' => 1.0,
                    'source_currency' => $wallet->currency,
                ]);

                // Create processing fee transaction
                $processing = $wallet->transactions()->create([
                    'amount' => $fees['processing_fee'],
                    'currency' => $wallet->currency,
                    'type' => 'processing_fee',
                    'status' => 'completed',
                    'description' => "Processing fee for withdrawal - {$reference}",
                    'reference' => $reference . '-PROC',
                    'rate' => 1.0,
                    'source_currency' => $wallet->currency,
                ]);

                // Create VAT transaction
                $vat = $wallet->transactions()->create([
                    'amount' => $fees['vat'],
                    'currency' => $wallet->currency,
                    'type' => 'vat',
                    'status' => 'completed',
                    'description' => "VAT for withdrawal processing fee - {$reference}",
                    'reference' => $reference . '-VAT',
                    'rate' => 1.0,
                    'source_currency' => $wallet->currency,
                ]);

                // Initiate transfer
                $data = [
                    'account_number' => $validated['account_number'],
                    'bank_code' => $validated['bank_code'],
                    'amount' => $validated['amount'],
                    'currency' => $wallet->currency,
                    'reference' => $reference,
                    'narration' => "Billing Withdrawal to {$accountName}",
                    'callback_url' => route('wallet.withdrawal.callback'),
                    'debit_currency' => 'NGN'
                ];

                $response = $flutterwaveService->initiateTransfer($data);

                if ($response['status'] == 'success' && $response['data']['status'] == "NEW") {
                    // Update transaction with transfer details
                    $transaction->update([
                        'status' => 'pending',
                        // 'provider_reference' => $response['data']['id'] ?? null,
                        // 'meta' => array_merge($transaction->meta, [
                        //     'transfer_id' => $response['data']['id'] ?? null,
                        //     'transfer_status' => $response['data']['status'] ?? 'pending',
                        //     'bank_name' => $response['data']['bank_name'] ?? null,
                        //     'fee' => $response['data']['fee'] ?? 0
                        // ])
                    ]);
                    $processing->update([
                        'status' => 'pending',
                    ]);
                    $vat->update([
                        'status' => 'pending',
                    ]);
                } elseif ($response['status'] == 'success' && $response['data']['status'] == "SUCCESSFUL") {
                    $transaction->update([
                        'status' => 'completed',
                    ]);
                    $processing->update([
                        'status' => 'completed',
                    ]);
                    $vat->update([
                        'status' => 'completed',
                    ]);
                } elseif ($response['status'] == 'success' && $response['data']['status'] == "PENDING") {
                    $transaction->update([
                        'status' => 'pending',
                    ]);
                    $processing->update([
                        'status' => 'pending',
                    ]);
                    $vat->update([
                        'status' => 'pending',
                    ]);
                } elseif ($response['status'] == 'success' && $response['data']['status'] == "FAILED") {
                    $transaction->update([
                        'status' => 'failed',
                    ]);
                    $processing->update([
                        'status' => 'failed',
                    ]);
                    $vat->update([
                        'status' => 'failed',
                    ]);
                }
            });

            return back()->with('success', 'Withdrawal initiated successfully');
        } catch (\Exception $e) {
            Log::error('Withdrawal Error:', [
                'message' => $e->getMessage(),
                'wallet_id' => $wallet->id
            ]);

            return back()->with('error', 'Error processing withdrawal: ' . $e->getMessage());
        }
    }

    public function withdrawalCallback(Request $request)
    {
        Log::info('Withdrawal callback received:', $request->all());

        // Verify webhook signature
        $flutterwaveService = app(FlutterwaveService::class);
        $signature = $request->header('verif-hash');
        if (!$signature || !$flutterwaveService->verifyWebhookSignature($signature, $request->getContent())) {
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }

        $event = $request->input('event');
        if ($event === 'transfer.completed') {
            $data = $request->input('data');
            $reference = $data['reference'];

            $transaction = WalletTransaction::where('reference', $reference)->first();
            $processingFeeTransaction = WalletTransaction::where('reference', $reference . '-PROC')->where('type', 'processing_fee')->first();
            $vatTransaction = WalletTransaction::where('reference', $reference . '-VAT')->where('type', 'vat')->first();
            if (!$transaction && !$processingFeeTransaction && !$vatTransaction) {
                Log::error('Transaction not found for reference: ' . $reference);
                return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
            }

            // Check if the transaction has been completed
            $transaction->refresh(); // Refresh the transaction to get the latest status
            if ($transaction->status === 'completed') {
                Log::info('Transaction already completed for reference: ' . $reference);
                return response()->json(['status' => 'error', 'message' => 'Transaction already completed'], 400);
            }

            // Update transaction status based on transfer status
            $status = strtoupper($data['status']);
            DB::transaction(function () use ($transaction, $status, $reference, $processingFeeTransaction, $vatTransaction) {
                $transaction->update([
                    'status' => $status === 'SUCCESSFUL' ? 'completed' : ($status === 'FAILED' ? 'failed' : 'pending'),
                ]);


                $processingFeeTransaction->update([
                    'status' => $status === 'SUCCESSFUL' ? 'completed' : 'failed',
                ]);

                $vatTransaction->update([
                    'status' => $status === 'SUCCESSFUL' ? 'completed' : 'failed',
                ]);
            });

            // Notify user about the transfer status
            try {
                $user = $transaction->wallet->organization->users()->first();
                $amount = number_format(abs($transaction->amount), 2);

                $message = $status === 'SUCCESSFUL'
                    ? "Your withdrawal of {$transaction->currency} {$amount} has been completed successfully."
                    : "Your withdrawal of {$transaction->currency} {$amount} has failed.";

                $user->notify(new WalletNotification([
                    'subject' => 'Withdrawal ' . ucfirst(strtolower($status)),
                    'message' => $message,
                    'type' => $status === 'SUCCESSFUL' ? 'withdrawal_success' : 'withdrawal_failed'
                ]));
            } catch (\Exception $e) {
                Log::error('Failed to send withdrawal notification: ' . $e->getMessage());
            }

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'success', 'message' => 'Event not handled']);
    }

    public function verifyBankAccount(Request $request, FlutterwaveService $flutterwaveService)
    {
        $validated = $request->validate([
            'account_number' => 'required|string',
            'bank_code' => 'required|string'
        ]);

        try {
            $response = $flutterwaveService->verifyBankAccount(
                $validated['account_number'],
                $validated['bank_code']
            );

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to verify account'], 500);
        }
    }

    public function calculateWithdrawalFees(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'wallet_id' => 'required|exists:wallets,id'
        ]);

        $wallet = Wallet::findOrFail($validated['wallet_id']);
        $fees = $wallet->calculateWithdrawalFees($validated['amount']);

        return response()->json([
            'processing_fee' => number_format($fees['processing_fee'], 2),
            'vat' => number_format($fees['vat'], 2),
            'total_amount' => number_format($fees['total_amount'], 2),
            'amount' => number_format($validated['amount'], 2)
        ]);
    }
}
