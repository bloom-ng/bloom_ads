<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\AdminSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PhoneVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.brevo.api_key' => 'test-key',
            'services.brevo.sms_sender' => 'BloomAds',
        ]);

        AdminSetting::set('phone_verification_required', '1');

        // Same middleware stack as the dashboard group, without the dashboard's own dependencies
        Route::middleware(['web', 'auth', 'verified', 'phone.verified'])
            ->get('/_test/gated', fn () => 'ok');
    }

    protected function fakeBrevo(int $status = 201): void
    {
        Http::fake([
            'api.brevo.com/*' => Http::response($status === 201 ? ['messageId' => 1511882900100020] : ['code' => 'invalid_parameter'], $status),
        ]);
    }

    protected function makeUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'user_type' => 'direct_advertiser',
            'phone_country_code' => '+234',
            'phone' => '07067531320',
        ], $attributes));
    }

    protected function sendCode(string $number = '07067531320', string $countryCode = '+234')
    {
        return $this->post(route('phone.send'), [
            'country_code' => $countryCode,
            'phone_number' => $number,
        ]);
    }

    protected function lastSentCode(): string
    {
        $request = Http::recorded()->last()[0];
        preg_match('/code is (\d{6})/', $request['content'], $matches);

        return $matches[1];
    }

    public function test_dashboard_routes_require_a_verified_phone(): void
    {
        $middleware = app('router')->getRoutes()->getByName('dashboard')->gatherMiddleware();

        $this->assertContains('phone.verified', $middleware);
    }

    public function test_unverified_user_is_redirected_to_phone_verification(): void
    {
        $this->actingAs($this->makeUser())
            ->get('/_test/gated')
            ->assertRedirect(route('phone.notice'));
    }

    public function test_json_requests_from_unverified_users_get_403(): void
    {
        $this->actingAs($this->makeUser())
            ->getJson('/_test/gated')
            ->assertForbidden();
    }

    public function test_gate_is_lifted_when_admin_setting_is_off(): void
    {
        AdminSetting::set('phone_verification_required', '0');

        $this->actingAs($this->makeUser())
            ->get('/_test/gated')
            ->assertOk();
    }

    public function test_unverified_user_can_still_log_out(): void
    {
        $this->actingAs($this->makeUser())
            ->post(route('user.logout'))
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_email_must_be_verified_before_phone(): void
    {
        $this->actingAs($this->makeUser(['email_verified_at' => null]))
            ->get(route('phone.notice'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verification_page_renders(): void
    {
        $this->actingAs($this->makeUser())
            ->get(route('phone.notice'))
            ->assertOk()
            ->assertSee('Verify Your Phone Number')
            ->assertSee('07067531320');
    }

    public function test_sends_code_through_brevo(): void
    {
        $this->fakeBrevo();

        $this->actingAs($this->makeUser());
        $this->sendCode()
            ->assertRedirect(route('phone.notice'))
            ->assertSessionHasNoErrors();

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://api.brevo.com/v3/transactionalSMS/send'
                && $request->hasHeader('api-key', 'test-key')
                && $request['sender'] === 'BloomAds'
                && $request['recipient'] === '2347067531320'
                && $request['type'] === 'transactional'
                && preg_match('/code is \d{6}\./', $request['content']);
        });

        $this->get(route('phone.notice'))->assertSee('+234 ••• ••• 1320');
    }

    public function test_invalid_number_is_rejected_without_sending(): void
    {
        $this->fakeBrevo();

        $this->actingAs($this->makeUser());
        $this->sendCode('12345')->assertSessionHasErrors('phone_number');

        Http::assertNothingSent();
    }

    public function test_unsupported_country_code_is_rejected(): void
    {
        $this->fakeBrevo();

        $this->actingAs($this->makeUser());
        $this->sendCode('7067531320', '+999')->assertSessionHasErrors('country_code');

        Http::assertNothingSent();
    }

    public function test_resend_is_throttled(): void
    {
        $this->fakeBrevo();

        $this->actingAs($this->makeUser());
        $this->sendCode()->assertSessionHasNoErrors();
        $this->sendCode()->assertSessionHasErrors('phone_number');

        Http::assertSentCount(1);

        $this->travel(61)->seconds();
        $this->sendCode()->assertSessionHasNoErrors();

        Http::assertSentCount(2);
    }

    public function test_brevo_failure_shows_error_and_allows_retry(): void
    {
        $this->fakeBrevo(400);

        $this->actingAs($this->makeUser());
        $this->sendCode()->assertSessionHasErrors('phone_number');
        $this->sendCode()->assertSessionHasErrors('phone_number');

        // Failed sends don't start the resend cooldown
        Http::assertSentCount(2);
    }

    public function test_correct_code_verifies_number_and_returns_to_intended_page(): void
    {
        $this->fakeBrevo();
        $user = $this->makeUser();

        $this->actingAs($user)->get('/_test/gated')->assertRedirect(route('phone.notice'));
        $this->sendCode('0706 753 1320');

        $this->post(route('phone.verify'), ['code' => $this->lastSentCode()])
            ->assertRedirect('/_test/gated')
            ->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertTrue($user->hasVerifiedPhone());
        $this->assertSame('+2347067531320', $user->verified_phone);
        $this->assertSame('+234', $user->phone_country_code);
        $this->assertSame('7067531320', $user->phone);

        $this->get('/_test/gated')->assertOk();
    }

    public function test_wrong_codes_are_limited(): void
    {
        $this->fakeBrevo();
        $user = $this->makeUser();

        $this->actingAs($user);
        $this->sendCode();
        $code = $this->lastSentCode();

        for ($i = 1; $i <= 3; $i++) {
            $this->post(route('phone.verify'), ['code' => '000000'])->assertSessionHasErrors('code');
        }

        $this->post(route('phone.verify'), ['code' => '000000'])
            ->assertSessionHasErrors(['code' => 'Incorrect code. 1 attempt left.']);

        $this->post(route('phone.verify'), ['code' => '000000'])
            ->assertSessionHasErrors(['code' => 'Too many incorrect attempts. Please request a new code.']);

        // The real code no longer works either
        $this->post(route('phone.verify'), ['code' => $code])
            ->assertSessionHasErrors(['code' => 'Your code has expired. Please request a new one.']);

        $this->assertFalse($user->fresh()->hasVerifiedPhone());
    }

    public function test_code_expires_after_ten_minutes(): void
    {
        $this->fakeBrevo();
        $user = $this->makeUser();

        $this->actingAs($user);
        $this->sendCode();
        $code = $this->lastSentCode();

        $this->travel(11)->minutes();

        $this->post(route('phone.verify'), ['code' => $code])
            ->assertSessionHasErrors(['code' => 'Your code has expired. Please request a new one.']);

        $this->assertFalse($user->fresh()->hasVerifiedPhone());
    }

    public function test_number_verified_on_another_account_is_rejected(): void
    {
        $this->fakeBrevo();
        $this->makeUser()->markPhoneAsVerified('+234', '7067531320', '+2347067531320');

        $this->actingAs($this->makeUser());
        $this->sendCode('07067531320')->assertSessionHasErrors('phone_number');

        Http::assertNothingSent();
    }

    public function test_verified_user_keeps_old_number_until_new_one_is_confirmed(): void
    {
        $this->fakeBrevo();
        $user = $this->makeUser();
        $user->markPhoneAsVerified('+234', '7067531320', '+2347067531320');

        $this->actingAs($user)->get(route('phone.notice'))->assertSee('Change Your Phone Number');
        $this->sendCode('08031234567');

        $this->assertSame('+2347067531320', $user->fresh()->verified_phone);

        $this->post(route('phone.verify'), ['code' => $this->lastSentCode()])
            ->assertRedirect(route('account'));

        $this->assertSame('+2348031234567', $user->fresh()->verified_phone);
        $this->assertSame('8031234567', $user->fresh()->phone);
    }

    public function test_admin_phone_change_resets_verification(): void
    {
        $user = $this->makeUser();
        $user->markPhoneAsVerified('+234', '7067531320', '+2347067531320');
        $admin = Admin::create(['email' => 'admin@example.com', 'password' => 'password123']);

        $payload = [
            'name' => $user->name,
            'email' => $user->email,
            'phone_country_code' => '+234',
            'phone' => '7067531320',
        ];

        // Saving without touching the number keeps it verified
        $this->actingAs($admin, 'admin')
            ->put(route('admin.users.update', $user), $payload)
            ->assertRedirect(route('admin.users.index'));
        $this->assertTrue($user->fresh()->hasVerifiedPhone());

        $this->actingAs($admin, 'admin')
            ->put(route('admin.users.update', $user), array_merge($payload, ['phone' => '8031234567']))
            ->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertFalse($user->hasVerifiedPhone());
        $this->assertNull($user->verified_phone);
    }
}
