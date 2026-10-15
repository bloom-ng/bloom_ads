<?php

namespace App\Http\Controllers;

use App\Helpers\CountryHelper;
use App\Helpers\PhoneHelper;
use App\Services\PhoneVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class PhoneVerificationController extends Controller
{
    public function show(Request $request, PhoneVerificationService $verification)
    {
        $user = $request->user();
        $pending = $verification->pending($user);

        return view('auth.verify-phone', [
            'user' => $user,
            'countryCodes' => CountryHelper::getCountryCodes(),
            'pending' => $pending ? Arr::only($pending, ['country_code', 'phone']) : null,
            'pendingNumber' => $pending ? PhoneHelper::mask($pending['e164']) : null,
            'resendIn' => $verification->resendAvailableIn($user),
        ]);
    }

    public function send(Request $request, PhoneVerificationService $verification)
    {
        $request->validate([
            'country_code' => ['required', Rule::in(array_column(CountryHelper::getCountryCodes(), 'code'))],
            'phone_number' => 'required|string|max:20',
        ]);

        $verification->sendCode($request->user(), $request->country_code, $request->phone_number, $request->ip());

        return redirect()->route('phone.notice')
            ->with('success', 'We sent a 6-digit code to your phone.');
    }

    public function verify(Request $request, PhoneVerificationService $verification)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = $request->user();
        $wasVerified = $user->hasVerifiedPhone();

        $verification->verifyCode($user, $request->code);

        // Verified users come here from Account settings to change their number
        if ($wasVerified) {
            return redirect()->route('account')->with('success', 'Phone number updated');
        }

        return redirect()->intended(route('dashboard'))->with('success', 'Phone number verified');
    }
}
