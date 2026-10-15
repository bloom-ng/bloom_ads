<?php

namespace App\Services;

use App\Mail\TwoFactorCodeMail;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class TwoFactorCodeService
{
    const CODE_TTL_MINUTES = 10;
    const MAX_ATTEMPTS = 5;
    const MAX_SENDS = 5;
    const SEND_WINDOW_SECONDS = 900;

    /**
     * Apply the send limit and email a fresh 6-digit login code.
     *
     * @throws ValidationException
     */
    public function sendCode(User $user): void
    {
        $limitKey = "2fa-send:{$user->id}";

        if (RateLimiter::tooManyAttempts($limitKey, self::MAX_SENDS)) {
            $minutes = (int) ceil(RateLimiter::availableIn($limitKey) / 60);
            $this->fail('email', "Too many verification codes requested. Please try again in {$minutes} " . ($minutes === 1 ? 'minute' : 'minutes') . '.');
        }

        RateLimiter::hit($limitKey, self::SEND_WINDOW_SECONDS);

        $code = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(self::CODE_TTL_MINUTES);

        Cache::put($this->cacheKey($user), [
            'hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => $expiresAt->getTimestamp(),
        ], $expiresAt);

        try {
            Mail::to($user->email)->send(new TwoFactorCodeMail($code));
        } catch (\Throwable $e) {
            Cache::forget($this->cacheKey($user));
            Log::error('2FA code email failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            $this->fail('email', 'We could not send your verification code. Please try again.');
        }
    }

    /**
     * Check the code; the pending code is discarded once it matches or runs out of attempts.
     *
     * @throws ValidationException
     */
    public function verifyCode(User $user, string $code): void
    {
        $pending = $this->pending($user);

        if (!$pending) {
            $this->fail('code', 'Your code has expired. Please log in again to get a new one.');
        }

        if (!Hash::check($code, $pending['hash'])) {
            $pending['attempts']++;

            if ($pending['attempts'] >= self::MAX_ATTEMPTS) {
                Cache::forget($this->cacheKey($user));
                $this->fail('code', 'Too many incorrect attempts. Please log in again to get a new code.');
            }

            Cache::put($this->cacheKey($user), $pending, Carbon::createFromTimestamp($pending['expires_at']));

            $left = self::MAX_ATTEMPTS - $pending['attempts'];
            $this->fail('code', "Incorrect code. {$left} " . ($left === 1 ? 'attempt' : 'attempts') . ' left.');
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

    protected function cacheKey(User $user): string
    {
        return "2fa-code:{$user->id}";
    }

    /**
     * @throws ValidationException
     */
    protected function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
