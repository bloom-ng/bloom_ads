<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Services\FlutterwaveService;
use App\Services\PayPalService;
use Illuminate\Support\Facades\Auth;
use App\Services\PaystackService;

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

        $organization = Organization::with(['wallets', 'users' => function ($query) use ($user) {
            $query->where('users.id', $user->id);
        }])->findOrFail($currentOrgId);

        $userRole = $organization->users->first()->pivot->role;

        return view('dashboard.wallet.index', compact('organization', 'userRole'));
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
            'currency' => 'required|in:NGN'
        ]);

        $wallet = Wallet::findOrFail($validated['wallet_id']);

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
            'currency' => 'required|in:NGN'
        ]);

        $wallet = Wallet::findOrFail($validated['wallet_id']);

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
            // dd($request->query());
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

                // Create transaction record
                $transaction = $wallet->transactions()->create([
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                    'type' => 'credit',
                    'description' => 'Wallet funding via Flutterwave',
                    'reference' => $data['tx_ref'],
                    'status' => 'completed'
                ]);

                // Update wallet balance
                $wallet->increment('balance', $data['amount']);

                return redirect()->route('wallet.index')
                    ->with('success', 'Payment successful! Your wallet has been credited.');
            }

            return redirect()->route('wallet.index')
                ->with('error', 'Payment verification failed.');
        } catch (\Exception $e) {
            \Log::error('Flutterwave callback error: ' . $e->getMessage());
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
                    ->with('error', 'No reference provided.');
            }

            $paystack = new PaystackService();
            $response = $paystack->verifyTransaction($reference);

            if ($response['status'] && $response['data']['status'] === 'success') {
                $data = $response['data'];

                // Find the wallet
                $walletId = $data['metadata']['wallet_id'] ?? null;

                $wallet = Wallet::findOrFail($walletId);

                // Create transaction record
                $transaction = $wallet->transactions()->create([
                    'amount' => $data['amount'] / 100, // Convert from kobo to naira
                    'currency' => $data['currency'],
                    'type' => 'credit',
                    'description' => 'Wallet funding via Paystack',
                    'reference' => $data['reference'],
                    'status' => 'completed'
                ]);

                // Update wallet balance
                $wallet->increment('balance', $data['amount'] / 100);

                return redirect()->route('wallet.index')
                    ->with('success', 'Payment successful! Your wallet has been credited.');
            }

            return redirect()->route('wallet.index')
                ->with('error', 'Payment verification failed.');
        } catch (\Exception $e) {
            \Log::error('Paystack callback error: ' . $e->getMessage());
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
}
