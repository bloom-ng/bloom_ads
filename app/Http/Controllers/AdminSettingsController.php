<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $settings = AdminSetting::all();
        return view('admin-dashboard.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required|string',
        ]);

        AdminSetting::create([
            'name' => $request->input('name'),
            'key' => Str::slug($request->input('name')), // Convert name to a slug for the key
            'value' => $request->input('value'),
        ]);

        return redirect()->route('admin.adminsettings.index')
            ->with('success', 'Setting created successfully');
    }

    public function update(Request $request, AdminSetting $adminSetting)
    {
        $request->validate([
            'value' => 'required|string',
        ]);

        $adminSetting->update([
            'value' => $request->input('value')
        ]);

        // Always clear the AdminSetting-level cache for the updated key
        Cache::forget("admin_setting_{$adminSetting->key}");

        // If currency margin was updated, recalculate rates
        if ($adminSetting->key === 'currency_margin') {
            $margin = $request->input('value');
            
            // Get current API rates
            $usdApiRate = AdminSetting::where('key', 'usd_api_rate')->first()?->value ?? 1800;
            $gbpApiRate = AdminSetting::where('key', 'gbp_api_rate')->first()?->value ?? 2300;

            // Calculate new Bloom rates with updated margin
            $usdBloomRate = $usdApiRate + $margin;
            $gbpBloomRate = $gbpApiRate + $margin;

            // Update Bloom rates
            AdminSetting::updateOrCreate(
                ['key' => 'usd_rate'],
                ['name' => 'USD RATE', 'value' => $usdBloomRate]
            );

            AdminSetting::updateOrCreate(
                ['key' => 'gbp_rate'],
                ['name' => 'GBP RATE', 'value' => $gbpBloomRate]
            );
        }

        // CRITICAL: Always flush the Wallet::getRate() caches for both currencies.
        // These are separate from the AdminSetting cache and are used everywhere
        // the user side displays or converts exchange rates. Without this, users
        // see stale rates for up to 5 minutes after an admin changes any rate.
        Cache::forget('currency_rate_usd');
        Cache::forget('currency_rate_gbp');

        // Also clear the AdminSetting-level caches for the Bloom rates and API rates,
        // so the admin settings page itself also reflects the latest values immediately.
        Cache::forget('admin_setting_usd_rate');
        Cache::forget('admin_setting_gbp_rate');
        Cache::forget('admin_setting_usd_api_rate');
        Cache::forget('admin_setting_gbp_api_rate');
        Cache::forget('admin_setting_currency_margin');

        return redirect()->route('admin.adminsettings.index')
            ->with('success', 'Setting updated successfully');
    }

    public function destroy(AdminSetting $adminSetting)
    {
        try {
            // Don't allow deletion of currency-related settings
            if (in_array($adminSetting->key, ['currency_margin', 'usd_rate', 'gbp_rate'])) {
                return redirect()->route('admin.adminsettings.index')
                    ->with('error', 'Cannot delete currency-related settings');
            }

            $adminSetting->delete();
            
            return redirect()->route('admin.adminsettings.index')
                ->with('success', 'Setting deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.adminsettings.index')
                ->with('error', 'Failed to delete setting: ' . $e->getMessage());
        }
    }

    /**
     * Manually refresh currency rates.
     * Limited to 2 refreshes per calendar day per admin.
     */
    public function refreshRates(Request $request)
    {
        $cacheKey = 'currency_refresh_count_' . now()->format('Y-m-d');
        $count = (int) Cache::get($cacheKey, 0);
        $maxPerDay = 2;

        if ($count >= $maxPerDay) {
            return response()->json([
                'success'   => false,
                'message'   => 'Daily limit reached. Rates can only be manually refreshed ' . $maxPerDay . ' times per day.',
                'remaining' => 0,
            ], 429);
        }

        try {
            Artisan::call('currency:fetch-rates');

            // Increment counter; expires at end of today
            Cache::put($cacheKey, $count + 1, now()->endOfDay());

            $remaining = $maxPerDay - ($count + 1);

            return response()->json([
                'success'   => true,
                'message'   => 'Currency rates refreshed successfully.',
                'remaining' => $remaining,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh rates: ' . $e->getMessage(),
            ], 500);
        }
    }
}