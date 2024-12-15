<?php

namespace App\Http\Controllers;

use App\Models\AdAccount;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FilteredAdAccountsExport;
use Illuminate\Support\Facades\Log;

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
} 