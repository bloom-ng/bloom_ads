<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class InviteRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $owner = User::factory()->create(['user_type' => 'direct_advertiser']);
        $this->organization = Organization::create(['name' => 'Acme', 'user_id' => $owner->id]);
    }

    protected function invite(): OrganizationInvite
    {
        return OrganizationInvite::create([
            'organization_id' => $this->organization->id,
            'email' => 'invitee@example.com',
            'role' => 'finance',
            'token' => Str::random(32),
            'expires_at' => now()->addDay(),
        ]);
    }

    protected function openInvite(OrganizationInvite $invite)
    {
        return $this->get(route('signup.invite.show', ['email' => $invite->email, 'token' => $invite->token]));
    }

    protected function register(string $email)
    {
        return $this->post(route('signup.register'), [
            'name' => 'Invited Person',
            'email' => $email,
            'password' => 'password123',
            'user_type' => 'direct_advertiser',
            'country_code' => '+234',
            'phone_number' => '07067531320',
            'country' => 'Nigeria',
            'terms_accepted' => '1',
        ]);
    }

    protected function assertRejected($response, string $email): void
    {
        $response->assertRedirect('/signup1')
            ->assertSessionHasErrors(['email' => 'This invitation is no longer valid. Please ask for a new one.']);

        $this->assertGuest();
        $this->assertNull(session('invite_data'));
        $this->assertDatabaseMissing('users', ['email' => $email]);
    }

    public function test_invited_user_registers_into_the_organization_with_the_invited_role(): void
    {
        $invite = $this->invite();
        $this->openInvite($invite)->assertOk();

        $this->register($invite->email)->assertRedirect(route('verification.notice'));

        $user = User::where('email', $invite->email)->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('finance', $this->organization->users()->whereKey($user->id)->first()->pivot->role);
        $this->assertSame($this->organization->id, $user->settings->current_organization_id);
        $this->assertSame(1, Organization::count());
        $this->assertDatabaseHas('organization_invites', ['id' => $invite->id, 'used' => true]);
        $this->assertNull(session('invite_data'));
    }

    public function test_registering_under_another_email_is_rejected_and_leaves_the_invite_open(): void
    {
        $invite = $this->invite();
        $this->openInvite($invite)->assertOk();

        $this->assertRejected($this->register('someone-else@example.com'), 'someone-else@example.com');

        $this->assertSame(0, $this->organization->users()->count());
        $this->assertDatabaseHas('organization_invites', ['id' => $invite->id, 'used' => false]);
    }

    public function test_invite_cancelled_after_the_link_was_opened_is_rejected(): void
    {
        $invite = $this->invite();
        $this->openInvite($invite)->assertOk();

        // What OrganizationController::cancelInvite does
        $invite->delete();

        $this->assertRejected($this->register($invite->email), $invite->email);
        $this->assertSame(0, $this->organization->users()->count());
    }

    public function test_invite_that_expired_after_the_link_was_opened_is_rejected(): void
    {
        $invite = $this->invite();
        $this->openInvite($invite)->assertOk();

        $this->travel(2)->days();

        $this->assertRejected($this->register($invite->email), $invite->email);
        $this->assertSame(0, $this->organization->users()->count());
        $this->assertDatabaseHas('organization_invites', ['id' => $invite->id, 'used' => false]);
    }

    public function test_invite_claimed_by_another_registration_is_rejected(): void
    {
        $invite = $this->invite();
        $this->openInvite($invite)->assertOk();

        // Another request claimed it first and hasn't created its user yet
        $invite->forceFill(['used' => true])->save();

        $this->assertRejected($this->register($invite->email), $invite->email);
        $this->assertSame(0, $this->organization->users()->count());
    }

    public function test_invite_cannot_be_used_twice_across_two_sessions(): void
    {
        $invite = $this->invite();

        // A second browser opens the forwarded link while the invite is still open
        $this->openInvite($invite)->assertOk();
        $secondSession = ['invite_data' => session('invite_data')];
        $this->flushSession();

        $this->openInvite($invite)->assertOk();
        $this->register($invite->email)->assertRedirect(route('verification.notice'));
        Auth::logout();
        $this->flushSession();

        // The email field is only readonly in the browser
        $this->assertRejected(
            $this->withSession($secondSession)->register('someone-else@example.com'),
            'someone-else@example.com'
        );

        $this->assertSame(1, $this->organization->users()->count());
        $this->assertTrue($this->organization->users()->where('email', $invite->email)->exists());
    }
}
