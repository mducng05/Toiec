<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use App\Models\UserAnswer;

class ExamAttemptService
{
    public function __construct(
        private ScoringService $scoringService
    ) {}

    /**
     * Start a new exam attempt or resume an existing in-progress attempt.
     */
    public function startOrResumeAttempt(User $user, Exam $exam): ExamAttempt
    {
        // Check for in-progress attempt
        $attempt = ExamAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->latest()
            ->first();

        if ($attempt) {
            return $attempt;
        }

        return ExamAttempt::create([
            'user_id'    => $user->id,
            'exam_id'    => $exam->id,
            'status'     => 'in_progress',
            'started_at' => now(),
        ]);
    }

    /**
     * Auto-save a user's answer (AJAX).
     */
    public function saveAnswer(ExamAttempt $attempt, int $questionId, ?int $optionId, ?bool $isFlagged = null): UserAnswer
    {
        $data = [
            'answered_at' => now(),
        ];

        if ($optionId !== null) {
            $data['question_option_id'] = $optionId;
        }

        if ($isFlagged !== null) {
            $data['is_flagged'] = $isFlagged;
        }

        return UserAnswer::updateOrCreate(
            [
                'exam_attempt_id' => $attempt->id,
                'question_id'     => $questionId,
            ],
            $data
        );
    }

    /**
     * Submit and score the exam attempt.
     */
    public function submitAttempt(ExamAttempt $attempt): \App\Models\AttemptResult
    {
        return $this->scoringService->scoreAttempt($attempt);
    }
}
