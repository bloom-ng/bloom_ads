<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminWalletController extends Controller
{
    public function index()
    {
        $my_date = Carbon::now()->format('l, F j, Y');

        // Add pagination while maintaining the eager loading of relationships
        $wallets = Wallet::with(['organization', 'organization.users'])
            ->orderBy('created_at', 'desc')  // Sort by newest first
            ->paginate(10);  // 10 wallets per page

        return view('admin-dashboard.wallets.index', compact('wallets', 'my_date'));
    }

    public function credit(Request $request, Wallet $wallet)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($wallet, $validated) {
            $wallet->transactions()->create([
                'amount' => $validated['amount'],
                'currency' => $wallet->currency,
                'type' => 'credit',
                'description' => $validated['description'],
                'reference' => 'ADMIN-' . Str::random(20),
                'status' => 'completed',
                'rate' => 1,
                'source_currency' => $wallet->currency
            ]);
        });

        return back()->with('success', 'Wallet credited successfully');
    }

    public function debit(Request $request, Wallet $wallet)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        if ($wallet->getBalance() < $validated['amount']) {
            return back()->with('error', 'Insufficient wallet balance');
        }

        DB::transaction(function () use ($wallet, $validated) {
            $wallet->transactions()->create([
                'amount' => $validated['amount'],
                'currency' => $wallet->currency,
                'type' => 'debit',
                'description' => $validated['description'],
                'reference' => 'ADMIN-' . Str::random(20),
                'status' => 'completed',
                'rate' => 1,
                'source_currency' => $wallet->currency
            ]);
        });

        return back()->with('success', 'Wallet debited successfully');
    }

    public function transferFromAdAccount(Request $request, AdAccount $adAccount)
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

        // Check if ad account has sufficient balance
        if ($adAccount->getBalance() < $validated['amount']) {
            return back()->with('error', 'Insufficient ad account balance');
        }

        // Calculate fees
        $fees = $adAccount->calculateFees($validated['amount']);
        $totalAmount = $fees['total_amount'];

        DB::transaction(function () use ($adAccount, $wallet, $validated, $fees, $totalAmount) {
            // Create ad account transaction
            AdAccountTransaction::create([
                'ad_account_id' => $adAccount->id,
                'wallet_id' => $wallet->id,
                'amount' => $validated['amount'],
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
                'amount' => $validated['amount'],
                'currency' => $wallet->currency,
                'type' => 'credit',
                'description' => 'Admin transfer from ad account',
                'reference' => 'ADMIN-' . Str::random(20),
                'status' => 'completed',
                'rate' => 1,
                'source_currency' => $wallet->currency
            ]);
        });

        return back()->with('success', 'Funds transferred successfully');
    }
}
