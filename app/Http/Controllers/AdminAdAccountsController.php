<?php

namespace App\Http\Controllers;

use App\Models\AdAccount;
use Illuminate\Http\Request;

class AdminAdAccountsController extends Controller
{
    public function index()
    {
        $adAccounts = AdAccount::with(['user', 'organization'])->get();
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
            'business_manager_id' => 'nullable|string|size:32',
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
} 