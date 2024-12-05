<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Helpers\CountryHelper;

class SignupController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'user_type' => 'required|in:direct_advertiser,agency,partner',
            'business_name' => 'required|string|max:255',
            'country_code' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'weblink' => 'required|url|max:255',
            'country' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'business_name' => $request->business_name,
            'phone_country_code' => $request->country_code,
            'phone' => $request->phone_number,
            'weblink' => $request->weblink,
            'country' => $request->country,
        ]);

        $organisation = new Organization();

        $organisation->name = $request->business_name;
        $organisation->save();

        $user->organizations()->attach($organisation->id, ['role' => 'owner']);

        Auth::login($user);

        return redirect('/dashboard');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Invalid email');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Incorrect password');
        }


        Auth::login($user);

        return redirect('/dashboard');
    }

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            $user = User::where('oauth_id', $socialUser->getId())
                ->where('oauth_provider', $provider)
                ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'oauth_id' => $socialUser->getId(),
                    'oauth_provider' => $provider,
                ]);
            }

            Auth::login($user);
            return redirect('/dashboard');
        } catch (\Exception $e) {
            return redirect('/signup')->with('error', 'OAuth sign in failed');
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
            ->first();

        if (!$invite) {
            return back()->with('error', 'Invalid invite');
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $user = User::create([
                'email' => $request->email,
                'user_type' => 'direct_advertiser',
                // Other fields will be filled during registration
            ]);
        }

        // Mark invite as used
        $invite->update(['used' => true]);

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
}
