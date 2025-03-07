<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UsersController extends Controller
{
    public function index()
    {
        $my_date = Carbon::now()->format('l, F j, Y');
        $users = User::orderBy('created_at', 'desc')  // Optional: sort by creation date
            ->paginate(10);  // This will paginate with 1 user per page
    
        return view('admin-dashboard.users.index', compact('users', 'my_date'));
    }

    public function createInitialUser()
    {
        User::create([
            'name' => 'Olusanu Emmanuel',
            'email' => 'olusanuemmanuel@gmail.com',
            'password' => Hash::make('Password100%'),
            'business_name' => 'Lekan logistics',
            'phone_country_code' => '+234',
            'phone' => '7067531320',
            'weblink' => null,
            'country' => 'Nigeria'
        ]);

        return redirect()->route('users.index')->with('success', 'Initial user created successfully');
    }

    public function edit(User $user)
    {
        return view('admin-dashboard.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'business_name' => 'nullable|string|max:255',
            'phone_country_code' => 'nullable|string|max:5',
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }

    public function account()
    {
        return view('dashboard.account', [
            'user' => auth()->user(),
            'title' => 'Account Settings',
            'page' => 'account'
        ]);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        auth()->user()->update($validated);

        return redirect()->route('account')
            ->with('success', 'Profile updated successfully');
    }

    public function updateDarkMode(Request $request)
    {
        $user = Auth::user();
        $user->dark_mode = $request->dark_mode;
        $user->save();

        return response()->json(['success' => true]);
    }
} 