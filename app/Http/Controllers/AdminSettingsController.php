<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
}