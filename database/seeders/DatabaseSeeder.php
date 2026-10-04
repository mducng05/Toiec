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
        $admin = User::firstOrCreate(
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

        // Create sample exam if none exists
        if (\App\Models\Exam::count() === 0) {
            $examService = app(\App\Services\ExamService::class);
            $exam = $examService->createExam([
                'title'            => 'ETS TOEIC 2024 — Test 01',
                'description'      => 'Đề thi thử TOEIC chuẩn format 2024 với đầy đủ 7 phần thi (Listening & Reading), có sẵn audio và lời giải chi tiết.',
                'duration_minutes' => 120,
                'is_full_test'     => true,
                'parts'            => [1, 2, 3, 4, 5, 6, 7],
            ], $admin->id);

            $exam->update(['status' => 'published']);
        }
    }
}
