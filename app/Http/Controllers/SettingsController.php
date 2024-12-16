<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        if (Auth::guard('admin')->check()) {
            // For admin users
            $organizations = Organization::with('users')->get();
        } else {
            // For regular users
            $organizations = auth()->user()->organizations()->with('users')->get();
        }

        return view('dashboard.settings.index', compact('organizations'));
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

        $user->settings->update([
            'current_organization_id' => $organization->id
        ]);

        return back()->with('success', 'Current organization updated successfully.');
    }
}
