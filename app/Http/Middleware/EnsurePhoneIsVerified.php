<?php

namespace App\Http\Middleware;

use App\Services\PhoneVerificationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user
            || $user->hasVerifiedPhone()
            || $request->routeIs('user.logout')
            || !PhoneVerificationService::required()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(403, 'Your phone number is not verified.');
        }

        return redirect()->guest(route('phone.notice'));
    }
}
