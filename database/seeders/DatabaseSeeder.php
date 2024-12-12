<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            // ... other seeders
        ]);

        $user = User::first();
        
        if ($user) {
            $org = Organization::create([
                'name' => 'Default Organization',
                'user_id' => $user->id
            ]);
            
            $user->update(['current_organization_id' => $org->id]);
        }
    }
}
