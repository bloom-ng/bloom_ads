<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $organizations = $user->organizations;

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

        $user->setCurrentOrganization($organization);

        return back()->with('success', 'Current organization updated successfully.');
    }
}
