<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show user home page with published exams.
     */
    public function index(): View
    {
        $exams = Exam::published()
            ->withCount('parts')
            ->latest()
            ->paginate(12);

        return view('user.home', compact('exams'));
    }
}
