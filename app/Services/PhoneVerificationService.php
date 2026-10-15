<?php

namespace App\Services;

use App\Helpers\PhoneHelper;
use App\Models\AdminSetting;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class PhoneVerificationService
{
    const CODE_TTL_MINUTES = 10;
    const MAX_ATTEMPTS = 5;
    const RESEND_COOLDOWN_SECONDS = 60;

    protected $brevo;

    public function __construct(BrevoService $brevo)
    {
        $this->brevo = $brevo;
    }

    /**
     * Whether unverified users are blocked (Admin Settings: phone_verification_required).
     */
    public static function required(): bool
    {
        return filter_var(AdminSetting::get('phone_verification_required', '0'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Normalise the number, apply rate limits and SMS a fresh 6-digit code.
     *
     * @throws ValidationException
     */
    public function sendCode(User $user, string $countryCode, string $number, ?string $ip = null): array
    {
        $e164 = PhoneHelper::toE164($countryCode, $number);

        if (!$e164) {
            $this->fail('phone_number', 'Please enter a valid mobile number for the selected country.');
        }

        $this->ensureNumberIsAvailable($user, $e164);

        $limits = [
            "phone-otp-resend:{$user->id}" => [1, self::RESEND_COOLDOWN_SECONDS],
            "phone-otp-user:{$user->id}" => [5, 3600],
            "phone-otp-number:{$e164}" => [5, 3600],
        ];
        if ($ip) {
            $limits["phone-otp-ip:{$ip}"] = [20, 3600];
        }

        foreach ($limits as $key => [$max]) {
            if (RateLimiter::tooManyAttempts($key, $max)) {
                $this->fail('phone_number', 'Too many code requests. Please try again in ' . $this->humanSeconds(RateLimiter::availableIn($key)) . '.');
            }
        }

        foreach ($limits as $key => [, $decay]) {
            RateLimiter::hit($key, $decay);
        }

        $code = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(self::CODE_TTL_MINUTES);

        $pending = [
            'country_code' => $countryCode,
            'phone' => PhoneHelper::nationalNumber($e164),
            'e164' => $e164,
            'hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => $expiresAt->getTimestamp(),
        ];

        Cache::put($this->cacheKey($user), $pending, $expiresAt);

        $sent = $this->brevo->sendSms(
            $e164,
            'Your ' . config('app.name') . ' verification code is ' . $code . '. It expires in ' . self::CODE_TTL_MINUTES . ' minutes. Do not share it.'
        );

        if (!$sent) {
            Cache::forget($this->cacheKey($user));
            RateLimiter::clear("phone-otp-resend:{$user->id}");
            $this->fail('phone_number', 'We could not send the SMS right now. Please check the number and try again.');
        }

        return $pending;
    }

    /**
     * Check the code and, if it matches, save the number as verified on the user.
     *
     * @throws ValidationException
     */
    public function verifyCode(User $user, string $code): void
    {
        $pending = $this->pending($user);

        if (!$pending) {
            $this->fail('code', 'Your code has expired. Please request a new one.');
        }

        if (!Hash::check($code, $pending['hash'])) {
            $pending['attempts']++;

            if ($pending['attempts'] >= self::MAX_ATTEMPTS) {
                Cache::forget($this->cacheKey($user));
                $this->fail('code', 'Too many incorrect attempts. Please request a new code.');
            }

            Cache::put($this->cacheKey($user), $pending, Carbon::createFromTimestamp($pending['expires_at']));

            $left = self::MAX_ATTEMPTS - $pending['attempts'];
            $this->fail('code', "Incorrect code. {$left} " . ($left === 1 ? 'attempt' : 'attempts') . ' left.');
        }

        $this->ensureNumberIsAvailable($user, $pending['e164']);

        try {
            $user->markPhoneAsVerified($pending['country_code'], $pending['phone'], $pending['e164']);
        } catch (UniqueConstraintViolationException $e) {
            $this->fail('code', $this->takenMessage());
        }

        Cache::forget($this->cacheKey($user));
    }

    /**
     * The code currently awaiting entry for this user, if any.
     */
    public function pending(User $user): ?array
    {
        return Cache::get($this->cacheKey($user));
    }

    public function cancel(User $user): void
    {
        Cache::forget($this->cacheKey($user));
    }

    public function resendAvailableIn(User $user): int
    {
        $key = "phone-otp-resend:{$user->id}";

        return RateLimiter::tooManyAttempts($key, 1) ? RateLimiter::availableIn($key) : 0;
    }

    protected function ensureNumberIsAvailable(User $user, string $e164): void
    {
        $taken = User::where('verified_phone', $e164)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($taken) {
            $this->fail('phone_number', $this->takenMessage());
        }
    }

    protected function takenMessage(): string
    {
        return 'This phone number is already linked to another account. Please use a different number or contact support.';
    }

    protected function cacheKey(User $user): string
    {
        return "phone-otp:{$user->id}";
    }

    protected function humanSeconds(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' ' . ($seconds === 1 ? 'second' : 'seconds');
        }

        $minutes = (int) ceil($seconds / 60);

        return $minutes . ' ' . ($minutes === 1 ? 'minute' : 'minutes');
    }

    /**
     * @throws ValidationException
     */
    protected function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
