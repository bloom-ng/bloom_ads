<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\User;
use App\Models\UserSettings;
use App\Mail\NewUserRegistrationMail;
use App\Services\TwoFactorCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Helpers\CountryHelper;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;

class SignupController extends Controller
{
    const MAX_LOGIN_ATTEMPTS = 5;

    public function index()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.dashboard', ['dark_mode' => $admin->dark_mode]);
    }


    public function register(Request $request)
    {
        // Validate before the try so field errors (e.g. a taken email) reach the form instead of the generic failure
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'user_type' => 'required|in:direct_advertiser,agency,partner',
            'business_name' => session('invite_data') ? 'nullable|string|max:255' : 'required|string|max:255',
            'country_code' => 'required',
            'phone_number' => 'required',
            'country' => 'required',
            'weblink' => $request->user_type === 'direct_advertiser' ? 'nullable' : 'required|url',
            'terms_accepted' => 'required|accepted',
        ]);

        DB::beginTransaction();
        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => $request->user_type,
                'business_name' => $request->business_name,
                'phone_country_code' => $request->country_code,
                'phone' => $request->phone_number,
                'weblink' => $request->weblink ?? "",
                'country' => $request->country
            ]);

            Log::info('User created successfully', ['user_id' => $user->id, 'email' => $user->email]);

            // Check if user is being invited to an organization
            if ($inviteData = session('invite_data')) {
                $organization = Organization::findOrFail($inviteData['organization_id']);

                // Attach user to organization with invited role
                $user->organizations()->attach($organization->id, ['role' => $inviteData['role']]);

                // Create user settings
                $user->settings()->create([
                    'current_organization_id' => $organization->id,
                    'preferences' => UserSettings::getPreferences()
                ]);

                // Mark invite as used
                OrganizationInvite::where('token', $inviteData['token'])->update(['used' => true]);

                // Clear invite data from session
                session()->forget('invite_data');
            } else {
                // Create new organization for non-invited users
                $organization = Organization::create([
                    'name' => $request->business_name,
                    'user_id' => $user->id
                ]);

                // Create user settings
                $user->settings()->create([
                    'current_organization_id' => $organization->id,
                    'preferences' => UserSettings::getPreferences()
                ]);

                // Attach user to organization with 'owner' role
                $user->organizations()->attach($organization->id, ['role' => 'owner']);
            }

            DB::commit();

            // Send verification email
            event(new Registered($user));

            Auth::login($user);

            return redirect()->route('verification.notice');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed: ' . $e->getMessage());
            throw ValidationException::withMessages([
                'email' => ['Registration failed. Please try again.'],
            ]);
        }
    }

    public function login(Request $request, TwoFactorCodeService $twoFactor)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        // Password guesses per email + IP, as in Laravel Breeze's LoginRequest
        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_LOGIN_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->with('error', "Too many login attempts. Please try again in {$seconds} " . ($seconds === 1 ? 'second' : 'seconds') . '.');
        }

        $user = User::where('email', $request->email)->first();

        // One message for both cases so the form can't be used to check which emails have accounts
        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey);

            return back()->with('error', 'Invalid email or password.');
        }

        RateLimiter::clear($throttleKey);

        // Check if 2FA is enabled
        if ($user->settings?->two_factor_enabled) {
            // Emails a hashed, expiring code; throws a validation error if sending is throttled or fails
            $twoFactor->sendCode($user);

            session(['2fa_user_id' => $user->id]);

            // Redirect to the existing 2FA code entry page
            return redirect()->route('2fa.verify');
        }

        // Log the user in if 2FA is not enabled
        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    public function redirectToProvider($provider)
    {
        // Store user type in session, validate it's one of the allowed types
        $userType = request('user_type', 'direct_advertiser');
        if (!in_array($userType, ['direct_advertiser', 'agency', 'partner'])) {
            $userType = 'direct_advertiser'; // default fallback
        }
        session(['oauth_user_type' => $userType]);

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(Request $request, TwoFactorCodeService $twoFactor, $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            // Get user type from session, default to direct_advertiser if not set
            $userType = session('oauth_user_type', 'direct_advertiser');
            session()->forget('oauth_user_type');

            // Find existing user or create new one
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                return redirect('/login')->with('error', 'User not found, please signup');
                // DB::beginTransaction();
                // try {
                //     // Create user with the specific user type
                //     $user = User::create([
                //         'name' => $socialUser->getName(),
                //         'email' => $socialUser->getEmail(),
                //         'user_type' => $userType,
                //         'password' => Hash::make(Str::random(24)),
                //         'email_verified_at' => now(),
                //         'oauth_id' => $socialUser->getId(),
                //         'oauth_provider' => $provider,
                //     ]);

                //     // Create organization
                //     $organization = Organization::create([
                //         'name' => $user->name . "'s Organization",
                //         'user_id' => $user->id
                //     ]);

                //     // Create user settings
                //     UserSettings::create([
                //         'user_id' => $user->id,
                //         'current_organization_id' => $organization->id,
                //         'preferences' => UserSettings::getPreferences()
                //     ]);

                //     // Attach user to organization with 'owner' role
                //     $user->organizations()->attach($organization->id, ['role' => 'owner']);

                //     DB::commit();
                // } catch (\Exception $e) {
                //     DB::rollBack();
                //     throw $e;
                // }
            }

            // Same 2FA step as password login
            if ($user->settings?->two_factor_enabled) {
                $twoFactor->sendCode($user);

                session(['2fa_user_id' => $user->id]);

                return redirect()->route('2fa.verify');
            }

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        } catch (ValidationException $e) {
            // Code sending throttled or failed; back() would point at the provider, so go to the login page
            return redirect('/login')->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('OAuth login failed', ['provider' => $provider, 'exception' => $e]);

            return redirect('/login')
                ->with('error', 'We could not sign you in with that account. Please try again.');
        }
    }

    public function inviteSignup(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required'
        ]);

        // Verify invite token
        $invite = OrganizationInvite::where('token', $request->token)
            ->where('email', $request->email)
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->firstOrFail();

        // Check if user already exists
        $existingUser = User::where('email', $invite->email)->first();
        if ($existingUser) {
            // Add user to organization
            $invite->organization->users()->attach($existingUser->id, ['role' => $invite->role]);

            // Mark invite as used
            $invite->update(['used' => true]);

            // Log the user in
            Auth::login($existingUser);
            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('success', 'You have been added to the organization.');
        }

        // If user doesn't exist, redirect to registration with invite data
        return redirect()->route('complete-registration', ['email' => $request->email]);
    }

    public function showSignup1()
    {
        $countryCodes = CountryHelper::getCountryCodes();
        $countries = CountryHelper::getCountries();
        return view('signup1', compact('countryCodes', 'countries'));
    }

    public function showSignup2()
    {
        $countryCodes = CountryHelper::getCountryCodes();
        $countries = CountryHelper::getCountries();
        return view('signup2', compact('countryCodes', 'countries'));
    }

    public function showSignup3()
    {
        $countryCodes = CountryHelper::getCountryCodes();
        $countries = CountryHelper::getCountries();
        return view('signup3', compact('countryCodes', 'countries'));
    }

    public function verify2fa(Request $request, TwoFactorCodeService $twoFactor)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = User::find(session('2fa_user_id'));

        if (!$user) {
            return redirect('/login')->withErrors(['code' => 'Your code has expired. Please log in again to get a new one.']);
        }

        try {
            $twoFactor->verifyCode($user, $request->code);
        } catch (ValidationException $e) {
            // Expired or out of attempts: a new code only comes from logging in again
            if (!$twoFactor->pending($user)) {
                session()->forget('2fa_user_id');

                return redirect('/login')->withErrors($e->errors());
            }

            return back()->withErrors($e->errors());
        }

        session()->forget('2fa_user_id');

        Auth::login($user);
        $request->session()->regenerate();

        // Redirect to the dashboard
        return redirect()->intended('dashboard');
    }

    public function showInviteSignup(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required'
        ]);

        // Verify invite token
        $invite = OrganizationInvite::where('token', $request->token)
            ->where('email', $request->email)
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->firstOrFail();

        // Store invite data in session
        session([
            'invite_data' => [
                'organization_id' => $invite->organization_id,
                'role' => $invite->role,
                'email' => $invite->email,
                'token' => $invite->token
            ]
        ]);

        // Get country codes and countries for the form
        $countryCodes = CountryHelper::getCountryCodes();
        $countries = CountryHelper::getCountries();

        return view('signup1', compact('countryCodes', 'countries'));
    }
}
