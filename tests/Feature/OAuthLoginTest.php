<?php

namespace Tests\Feature;

use App\Mail\TwoFactorCodeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class OAuthLoginTest extends TestCase
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

    protected function fakeProviderUser(string $email): void
    {
        Socialite::shouldReceive('driver->user')->andReturn(
            (new SocialiteUser)->map(['id' => '1234567890', 'name' => 'Someone', 'email' => $email])
        );
    }

    protected function returnFromProvider()
    {
        return $this->get('/auth/google/callback');
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

    public function test_user_without_two_factor_is_signed_in_with_a_regenerated_session(): void
    {
        $user = $this->makeUser(twoFactor: false);
        $this->fakeProviderUser($user->email);
        $fixated = Str::random(40);

        $this->withCookie(config('session.cookie'), $fixated)
            ->withSession(['_token' => $fixated])
            ->returnFromProvider()
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($fixated, session()->getId());
        $this->assertNotSame($fixated, session()->token());
        Mail::assertNothingSent();
    }

    public function test_two_factor_user_is_sent_a_code_instead_of_being_signed_in(): void
    {
        $user = $this->makeUser();
        $this->fakeProviderUser($user->email);

        $this->returnFromProvider()->assertRedirect(route('2fa.verify'));

        $this->assertGuest();
        $this->assertSame($user->id, session('2fa_user_id'));
        $this->assertMatchesRegularExpression('/^\d{6}$/', $this->lastSentCode());
    }

    public function test_two_factor_user_is_signed_in_after_entering_the_code(): void
    {
        $user = $this->makeUser();
        $this->fakeProviderUser($user->email);
        $this->returnFromProvider();

        $this->from('/2fa/verify')
            ->post('/2fa/verify', ['code' => $this->lastSentCode()])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_throttled_code_request_returns_to_the_login_page(): void
    {
        $user = $this->makeUser();
        $this->fakeProviderUser($user->email);

        for ($i = 0; $i < 5; $i++) {
            $this->returnFromProvider()->assertRedirect(route('2fa.verify'));
        }

        $this->returnFromProvider()
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        Mail::assertSent(TwoFactorCodeMail::class, 5);
    }

    public function test_unknown_email_is_not_signed_in(): void
    {
        $this->fakeProviderUser('nobody@example.com');

        $this->returnFromProvider()
            ->assertRedirect('/login')
            ->assertSessionHas('error', 'User not found, please signup');

        $this->assertGuest();
    }

    public function test_provider_error_is_logged_and_shown_as_a_generic_message(): void
    {
        $logged = [];
        Event::listen(MessageLogged::class, function (MessageLogged $event) use (&$logged) {
            $logged[] = $event;
        });

        Socialite::shouldReceive('driver->user')->andThrow(new InvalidStateException('internal provider detail'));

        $this->returnFromProvider()
            ->assertRedirect('/login')
            ->assertSessionHas('error', 'We could not sign you in with that account. Please try again.');

        $this->assertGuest();

        $errors = array_filter($logged, fn (MessageLogged $event) => $event->level === 'error');
        $this->assertCount(1, $errors);
        $error = array_values($errors)[0];
        $this->assertSame('OAuth login failed', $error->message);
        $this->assertSame('google', $error->context['provider']);
        $this->assertInstanceOf(InvalidStateException::class, $error->context['exception']);
    }
}
