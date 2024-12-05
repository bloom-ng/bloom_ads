<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $organizations = $user->organizations;
        $currentOrganization = $user->currentOrganization;

        return view('dashboard.settings.index', compact('organizations', 'currentOrganization'));
    }

    public function setCurrentOrganization(Request $request)
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id'
        ]);

        $user = auth()->user();

        // Verify user belongs to this organization
        if (!$user->organizations()
            ->where('organizations.id', $validated['organization_id'])
            ->exists()) {
            return back()->with('error', 'You do not belong to this organization.');
        }

        $user->update(['current_organization_id' => $validated['organization_id']]);

        return back()->with('success', 'Current organization updated successfully.');
    }
}
