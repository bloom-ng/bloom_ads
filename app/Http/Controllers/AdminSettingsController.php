<?php

namespace App\Http\Controllers;

use App\Models\UserSettings;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $userSettings = UserSettings::all();

        return view('admin-dashboard.settings.index', compact('userSettings'));
    }

    public function create()
    {
        return view('admin-dashboard.settings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required|string',
        ]);

        UserSettings::create([
            'user_id' => auth()->id(), // Assuming you want to associate it with the current user
            'preferences' => [$request->input('name') => $request->input('value')],
        ]);

        return redirect()->route('admin.adminsettings.index')->with('success', 'Setting created successfully');
    }

    public function update(Request $request, UserSettings $userSetting)
    {
        $request->validate([
            'value' => 'required|string',
        ]);

        $preferences = $userSetting->preferences;
        $preferences[$request->input('name')] = $request->input('value');
        $userSetting->update(['preferences' => $preferences]);

        return redirect()->route('admin.adminsettings.index')->with('success', 'Setting updated successfully');
    }

    public function destroy(AdminSetting $adminsetting)
    {
        Cache::forget("admin_setting_{$adminsetting->key}");
        $adminsetting->delete();

        return redirect()
            ->route('admin.adminsettings.index')
            ->with('success', 'Setting deleted successfully');
    }
} 