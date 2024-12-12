<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class InitialUserSeeder extends Seeder
{
    public function run()
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
    }
} 