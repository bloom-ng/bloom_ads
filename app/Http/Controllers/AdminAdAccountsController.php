<?php

namespace App\Http\Controllers;

use App\Models\AdAccount;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProcessingAdAccountsExport;

class AdminAdAccountsController extends Controller
{
    public function index()
    {
        $adAccounts = AdAccount::with(['user', 'organization'])
        ->orderBy('created_at', 'desc')  // Optional: sort by creation date
        ->paginate(15);  // This will paginate with 10 items per page
    
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

    public function exportProcessingAccounts()
    {
        $processingAccounts = AdAccount::where('status', 'processing')->count();

        if ($processingAccounts === 0) {
            return redirect()->route('admin.adaccounts.index')
                ->with('warning', 'No processing ad accounts found to export.');
        }

        // If we have processing accounts, proceed with export
        return Excel::download(
            new ProcessingAdAccountsExport(),
            'processing-ad-accounts-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
} 