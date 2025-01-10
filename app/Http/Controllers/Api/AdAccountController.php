<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdAccount;
use App\Models\BusinessManager;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdAccountController extends Controller
{
    public function linkAccount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ad_account_id' => 'required|string',
            'business_manager_id' => 'required|exists:business_managers,id'
        ]);

        $businessManager = BusinessManager::findOrFail($validated['business_manager_id']);

        $adAccount = AdAccount::where('provider_id', $validated['ad_account_id'])->first();
        
        if (!$adAccount) {
            return response()->json(['message' => 'Ad account not found'], 404);
        }

        $adAccount->update([
            'provider' => 'meta',
            'provider_bm_id' => $businessManager->provider_id
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
    
} 