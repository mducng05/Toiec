<?php

namespace App\Services;

use App\Models\AttemptResult;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\UserAnswer;
use Illuminate\Support\Facades\DB;

class ScoringService
{
    /**
     * Standard TOEIC Listening Conversion Table (Raw 0-100 to Scaled 5-495).
     */
    private const LISTENING_SCALE = [
        0 => 5, 1 => 5, 2 => 5, 3 => 5, 4 => 5, 5 => 5, 6 => 10, 7 => 15, 8 => 20, 9 => 25,
        10 => 30, 11 => 35, 12 => 40, 13 => 45, 14 => 50, 15 => 55, 16 => 60, 17 => 65, 18 => 70, 19 => 75,
        20 => 80, 21 => 85, 22 => 90, 23 => 95, 24 => 100, 25 => 110, 26 => 115, 27 => 120, 28 => 125, 29 => 130,
        30 => 135, 31 => 140, 32 => 145, 33 => 150, 34 => 160, 35 => 165, 36 => 170, 37 => 175, 38 => 180, 39 => 185,
        40 => 190, 41 => 195, 42 => 200, 43 => 210, 44 => 215, 45 => 220, 46 => 225, 47 => 230, 48 => 235, 49 => 240,
        50 => 245, 51 => 250, 52 => 255, 53 => 260, 54 => 265, 55 => 270, 56 => 275, 57 => 280, 58 => 285, 59 => 290,
        60 => 295, 61 => 300, 62 => 305, 63 => 310, 64 => 320, 65 => 325, 66 => 330, 67 => 335, 68 => 340, 69 => 345,
        70 => 350, 71 => 355, 72 => 360, 73 => 365, 74 => 370, 75 => 375, 76 => 380, 77 => 385, 78 => 390, 79 => 395,
        80 => 400, 81 => 405, 82 => 410, 83 => 415, 84 => 420, 85 => 425, 86 => 430, 87 => 435, 88 => 440, 89 => 445,
        90 => 450, 91 => 455, 92 => 460, 93 => 465, 94 => 470, 95 => 475, 96 => 480, 97 => 485, 98 => 490, 99 => 495, 100 => 495
    ];

    /**
     * Standard TOEIC Reading Conversion Table (Raw 0-100 to Scaled 5-495).
     */
    private const READING_SCALE = [
        0 => 5, 1 => 5, 2 => 5, 3 => 5, 4 => 5, 5 => 5, 6 => 5, 7 => 5, 8 => 5, 9 => 10,
        10 => 15, 11 => 20, 12 => 25, 13 => 30, 14 => 35, 15 => 40, 16 => 45, 17 => 50, 18 => 55, 19 => 60,
        20 => 65, 21 => 70, 22 => 75, 23 => 80, 24 => 85, 25 => 90, 26 => 95, 27 => 100, 28 => 105, 29 => 110,
        30 => 115, 31 => 120, 32 => 125, 33 => 130, 34 => 135, 35 => 140, 36 => 145, 37 => 150, 38 => 155, 39 => 160,
        40 => 165, 41 => 170, 42 => 175, 43 => 180, 44 => 185, 45 => 190, 46 => 195, 47 => 200, 48 => 205, 49 => 210,
        50 => 215, 51 => 220, 52 => 225, 53 => 230, 54 => 235, 55 => 240, 56 => 245, 57 => 250, 58 => 255, 59 => 260,
        60 => 265, 61 => 270, 62 => 275, 63 => 280, 64 => 285, 65 => 290, 66 => 295, 67 => 300, 68 => 305, 69 => 310,
        70 => 315, 71 => 320, 72 => 325, 73 => 330, 74 => 335, 75 => 340, 76 => 345, 77 => 350, 78 => 355, 79 => 360,
        80 => 365, 81 => 370, 82 => 375, 83 => 380, 84 => 385, 85 => 390, 86 => 395, 87 => 400, 88 => 405, 89 => 410,
        90 => 415, 91 => 420, 92 => 425, 93 => 430, 94 => 435, 95 => 440, 96 => 445, 97 => 455, 98 => 470, 99 => 485, 100 => 495
    ];

    /**
     * Score an exam attempt.
     */
    public function scoreAttempt(ExamAttempt $attempt): AttemptResult
    {
        return DB::transaction(function () use ($attempt) {
            $exam = $attempt->exam()->with(['parts.questions.options'])->first();
            $answers = $attempt->answers()->get()->keyBy('question_id');

            $totalQuestions = 0;
            $correctAnswers = 0;
            $wrongAnswers = 0;
            $unanswered = 0;
            $listeningCorrect = 0;
            $readingCorrect = 0;
            $totalListeningQuestions = 0;
            $totalReadingQuestions = 0;
            $partResults = [];

            foreach ($exam->parts as $part) {
                $pNum = $part->part_number;
                $isListening = ($part->section === 'listening');
                $partTotal = 0;
                $partCorrect = 0;
                $partWrong = 0;
                $partUnanswered = 0;

                foreach ($part->questions as $question) {
                    $totalQuestions++;
                    $partTotal++;

                    if ($isListening) {
                        $totalListeningQuestions++;
                    } else {
                        $totalReadingQuestions++;
                    }

                    $correctOption = $question->options->firstWhere('is_correct', true);
                    $userAnswer = $answers->get($question->id);

                    if (!$userAnswer || !$userAnswer->question_option_id) {
                        $unanswered++;
                        $partUnanswered++;
                    } elseif ($correctOption && $userAnswer->question_option_id === $correctOption->id) {
                        $correctAnswers++;
                        $partCorrect++;
                        if ($isListening) {
                            $listeningCorrect++;
                        } else {
                            $readingCorrect++;
                        }
                    } else {
                        $wrongAnswers++;
                        $partWrong++;
                    }
                }

                $partAccuracy = $partTotal > 0 ? round(($partCorrect / $partTotal) * 100, 1) : 0;

                $partResults["part_{$pNum}"] = [
                    'part_number' => $pNum,
                    'title'       => $part->title,
                    'section'     => $part->section,
                    'total'       => $partTotal,
                    'correct'     => $partCorrect,
                    'wrong'       => $partWrong,
                    'unanswered'  => $partUnanswered,
                    'accuracy'    => $partAccuracy,
                ];
            }

            $accuracy = $totalQuestions > 0
                ? round(($correctAnswers / $totalQuestions) * 100, 2)
                : 0.00;

            // Calculate scaled scores
            $listeningScore = $this->calculateScaledScore($listeningCorrect, $totalListeningQuestions, self::LISTENING_SCALE);
            $readingScore = $this->calculateScaledScore($readingCorrect, $totalReadingQuestions, self::READING_SCALE);
            $totalScore = $listeningScore + $readingScore;

            // Mark attempt complete
            $completedAt = now();
            $timeSpent = $attempt->started_at ? max(0, (int) abs($attempt->started_at->diffInSeconds($completedAt))) : 0;

            $attempt->update([
                'status'             => 'completed',
                'completed_at'       => $completedAt,
                'time_spent_seconds' => $timeSpent,
            ]);

            // Save AttemptResult
            return AttemptResult::updateOrCreate(
                ['exam_attempt_id' => $attempt->id],
                [
                    'total_questions'     => $totalQuestions,
                    'correct_answers'     => $correctAnswers,
                    'wrong_answers'       => $wrongAnswers,
                    'unanswered'          => $unanswered,
                    'listening_correct'   => $listeningCorrect,
                    'reading_correct'     => $readingCorrect,
                    'listening_score'     => $listeningScore,
                    'reading_score'       => $readingScore,
                    'total_score'         => $totalScore,
                    'accuracy_percentage' => $accuracy,
                    'part_results'        => $partResults,
                    'scored_at'           => now(),
                ]
            );
        });
    }

    /**
     * Map raw correct to standard scaled score (0-100 base).
     */
    private function calculateScaledScore(int $correct, int $total, array $scaleTable): int
    {
        if ($total <= 0) {
            return 0;
        }

        // Scale raw count to 100-item standard if part test / mini test
        $normalizedRaw = ($total === 100)
            ? $correct
            : (int) round(($correct / $total) * 100);

        $normalizedRaw = max(0, min(100, $normalizedRaw));

        return $scaleTable[$normalizedRaw] ?? 5;
    }
}
