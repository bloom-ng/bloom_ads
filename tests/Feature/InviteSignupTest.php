<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class InviteSignupTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_user_accepting_an_invite_is_signed_in_with_a_regenerated_session(): void
    {
        $owner = User::factory()->create(['user_type' => 'direct_advertiser']);
        $organization = Organization::create(['name' => 'Acme', 'user_id' => $owner->id]);
        $user = User::factory()->create(['user_type' => 'direct_advertiser']);
        $invite = OrganizationInvite::create([
            'organization_id' => $organization->id,
            'email' => $user->email,
            'role' => 'advertiser',
            'token' => Str::random(32),
            'expires_at' => now()->addDay(),
        ]);
        $fixated = Str::random(40);

        $this->withCookie(config('session.cookie'), $fixated)
            ->withSession(['_token' => $fixated])
            ->post('/signup/invite', ['email' => $user->email, 'token' => $invite->token])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($fixated, session()->getId());
        $this->assertNotSame($fixated, session()->token());
        $this->assertTrue($organization->users()->whereKey($user->id)->exists());
    }
}
