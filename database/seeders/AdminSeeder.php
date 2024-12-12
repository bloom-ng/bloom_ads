<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'email' => 'admin@bloomdigitmedia.com',
            'password' => Hash::make('Password100%'),
            'email_verified_at' => now(),
        ]);
    }
} 