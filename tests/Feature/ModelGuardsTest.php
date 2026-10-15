<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\User;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ModelGuardsTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_cannot_be_attached_to_an_organization_twice(): void
    {
        $user = User::factory()->create(['user_type' => 'direct_advertiser']);
        $organization = Organization::create(['name' => 'Acme', 'user_id' => $user->id]);

        $organization->users()->attach($user->id, ['role' => 'owner']);

        $this->expectException(UniqueConstraintViolationException::class);

        $organization->users()->attach($user->id, ['role' => 'advertiser']);
    }

    public function test_mass_assigning_a_non_fillable_attribute_throws(): void
    {
        $user = User::factory()->create(['user_type' => 'direct_advertiser']);
        $organization = Organization::create(['name' => 'Acme', 'user_id' => $user->id]);
        $invite = OrganizationInvite::create([
            'organization_id' => $organization->id,
            'email' => 'invitee@example.com',
            'role' => 'advertiser',
            'token' => Str::random(32),
            'expires_at' => now()->addDay(),
        ]);

        // 'used' isn't fillable; this used to be dropped silently and left invites reusable
        $this->expectException(MassAssignmentException::class);

        $invite->update(['used' => true]);
    }
}
