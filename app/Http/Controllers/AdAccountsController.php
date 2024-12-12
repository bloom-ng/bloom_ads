<?php

namespace App\Http\Controllers;

use App\Models\AdAccount;
use App\Services\RockAds;
use Illuminate\Http\Request;
use Illuminate\Http\Client\RequestException;

class AdAccountsController extends Controller
{
    protected $rockAds;

    public function __construct()
    {
        $this->rockAds = new RockAds();
    }

    public function index()
    {
        $adAccounts = AdAccount::with(['organization', 'user'])->get();
        return view('dashboard.adaccounts.index', compact('adAccounts'));
    }

    public function create()
    {
        try {
            // Get platforms from RockAds API
            $platformsResponse = $this->rockAds->getAdPlatforms();
            
            \Log::info('Initial platforms response:', [
                'platforms_count' => count($platformsResponse->platforms),
                'platforms' => array_map(function($p) {
                    return ['id' => $p->id, 'name' => $p->name];
                }, $platformsResponse->platforms)
            ]);

            // Transform the data for the view
            $platforms = collect($platformsResponse->platforms)
                ->filter(function($platform) {
                    $hasRequired = $platform->id && $platform->name;
                    \Log::info('Filtering platform:', [
                        'id' => $platform->id,
                        'name' => $platform->name,
                        'passes_filter' => $hasRequired
                    ]);
                    return $hasRequired;
                })
                ->map(function($platform) {
                    $data = [
                        'id' => $platform->getCode(),
                        'name' => $platform->name
                    ];
                    \Log::info('Mapping platform:', $data);
                    return $data;
                })
                ->values();

            \Log::info('Final platforms collection:', [
                'count' => $platforms->count(),
                'data' => $platforms->toArray()
            ]);

            $timezonesResponse = $this->rockAds->getTimezones();

            \Log::info('Timezones Response Object:', [
                'raw' => $timezonesResponse,
                'timezones_count' => count($timezonesResponse->timezones)
            ]);

            $timezones = collect($timezonesResponse->timezones)
                ->filter(function($timezone) {
                    $hasRequired = $timezone->id && $timezone->name;
                    \Log::info('Filtering timezone:', [
                        'id' => $timezone->id,
                        'name' => $timezone->name,
                        'offset_str' => $timezone->offset_str,
                        'passes_filter' => $hasRequired
                    ]);
                    return $hasRequired;
                })
                ->map(function($timezone) {
                    $data = [
                        'id' => $timezone->id,
                        'name' => $timezone->name . ' (' . $timezone->offset_str . ')'
                    ];
                    \Log::info('Mapping timezone:', $data);
                    return $data;
                })
                ->values();

            \Log::info('Final timezones collection:', [
                'count' => $timezones->count(),
                'data' => $timezones->toArray()
            ]);

            // Convert to array before passing to view and add debug
            $timezonesArray = $timezones->toArray();
            \Log::info('Timezones being passed to view:', $timezonesArray);

            return view('dashboard.adaccounts.create', [
                'platforms' => $platforms->toArray(),
                'timezones' => $timezonesArray,
                'currencies' => ['USD', 'NGN']
            ]);
        } catch (RequestException $e) {
            \Log::error('RockAds API Error:', [
                'message' => $e->getMessage(),
                'response' => $e->response?->json(),
                'status' => $e->response?->status()
            ]);
            return redirect()->back()
                ->with('error', 'Unable to fetch ad platforms data. Please check your API configuration.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:meta,google,tiktok',
                'timezone' => 'required|string',
                'currency' => 'required|in:USD,NGN',
                'business_manager_id' => 'nullable|string|size:32',
                'landing_page' => 'nullable|url',
            ]);

            $user = auth()->user();
            
            if (!$user->currentOrganization) {
                return redirect()->back()
                    ->with('error', 'Please select an organization first')
                    ->withInput();
            }

            $validated['user_id'] = $user->id;
            $validated['organization_id'] = $user->settings?->current_organization_id;
            $validated['status'] = AdAccount::STATUS_PROCESSING;

            AdAccount::create($validated);

            return redirect()->route('adaccounts.index')
                ->with('success', 'Ad Account created successfully');
        } catch (\Exception $e) {
            \Log::error('Ad Account Creation Error:', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'organization' => auth()->user()->currentOrganization
            ]);
            
            return redirect()->back()
                ->with('error', 'Error creating ad account: ' . $e->getMessage())
                ->withInput();
        }
    }
} 