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

        return redirect()->route('admin.adminsettings.index')
            ->with('success', 'Setting updated successfully');
    }

    public function destroy(AdminSetting $adminSetting)
    {
        $adminSetting->delete();
        return redirect()->route('admin.adminsettings.index')
            ->with('success', 'Setting deleted successfully');
    }
}