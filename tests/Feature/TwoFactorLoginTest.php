<?php

namespace Tests\Feature;

use App\Mail\TwoFactorCodeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class TwoFactorLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    protected function makeUser(bool $twoFactor = true): User
    {
        $user = User::factory()->create(['user_type' => 'direct_advertiser']);
        $user->settings()->create(['two_factor_enabled' => $twoFactor]);

        return $user;
    }

    protected function login(User $user, string $password = 'password')
    {
        return $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);
    }

    protected function submitCode(string $code)
    {
        return $this->from('/2fa/verify')->post('/2fa/verify', ['code' => $code]);
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

    protected function wrongCode(string $code): string
    {
        return $code === '000000' ? '111111' : '000000';
    }

    public function test_login_without_two_factor_signs_the_user_in(): void
    {
        $user = $this->makeUser(twoFactor: false);

        $this->login($user)->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
        Mail::assertNothingSent();
    }

    public function test_unknown_email_and_wrong_password_get_the_same_error(): void
    {
        $user = $this->makeUser(twoFactor: false);

        $this->from('/login')->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'password',
        ])->assertRedirect('/login')->assertSessionHas('error', 'Invalid email or password.');

        $this->login($user, 'wrong-password')
            ->assertRedirect('/login')
            ->assertSessionHas('error', 'Invalid email or password.');

        $this->assertGuest();
    }

    public function test_login_regenerates_the_session(): void
    {
        $user = $this->makeUser(twoFactor: false);
        $fixated = Str::random(40);

        $this->withCookie(config('session.cookie'), $fixated)
            ->withSession(['_token' => $fixated])
            ->login($user);

        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($fixated, session()->getId());
        $this->assertNotSame($fixated, session()->token());
    }

    public function test_two_factor_user_is_sent_a_code_instead_of_being_signed_in(): void
    {
        $user = $this->makeUser();

        $this->login($user)->assertRedirect(route('2fa.verify'));

        $this->assertGuest();
        $this->assertMatchesRegularExpression('/^\d{6}$/', $this->lastSentCode());
    }

    public function test_code_is_not_kept_in_the_session_or_logged(): void
    {
        $logged = [];
        Event::listen(MessageLogged::class, function (MessageLogged $event) use (&$logged) {
            $logged[] = $event->message . ' ' . json_encode($event->context);
        });

        $user = $this->makeUser();
        $this->login($user);
        $code = $this->lastSentCode();

        $this->assertStringNotContainsString($code, json_encode(session()->all()));

        $this->submitCode($this->wrongCode($code));
        $this->submitCode($code);

        foreach ($logged as $line) {
            $this->assertStringNotContainsString($code, $line);
        }
    }

    public function test_correct_code_signs_the_user_in_and_regenerates_the_session(): void
    {
        $user = $this->makeUser();
        $this->login($user);
        $fixated = Str::random(40);

        $this->withCookie(config('session.cookie'), $fixated)
            ->withSession(['_token' => $fixated])
            ->submitCode($this->lastSentCode())
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($fixated, session()->getId());
        $this->assertNotSame($fixated, session()->token());
    }

    public function test_code_cannot_be_reused(): void
    {
        $user = $this->makeUser();
        $this->login($user);
        $code = $this->lastSentCode();

        $this->submitCode($code);
        $this->post('/logout');
        $this->assertGuest();

        $this->submitCode($code)->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_wrong_code_returns_to_the_form_with_an_error(): void
    {
        $user = $this->makeUser();
        $this->login($user);

        $this->submitCode($this->wrongCode($this->lastSentCode()))
            ->assertRedirect('/2fa/verify')
            ->assertSessionHasErrors(['code' => 'Incorrect code. 4 attempts left.']);

        $this->assertGuest();
    }

    public function test_code_expires_after_ten_minutes(): void
    {
        $user = $this->makeUser();
        $this->login($user);
        $code = $this->lastSentCode();

        $this->travel(11)->minutes();

        $this->submitCode($code)
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['code' => 'Your code has expired. Please log in again to get a new one.']);

        $this->assertGuest();
    }

    public function test_code_is_discarded_after_five_wrong_attempts(): void
    {
        $user = $this->makeUser();
        $this->login($user);
        $code = $this->lastSentCode();

        for ($i = 0; $i < 4; $i++) {
            $this->submitCode($this->wrongCode($code))->assertRedirect('/2fa/verify');
        }

        $this->submitCode($this->wrongCode($code))
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['code' => 'Too many incorrect attempts. Please log in again to get a new code.']);

        // The right code no longer works either
        $this->submitCode($code)->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_code_requests_are_throttled(): void
    {
        $user = $this->makeUser();

        for ($i = 0; $i < 5; $i++) {
            $this->login($user)->assertRedirect(route('2fa.verify'));
        }

        $this->login($user)
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        Mail::assertSent(TwoFactorCodeMail::class, 5);
    }

    public function test_verifying_without_logging_in_first_is_rejected(): void
    {
        $this->submitCode('123456')->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_duplicate_email_on_register_shows_the_validation_error(): void
    {
        $user = $this->makeUser(twoFactor: false);

        $this->from('/signup1')->post(route('signup.register'), [
            'name' => 'Someone',
            'email' => $user->email,
            'password' => 'password123',
            'user_type' => 'direct_advertiser',
            'business_name' => 'Acme',
            'country_code' => '+234',
            'phone_number' => '07067531320',
            'country' => 'Nigeria',
            'terms_accepted' => '1',
        ])->assertSessionHasErrors(['email' => 'The email has already been taken.']);
    }
}
