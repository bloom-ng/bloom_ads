<?php

namespace App\Http\Controllers;

use App\Models\BusinessManager;
use App\Services\Meta\AdAccountService;
use Illuminate\Http\Request;

class BusinessManagerController extends Controller
{
    public function index()
    {
        $managers = BusinessManager::all();
        return view('admin-dashboard.business-managers.index', compact('managers'));
    }

    public function create()
    {
        return view('admin-dashboard.business-managers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'portfolio_id' => 'required|string',
            'token' => 'required|string',
            'platform' => 'nullable|string'
        ]);

        BusinessManager::create($validated);

        return redirect()
            ->route('admin.business-managers.index')
            ->with('success', 'Business Manager created successfully.');
    }

    public function edit(BusinessManager $businessManager)
    {
        return view('admin-dashboard.business-managers.edit', compact('businessManager'));
    }

    public function update(Request $request, BusinessManager $businessManager)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'portfolio_id' => 'required|string',
            'token' => 'required|string',
            'platform' => 'nullable|string'
        ]);

        $businessManager->update($validated);

        return redirect()
            ->route('admin.business-managers.index')
            ->with('success', 'Business Manager updated successfully.');
    }

    public function destroy(BusinessManager $businessManager)
    {
        $businessManager->delete();

        return redirect()
            ->route('admin.business-managers.index')
            ->with('success', 'Business Manager deleted successfully.');
    }

    public function showAccounts(Request $request, BusinessManager $businessManager)
    {
        $adAccountService = new AdAccountService($businessManager->portfolio_id, $businessManager->token);
        
        try {
            $before = $request->get('before');
            $after = $request->get('after');
            $limit = 25;
            $tab = $request->get('tab', 'client'); // Default to client accounts
            
            // Get accounts based on selected tab
            $result = $tab === 'owned' 
                ? $adAccountService->getOwnedAdAccounts($before, $after, $limit)
                : $adAccountService->getClientAdAccounts($before, $after, $limit);

            $adAccounts = collect($result->items)->map(fn ($account) => [
                'id' => str_replace('act_', '', $account['id']),
                'account_id' => $account['id'],
                'name' => $account['name'],
                'currency' => $account['currency'],
                'status' => $account['account_status'] === 1 ? 'ACTIVE' : 'INACTIVE',
                'amount_spent' => $account['amount_spent'],
                'balance' => $account['balance'],
                'business_name' => $account['business']['name'] ?? null,
            ]);

            return view('admin-dashboard.business-managers.accounts', [
                'businessManager' => $businessManager,
                'adAccounts' => $adAccounts,
                'nextCursor' => $result->nextCursor,
                'prevCursor' => $result->previousCursor,
                'hasNext' => $result->hasMore(),
                'hasPrev' => !empty($result->previousCursor),
                'activeTab' => $tab
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch ad accounts: ' . $e->getMessage());
        }
    }

    public function accounts(BusinessManager $businessManager, Request $request)
    {
        $activeTab = $request->get('tab', 'client');
        $before = $request->get('before');
        $after = $request->get('after');

        $adAccountService = new AdAccountService($businessManager->provider_id);
        
        if ($activeTab === 'client') {
            $result = $adAccountService->getClientAdAccounts($before, $after);
        } else {
            $result = $adAccountService->getOwnedAdAccounts($before, $after);
        }

        return view('admin-dashboard/business-managers/accounts', [
            'businessManager' => $businessManager,
            'adAccounts' => $result->data,
            'activeTab' => $activeTab,
            'hasNext' => $result->hasNext,
            'hasPrev' => $result->hasPrev,
            'nextCursor' => $result->after,
            'prevCursor' => $result->before,
        ]);
    }
}
