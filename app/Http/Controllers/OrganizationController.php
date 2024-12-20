<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Notifications\OrganizationInvitation;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $organizations = auth()->user()
            ->organizations()
            ->withPivot('role')
            ->get();

        return view('dashboard.organization.index', compact('organizations'));
    }

    public function invites(Organization $organization)
    {
        $this->authorize('manage', $organization);
        $pendingInvites = $organization->invites()->where('expires_at', '>', now())->get();
        return view('dashboard.organization.invites', compact('organization', 'pendingInvites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validated['user_id'] = Auth::user()->id;

        $organization = Organization::create($validated);

        // Add current user as owner
        $organization->users()->attach(Auth::user()->id, ['role' => 'owner']);

        return redirect()->route('organizations.index')->with('success', 'Organization created successfully');
    }

    public function invite(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:admin,finance,advertiser',
        ]);

        // Add logging
        \Log::info('Sending organization invitation', [
            'email' => $validated['email'],
            'organization' => $organization->name
        ]);

        // Check if user is already in organization
        $existingUser = User::where('email', $validated['email'])->first();
        if ($existingUser) {
            $organization->users()->attach($existingUser->id, ['role' => $validated['role']]);
            return back()->with('success', 'User added to organization');
        }

        // Create invitation
        $invite = OrganizationInvite::create([
            'organization_id' => $organization->id,
            'email' => $validated['email'],
            'role' => $validated['role'],
            'token' => Str::random(32),
            'expires_at' => now()->addDays(7),
        ]);

        try {
            // Send invitation email with error handling
            Notification::route('mail', $validated['email'])
                ->notify(new OrganizationInvitation($organization, $invite));

            \Log::info('Organization invitation sent successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to send organization invitation', [
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to send invitation email. Please try again.');
        }

        return back()->with('success', 'Invitation sent');
    }

    public function cancelInvite(Organization $organization, OrganizationInvite $invite)
    {
        $this->authorize('manage', $organization);
        $invite->delete();
        return back()->with('success', 'Invitation cancelled');
    }

    public function resendInvite(Organization $organization, OrganizationInvite $invite)
    {
        $this->authorize('manage', $organization);

        // Update expiration date
        $invite->update([
            'expires_at' => now()->addDays(7)
        ]);

        // Resend notification
        Notification::route('mail', $invite->email)
            ->notify(new OrganizationInvitation($organization, $invite));

        return back()->with('success', 'Invitation resent');
    }

    public function removeMember(Organization $organization, User $user)
    {
        $this->authorize('manage', $organization);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot remove yourself');
        }

        $organization->users()->detach($user->id);
        return back()->with('success', 'Member removed');
    }

    public function create()
    {
        return view('dashboard.organization.create');
    }
}
