<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء مسؤول باستخدام updateOrCreate لمنع التكرار
        User::updateOrCreate(
            ['email' => 'admin@events.com'],
            [
                'name' => 'Event Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'phone' => '+1234567890'
            ]
        );

        // إنشاء مستخدم عادي
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'phone' => '+1234567891'
            ]
        );
    }
}