<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard with basic stats.
     */
    public function index(): View
    {
        $stats = [
            'total_exams' => Exam::count(),
            'published_exams' => Exam::where('status', 'published')->count(),
            'total_users' => User::where('role', 'user')->count(),
            'total_attempts' => ExamAttempt::where('status', 'completed')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
