<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamPart;
use Illuminate\Support\Facades\DB;

class ExamService
{
    /**
     * Create a new exam with default parts.
     *
     * @param  array  $data  Validated data from ExamRequest
     * @param  int    $userId
     * @return Exam
     */
    public function createExam(array $data, int $userId): Exam
    {
        return DB::transaction(function () use ($data, $userId) {
            $exam = Exam::create([
                'user_id'          => $userId,
                'title'            => $data['title'],
                'description'      => $data['description'] ?? null,
                'duration_minutes' => $data['duration_minutes'] ?? 120,
                'status'           => 'draft',
                'is_full_test'     => $data['is_full_test'] ?? false,
            ]);

            // Auto-create selected parts
            if (!empty($data['parts'])) {
                foreach ($data['parts'] as $partNumber) {
                    $partNumber = (int) $partNumber;
                    ExamPart::create([
                        'exam_id'     => $exam->id,
                        'part_number' => $partNumber,
                        'title'       => ExamPart::PART_TITLES[$partNumber] ?? "Part {$partNumber}",
                        'section'     => ExamPart::PART_SECTIONS[$partNumber] ?? 'reading',
                        'order'       => $partNumber,
                    ]);
                }
            }

            return $exam;
        });
    }

    /**
     * Update exam data.
     */
    public function updateExam(Exam $exam, array $data): Exam
    {
        $exam->update([
            'title'            => $data['title'],
            'description'      => $data['description'] ?? null,
            'duration_minutes' => $data['duration_minutes'] ?? 120,
            'is_full_test'     => $data['is_full_test'] ?? false,
        ]);

        return $exam->fresh();
    }

    /**
     * Publish or unpublish an exam.
     * Validates that the exam has at least one question before publishing.
     */
    public function togglePublish(Exam $exam): array
    {
        if ($exam->status === 'published') {
            $exam->update(['status' => 'draft']);
            return ['status' => 'draft', 'message' => 'Đề thi đã được ẩn.'];
        }

        // Validate before publishing
        $totalQuestions = $exam->parts()->withCount('questions')->get()->sum('questions_count');
        if ($totalQuestions === 0) {
            return [
                'status'  => 'error',
                'message' => 'Không thể xuất bản: đề thi chưa có câu hỏi nào.',
            ];
        }

        $exam->update([
            'status'          => 'published',
            'total_questions' => $totalQuestions,
        ]);

        return ['status' => 'published', 'message' => 'Đề thi đã được xuất bản.'];
    }

    /**
     * Recalculate and cache total_questions for an exam.
     */
    public function syncTotalQuestions(Exam $exam): int
    {
        $total = $exam->parts()->withCount('questions')->get()->sum('questions_count');
        $exam->update(['total_questions' => $total]);
        return $total;
    }
}
