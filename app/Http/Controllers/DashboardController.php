<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $currentOrgId = $user->settings->current_organization_id;

        // Get total organizations user belongs to
        $totalOrganizations = $user->organizations()->count();

        // Get total money spent and credited for current organization
        $totalSpent = 0;
        $totalCredited = 0;

        if ($currentOrgId) {
            // Get all wallets for current organization
            $wallets = Wallet::where('organization_id', $currentOrgId)->get();

            foreach ($wallets as $wallet) {
                // Get completed transactions
                $transactions = $wallet->transactions()
                    ->where('status', 'completed')
                    ->get();

                foreach ($transactions as $transaction) {
                    $amount = $transaction->amount;

                    // Convert amount based on currency using transaction's stored rate
                    if ($transaction->currency !== 'NGN' && $transaction->rate) {
                        $amount = $amount * $transaction->rate;
                    }

                    // Add to totals based on transaction type
                    if ($transaction->type === 'debit') {
                        $totalSpent += $amount;
                    } else {
                        $totalCredited += $amount;
                    }
                }
            }
        }

        return view('dashboard.index', compact(
            'totalOrganizations',
            'totalSpent',
            'totalCredited'
        ));
    }
}
