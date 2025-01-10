<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::query()
            ->orderBy('name')
            ->get();

        return response()->json($organizations);
    }

    public function getAdAccounts(Organization $organization): JsonResponse
    {
        $adAccounts = $organization->adAccounts()
            ->whereNull('provider_bm_id')
            ->get();

        return response()->json($adAccounts);
    }
} 