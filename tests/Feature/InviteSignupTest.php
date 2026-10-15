<?php

namespace Tests\Feature;

use App\Mail\TwoFactorCodeMail;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

class InviteSignupTest extends TestCase
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

    protected function makeUser(?bool $twoFactor = null): User
    {
        $user = User::factory()->create(['user_type' => 'direct_advertiser']);

        if ($twoFactor !== null) {
            $user->settings()->create(['two_factor_enabled' => $twoFactor]);
        }

        return $user;
    }

    protected function inviteFor(User $user, string $role = 'advertiser'): OrganizationInvite
    {
        return OrganizationInvite::create([
            'organization_id' => $this->organization->id,
            'email' => $user->email,
            'role' => $role,
            'token' => Str::random(32),
            'expires_at' => now()->addDay(),
        ]);
    }

    protected function accept(OrganizationInvite $invite)
    {
        return $this->post('/signup/invite', ['email' => $invite->email, 'token' => $invite->token]);
    }

    protected function memberships(User $user)
    {
        return $this->organization->users()->whereKey($user->id);
    }

    protected function lastSentCode(): string
    {
        $code = null;

        Mail::assertSent(TwoFactorCodeMail::class, function (TwoFactorCodeMail $mail) use (&$code) {
            $code = (string) $mail->code;

            return true;
        });

        return $code;
    }

    public function test_existing_user_accepting_an_invite_is_signed_in_with_a_regenerated_session(): void
    {
        $user = $this->makeUser();
        $invite = $this->inviteFor($user);
        $fixated = Str::random(40);

        $this->withCookie(config('session.cookie'), $fixated)
            ->withSession(['_token' => $fixated])
            ->accept($invite)
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($fixated, session()->getId());
        $this->assertNotSame($fixated, session()->token());
        $this->assertTrue($this->memberships($user)->exists());
        $this->assertDatabaseHas('organization_invites', ['id' => $invite->id, 'used' => true]);
    }

    public function test_invite_cannot_be_used_again_to_sign_in(): void
    {
        $user = $this->makeUser(twoFactor: false);
        $invite = $this->inviteFor($user);

        $this->accept($invite)->assertRedirect(route('dashboard'));
        Auth::logout();

        $this->accept($invite)->assertNotFound();

        $this->assertGuest();
        $this->assertSame(1, $this->memberships($user)->count());
    }

    public function test_existing_member_is_not_attached_again_and_keeps_their_role(): void
    {
        $user = $this->makeUser(twoFactor: false);
        $this->organization->users()->attach($user->id, ['role' => 'admin']);
        $invite = $this->inviteFor($user, role: 'advertiser');

        $this->accept($invite)->assertRedirect(route('dashboard'));

        $this->assertSame(1, $this->memberships($user)->count());
        $this->assertSame('admin', $this->memberships($user)->first()->pivot->role);
        $this->assertDatabaseHas('organization_invites', ['id' => $invite->id, 'used' => true]);
    }

    public function test_two_factor_user_is_sent_a_code_instead_of_being_signed_in(): void
    {
        $user = $this->makeUser(twoFactor: true);
        $invite = $this->inviteFor($user);

        $this->accept($invite)->assertRedirect(route('2fa.verify'));

        $this->assertGuest();
        $this->assertSame($user->id, session('2fa_user_id'));
        $this->assertMatchesRegularExpression('/^\d{6}$/', $this->lastSentCode());
        $this->assertTrue($this->memberships($user)->exists());
        $this->assertDatabaseHas('organization_invites', ['id' => $invite->id, 'used' => true]);
    }

    public function test_two_factor_user_is_signed_in_after_entering_the_code(): void
    {
        $user = $this->makeUser(twoFactor: true);
        $this->accept($this->inviteFor($user));

        $this->from('/2fa/verify')
            ->post('/2fa/verify', ['code' => $this->lastSentCode()])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_throttled_code_request_sends_two_factor_user_to_the_login_page(): void
    {
        $user = $this->makeUser(twoFactor: true);
        $invite = $this->inviteFor($user);

        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit("2fa-send:{$user->id}", 900);
        }

        $this->accept($invite)
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertNull(session('2fa_user_id'));
        Mail::assertNothingSent();
        $this->assertTrue($this->memberships($user)->exists());
    }
}
