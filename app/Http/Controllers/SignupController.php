<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\User;
use App\Models\UserSettings;
use App\Mail\NewUserRegistrationMail;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Helpers\CountryHelper;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;

class SignupController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.dashboard', ['dark_mode' => $admin->dark_mode]);
    }


    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
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

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'User not found');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Incorrect password');
        }

        session(['user_id' => $user->id]);

        // Check if 2FA is enabled
        if ($user->settings->two_factor_enabled) {
            // Generate a random 6-digit code
            $code = rand(100000, 999999); // Secure random 6-digit code

            // Store the code in the session for verification later
            session(['2fa_code' => $code]);
            Log::info('2FA Code Stored in Session: ' . session('2fa_code'));


            // Send the code to the user's email
            Mail::to($user->email)->send(new TwoFactorCodeMail($code));

            // Redirect to the existing 2FA code entry page
            return redirect()->route('2fa.verify');
        }


        // Log the user in if 2FA is not enabled
        Auth::login($user);
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

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            // Get user type from session, default to direct_advertiser if not set
            $userType = session('oauth_user_type', 'direct_advertiser');

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

            Auth::login($user);

            // Clear the session
            session()->forget('oauth_user_type');

            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            return redirect('/login')
                ->with('error', 'OAuth error: ' . $e->getMessage());
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

    public function verify2fa(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        Log::info('Auth User:', ['user' => Auth::user()]);
        Log::info('Session Data Before Verification:', session()->all());
        // Check if the code matches the one stored in the session
        Log::info('Full Session Data: ' . json_encode(session()->all()));

        // Check if user is authenticated
        if ((string) $request->code === (string) session('2fa_code')) {
            // Clear the session code
            session()->forget('2fa_code');

            // Re-authenticate the user
            Auth::loginUsingId(session('user_id'));

            // Redirect to the dashboard
            return redirect()->intended('dashboard');
        }
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
