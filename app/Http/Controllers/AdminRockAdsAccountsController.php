<?php

namespace App\Http\Controllers;

use App\Services\RockAds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminRockAdsAccountsController extends Controller
{
    protected $rockAds;

    public function __construct()
    {
        $this->rockAds = new RockAds();
    }

    public function index(Request $request)
    {
        try {
            $adAccounts = $this->rockAds->getAdAccounts();
            
            // Get current page from request
            $currentPage = $request->get('page', 1);
            
            // Convert data to collection
            $collection = collect($adAccounts->data ?? []);
            
            // Create paginator
            $perPage = 10;
            $items = $collection->forPage($currentPage, $perPage);
            
            $paginator = new LengthAwarePaginator(
                $items,
                $collection->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            Log::info('RockAds Accounts Retrieved:', [
                'total' => $collection->count(),
                'current_page' => $currentPage
            ]);

            return view('admin-dashboard.rockads.index', [
                'adAccounts' => $paginator
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching RockAds accounts:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('admin-dashboard.rockads.index', [
                'adAccounts' => []
            ])->with('error', 'Unable to fetch RockAds accounts. Please try again later.');
        }
    }
} 