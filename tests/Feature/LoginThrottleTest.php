<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class LoginThrottleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        $this->freezeTime();
    }

    protected function makeUser(): User
    {
        $user = User::factory()->create(['user_type' => 'direct_advertiser']);
        $user->settings()->create(['two_factor_enabled' => false]);

        return $user;
    }

    protected function login(string $email, string $password = 'password', string $ip = '127.0.0.1')
    {
        return $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->from('/login')
            ->post('/login', ['email' => $email, 'password' => $password]);
    }

    protected function failLogins(string $email, int $times, string $ip = '127.0.0.1'): void
    {
        for ($i = 0; $i < $times; $i++) {
            $this->login($email, 'wrong-password', $ip)
                ->assertSessionHas('error', 'Invalid email or password.');
        }
    }

    public function test_sixth_attempt_is_locked_out_even_with_the_right_password(): void
    {
        $user = $this->makeUser();
        $this->failLogins($user->email, 5);

        $this->login($user->email)
            ->assertRedirect('/login')
            ->assertSessionHas('error', 'Too many login attempts. Please try again in 60 seconds.');

        $this->assertGuest();
    }

    public function test_lockout_message_counts_down(): void
    {
        $user = $this->makeUser();
        $this->failLogins($user->email, 5);

        $this->travel(59)->seconds();

        $this->login($user->email)
            ->assertSessionHas('error', 'Too many login attempts. Please try again in 1 second.');
    }

    public function test_lockout_ends_after_a_minute(): void
    {
        $user = $this->makeUser();
        $this->failLogins($user->email, 5);

        $this->travel(61)->seconds();

        $this->login($user->email)->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_successful_login_clears_the_failed_attempts(): void
    {
        $user = $this->makeUser();

        $this->failLogins($user->email, 4);
        $this->login($user->email)->assertRedirect('/dashboard');
        $this->post('/logout');
        $this->assertGuest();

        // Without the reset this would be failures 5-8 and the next login would be locked out
        $this->failLogins($user->email, 4);
        $this->login($user->email)->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_attempts_count_per_email_regardless_of_case(): void
    {
        $user = $this->makeUser();
        $this->failLogins(Str::upper($user->email), 5);

        $this->login($user->email)
            ->assertSessionHas('error', 'Too many login attempts. Please try again in 60 seconds.');

        $this->assertGuest();
    }

    public function test_lockout_does_not_block_other_ips_or_other_emails(): void
    {
        $user = $this->makeUser();
        $other = $this->makeUser();
        $this->failLogins($user->email, 5, '10.0.0.1');

        $this->login($other->email, ip: '10.0.0.1')->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($other);
        $this->post('/logout');

        $this->login($user->email, ip: '10.0.0.2')->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }
}
