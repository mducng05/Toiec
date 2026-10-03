<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin account
        User::firstOrCreate(
            ['email' => 'admin@toeic.local'],
            [
                'name' => 'Admin',
                'email' => 'admin@toeic.local',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Create a demo user account
        User::firstOrCreate(
            ['email' => 'user@toeic.local'],
            [
                'name' => 'Demo User',
                'email' => 'user@toeic.local',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );
    }
}
