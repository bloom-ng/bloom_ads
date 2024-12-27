<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\UserSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $currentOrganization = Auth::user()->settings->current_organization_id ?? null;

        if (Auth::guard('admin')->check()) {
            // For admin users
            $organizations = Organization::with('users')->get();
        } else {
            // For regular users
            $organizations = auth()->user()->organizations()->with('users')->get();
        }

        return view('dashboard.settings.index', compact('organizations', 'currentOrganization'));
    }

    public function setCurrentOrganization(Request $request)
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id'
        ]);

        $user = Auth::user();
        $organization = Organization::findOrFail($validated['organization_id']);

        // Verify user belongs to this organization
        if (!$user->organizations()
            ->where('organizations.id', $organization->id)
            ->exists()) {
            return back()->with('error', 'You do not belong to this organization.');
        }

        // Create or update user settings
        UserSettings::updateOrCreate(
            ['user_id' => $user->id],
            [
                'current_organization_id' => $organization->id,
                'preferences' => UserSettings::getPreferences()
            ]
        );

        return back()->with('success', 'Current organization updated successfully.');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'two_factor_enabled' => 'boolean',
            // Add other validation rules for other settings if necessary
        ]);

        // Get the user's settings
        $userSettings = UserSettings::where('user_id', auth()->id())->first();

        // Update the settings
        $userSettings->update($validated);

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
