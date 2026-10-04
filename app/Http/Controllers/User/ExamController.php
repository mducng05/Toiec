<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Services\ExamAttemptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function __construct(
        private ExamAttemptService $attemptService
    ) {}

    /**
     * Exam introduction page.
     */
    public function show(Exam $exam): View
    {
        if ($exam->status !== 'published' && (!auth()->check() || !auth()->user()->isAdmin())) {
            abort(404, 'Đề thi không tồn tại hoặc chưa xuất bản.');
        }

        $exam->load([
            'parts' => fn($q) => $q->withCount('questions')->orderBy('order'),
            'attempts' => fn($q) => $q->where('user_id', auth()->id())->latest(),
        ]);

        $latestAttempt = auth()->check()
            ? $exam->attempts->first()
            : null;

        return view('user.exams.show', compact('exam', 'latestAttempt'));
    }

    /**
     * Start or resume exam attempt.
     */
    public function start(Exam $exam): RedirectResponse
    {
        if ($exam->status !== 'published' && !auth()->user()->isAdmin()) {
            abort(403, 'Đề thi chưa được xuất bản.');
        }

        $attempt = $this->attemptService->startOrResumeAttempt(auth()->user(), $exam);

        return redirect()->route('exams.take', $attempt);
    }

    /**
     * Main exam taking interface (Countdown timer, two-pane layout, 200-question palette).
     */
    public function take(ExamAttempt $attempt): View|RedirectResponse
    {
        if ($attempt->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Bạn không có quyền truy cập bài thi này.');
        }

        if ($attempt->isCompleted()) {
            return redirect()->route('exams.results', $attempt);
        }

        $attempt->load([
            'exam.parts' => fn($q) => $q->with([
                'passages.questions.options',
                'passages.audioFile',
                'questions.options',
                'questions.audioFile',
                'audioFiles',
            ])->orderBy('order'),
            'answers',
        ]);

        $userAnswers = $attempt->answers->keyBy('question_id');

        // Calculate remaining seconds based on duration
        $durationSeconds = $attempt->exam->duration_minutes * 60;
        $elapsedSeconds = $attempt->started_at ? now()->diffInSeconds($attempt->started_at) : 0;
        $remainingSeconds = max(0, $durationSeconds - $elapsedSeconds);

        return view('user.exams.take', compact('attempt', 'userAnswers', 'remainingSeconds'));
    }

    /**
     * Auto-save answer via AJAX.
     */
    public function saveAnswer(Request $request, ExamAttempt $attempt): JsonResponse
    {
        if ($attempt->user_id !== auth()->id() || !$attempt->isInProgress()) {
            return response()->json(['error' => 'Unauthorized or expired attempt'], 403);
        }

        $data = $request->validate([
            'question_id'        => ['required', 'exists:questions,id'],
            'question_option_id' => ['nullable', 'exists:question_options,id'],
            'is_flagged'         => ['nullable', 'boolean'],
        ]);

        $answer = $this->attemptService->saveAnswer(
            $attempt,
            (int) $data['question_id'],
            isset($data['question_option_id']) ? (int) $data['question_option_id'] : null,
            $data['is_flagged'] ?? null
        );

        return response()->json([
            'success'     => true,
            'question_id' => $answer->question_id,
            'option_id'   => $answer->question_option_id,
            'is_flagged'  => $answer->is_flagged,
        ]);
    }

    /**
     * Submit and score the exam.
     */
    public function submit(ExamAttempt $attempt): RedirectResponse
    {
        if ($attempt->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($attempt->isInProgress()) {
            $this->attemptService->submitAttempt($attempt);
        }

        return redirect()
            ->route('exams.results', $attempt)
            ->with('success', 'Nộp bài thi thành công! Dưới đây là kết quả phân tích của bạn.');
    }

    /**
     * View exam result & score report dashboard.
     */
    public function result(ExamAttempt $attempt): View
    {
        if ($attempt->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $attempt->load(['exam.parts', 'result', 'user']);

        return view('user.exams.result', compact('attempt'));
    }

    /**
     * Review full exam answers with explanations.
     */
    public function review(ExamAttempt $attempt): View
    {
        if ($attempt->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $attempt->load([
            'exam.parts' => fn($q) => $q->with([
                'passages.questions.options',
                'passages.audioFile',
                'questions.options',
                'questions.audioFile',
                'audioFiles',
            ])->orderBy('order'),
            'answers',
            'result',
        ]);

        $userAnswers = $attempt->answers->keyBy('question_id');

        return view('user.exams.review', compact('attempt', 'userAnswers'));
    }
}
