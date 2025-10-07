<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin
        User::updateOrCreate(
            ['email' => 'admin@events.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'phone' => null,
                'is_admin' => true,
            ]
        );

        // Create regular user
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'phone' => null,
                'is_admin' => false,
            ]
        );
    }
}
