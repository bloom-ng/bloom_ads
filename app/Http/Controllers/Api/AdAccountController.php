<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdAccount;
use App\Models\BusinessManager;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Meta\AdAccountService;

class AdAccountController extends Controller
{
    public function linkAccount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'account_id' => 'required|string', //meta account id
            'ad_account_id' => 'required|string',
            'ad_account_name' => 'required|string',
            'currency' => 'required|string',
            'business_manager_id' => 'required|exists:business_managers,id'
        ]);

        $businessManager = BusinessManager::find($validated['business_manager_id']);

        if (!$businessManager) {
            return response()->json(['message' => 'Business manager not found'], 404);
        }

        $adAccount = AdAccount::find($validated['ad_account_id']);

        if (!$adAccount) {
            return response()->json(['message' => 'Ad account not found'], 404);
        }

        $adAccount->update([
            'provider' => 'meta',
            'provider_bm_id' => $businessManager->id,
            'provider_id' => $validated['account_id'],
            'provider_account_name' => $validated['ad_account_name'],
            'currency' => $validated['currency'],
            'status' => AdAccount::STATUS_APPROVED
        ]);

        return response()->json(['message' => 'Ad account linked successfully']);
    }

    public function linkNewAccount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'account_id' => 'required|string', //meta account id
            'ad_account_name' => 'required|string',
            'currency' => 'required|string',
            'business_manager_id' => 'required|exists:business_managers,id',
            'timezone' => 'string',
            'organization' => 'required|exists:organizations,id'
        ]);

        $businessManager = BusinessManager::find($validated['business_manager_id']);

        if (!$businessManager) {
            return response()->json(['message' => 'Business manager not found'], 404);
        }

       

        /* 
            protected $fillable = [
        'name',
        'type',
        'timezone',
        'currency',
        'provider',
        'provider_id',
        'status',
        'business_manager_id',
        'landing_page',
        'user_id',
        'organization_id',
        'provider_bm_id',
        'provider_account_name'
    ];
        */

        AdAccount::create([
            'user_id' => auth()->user()->id,
            'organization_id' => $validated['organization'],
            'landing_page' => 'example.com',
            'provider' => 'meta',
            'type' => 'meta',
            'business_manager_id' => $businessManager->portfolio_id,
            'provider_bm_id' => $businessManager->id,
            'provider_id' => $validated['account_id'],
            'name' => $validated['ad_account_name'],
            'provider_account_name' => $validated['ad_account_name'],
            'currency' => $validated['currency'],
            'timezone' => $validated['timezone'],
            'status' => AdAccount::STATUS_APPROVED,

        ]);

        return response()->json(['message' => 'Ad account linked successfully']);
    }

    public function getOrganizationAccounts(Organization $organization): JsonResponse
    {
        $accounts = $organization->adAccounts()
            ->whereNull('provider_bm_id')
            ->get(['id', 'name']);

        return response()->json($accounts);
    }

    public function getSpendCap(AdAccount $adAccount): JsonResponse
    {
        $businessManager = BusinessManager::find($adAccount->provider_bm_id);

        if (!$adAccount->provider_id) {
            return response()->json(['spend_cap' => 0]);
        }

        try {
            $metaService = new AdAccountService($businessManager->portfolio_id, $businessManager->token);
            $accountInfo = $metaService->getAdAccount($adAccount->provider_id);

            return response()->json([
                'spend_cap' => $accountInfo['spend_cap'] ?? 20,
                'balance' => $adAccount->getBalance()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
