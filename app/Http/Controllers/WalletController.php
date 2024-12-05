<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $organization = $user->currentOrganization;

        if (!$organization) {
            return redirect()->route('settings.index')
                ->with('error', 'Please select a current organization first.');
        }

        $organization->load('wallets');
        $userRole = $organization->users()->where('user_id', $user->id)->first()?->pivot->role;

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
        $userRole = $organization->users()->where('user_id', auth()->id())->first()?->pivot->role;
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
}
