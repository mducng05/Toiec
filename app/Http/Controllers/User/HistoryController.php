<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use Illuminate\View\View;

class HistoryController extends Controller
{
    /**
     * Display user's exam attempt history.
     */
    public function index(): View
    {
        $attempts = ExamAttempt::where('user_id', auth()->id())
            ->with(['exam', 'result'])
            ->latest()
            ->paginate(15);

        return view('user.history.index', compact('attempts'));
    }
}
