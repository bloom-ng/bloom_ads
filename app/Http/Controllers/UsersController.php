<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersController extends Controller
{
    public function index()
    {
        $my_date = Carbon::now()->format('l, F j, Y');
        $users = User::all();
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
} 