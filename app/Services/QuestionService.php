<?php

namespace App\Services;

use App\Models\ExamPart;
use App\Models\Passage;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuestionService
{
    public function __construct(
        private ExamService $examService
    ) {}

    /**
     * Create a new question with its options.
     */
    public function createQuestion(ExamPart $part, array $data, ?UploadedFile $image = null): Question
    {
        return DB::transaction(function () use ($part, $data, $image) {
            $imagePath = null;
            if ($image) {
                $imagePath = $image->store("exams/{$part->exam_id}/questions", 'private');
            }

            $question = Question::create([
                'exam_part_id'    => $part->id,
                'passage_id'      => $data['passage_id'] ?? null,
                'audio_file_id'   => $data['audio_file_id'] ?? null,
                'question_number' => $data['question_number'],
                'content'         => $data['content'] ?? null,
                'image_path'      => $imagePath,
                'question_type'   => $data['question_type'] ?? 'single_choice',
                'explanation'     => $data['explanation'] ?? null,
                'order'           => $data['order'] ?? $data['question_number'],
            ]);

            // Create options (A, B, C, D)
            if (!empty($data['options']) && is_array($data['options'])) {
                $correctOption = $data['correct_option'] ?? null;

                foreach ($data['options'] as $idx => $opt) {
                    $label = strtoupper(trim($opt['label'] ?? chr(65 + $idx)));
                    $content = $opt['content'] ?? '';
                    $isCorrect = ($label === strtoupper($correctOption)) || (!empty($opt['is_correct']));

                    QuestionOption::create([
                        'question_id' => $question->id,
                        'label'       => $label,
                        'content'     => $content,
                        'is_correct'  => $isCorrect,
                        'order'       => $idx + 1,
                    ]);
                }
            }

            $this->examService->syncTotalQuestions($part->exam);

            return $question;
        });
    }

    /**
     * Update an existing question and its options.
     */
    public function updateQuestion(Question $question, array $data, ?UploadedFile $image = null): Question
    {
        return DB::transaction(function () use ($question, $data, $image) {
            $imagePath = $question->image_path;

            if ($image) {
                if ($imagePath && Storage::disk('private')->exists($imagePath)) {
                    Storage::disk('private')->delete($imagePath);
                }
                $imagePath = $image->store("exams/{$question->examPart->exam_id}/questions", 'private');
            } elseif (!empty($data['remove_image']) && $imagePath) {
                if (Storage::disk('private')->exists($imagePath)) {
                    Storage::disk('private')->delete($imagePath);
                }
                $imagePath = null;
            }

            $question->update([
                'passage_id'      => $data['passage_id'] ?? $question->passage_id,
                'audio_file_id'   => $data['audio_file_id'] ?? $question->audio_file_id,
                'question_number' => $data['question_number'] ?? $question->question_number,
                'content'         => $data['content'] ?? $question->content,
                'image_path'      => $imagePath,
                'explanation'     => $data['explanation'] ?? $question->explanation,
                'order'           => $data['order'] ?? $question->order,
            ]);

            // Update options if provided
            if (isset($data['options']) && is_array($data['options'])) {
                $correctOption = $data['correct_option'] ?? null;

                // Delete old options and recreate for clean state
                $question->options()->delete();

                foreach ($data['options'] as $idx => $opt) {
                    $label = strtoupper(trim($opt['label'] ?? chr(65 + $idx)));
                    $content = $opt['content'] ?? '';
                    $isCorrect = ($label === strtoupper($correctOption)) || (!empty($opt['is_correct']));

                    QuestionOption::create([
                        'question_id' => $question->id,
                        'label'       => $label,
                        'content'     => $content,
                        'is_correct'  => $isCorrect,
                        'order'       => $idx + 1,
                    ]);
                }
            }

            $this->examService->syncTotalQuestions($question->examPart->exam);

            return $question;
        });
    }

    /**
     * Delete a question and associated files.
     */
    public function deleteQuestion(Question $question): void
    {
        DB::transaction(function () use ($question) {
            $exam = $question->examPart->exam;

            if ($question->image_path && Storage::disk('private')->exists($question->image_path)) {
                Storage::disk('private')->delete($question->image_path);
            }

            $question->options()->delete();
            $question->delete();

            $this->examService->syncTotalQuestions($exam);
        });
    }

    /**
     * Create a passage (đoạn văn đọc hiểu hoặc transcript).
     */
    public function createPassage(ExamPart $part, array $data, ?UploadedFile $image = null): Passage
    {
        $imagePath = null;
        if ($image) {
            $imagePath = $image->store("exams/{$part->exam_id}/passages", 'private');
        }

        $order = $data['order'] ?? (($part->passages()->max('order') ?? 0) + 1);

        return Passage::create([
            'exam_part_id'  => $part->id,
            'audio_file_id' => $data['audio_file_id'] ?? null,
            'title'         => $data['title'] ?? null,
            'content'       => $data['content'] ?? null,
            'image_path'    => $imagePath,
            'order'         => $order,
        ]);
    }

    /**
     * Update a passage.
     */
    public function updatePassage(Passage $passage, array $data, ?UploadedFile $image = null): Passage
    {
        $imagePath = $passage->image_path;

        if ($image) {
            if ($imagePath && Storage::disk('private')->exists($imagePath)) {
                Storage::disk('private')->delete($imagePath);
            }
            $imagePath = $image->store("exams/{$passage->examPart->exam_id}/passages", 'private');
        } elseif (!empty($data['remove_image']) && $imagePath) {
            if (Storage::disk('private')->exists($imagePath)) {
                Storage::disk('private')->delete($imagePath);
            }
            $imagePath = null;
        }

        $passage->update([
            'audio_file_id' => $data['audio_file_id'] ?? $passage->audio_file_id,
            'title'         => $data['title'] ?? $passage->title,
            'content'       => $data['content'] ?? $passage->content,
            'image_path'    => $imagePath,
            'order'         => $data['order'] ?? $passage->order,
        ]);

        return $passage;
    }

    /**
     * Delete a passage and reset passage_id on linked questions.
     */
    public function deletePassage(Passage $passage): void
    {
        DB::transaction(function () use ($passage) {
            if ($passage->image_path && Storage::disk('private')->exists($passage->image_path)) {
                Storage::disk('private')->delete($passage->image_path);
            }

            // Unlink questions from this passage
            $passage->questions()->update(['passage_id' => null]);

            $passage->delete();
        });
    }

    /**
     * Batch generate question slots for a part (e.g. Part 1: Q1-Q6, Part 2: Q7-Q31).
     */
    public function generateQuestionSlots(ExamPart $part, int $startNumber, int $count, int $optionsCount = 4): int
    {
        return DB::transaction(function () use ($part, $startNumber, $count, $optionsCount) {
            $created = 0;

            for ($i = 0; $i < $count; $i++) {
                $qNumber = $startNumber + $i;

                // Check if already exists in this exam
                $exists = Question::whereHas('examPart', fn($q) => $q->where('exam_id', $part->exam_id))
                    ->where('question_number', $qNumber)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $question = Question::create([
                    'exam_part_id'    => $part->id,
                    'question_number' => $qNumber,
                    'content'         => "Câu hỏi {$qNumber}",
                    'order'           => $qNumber,
                ]);

                // Create placeholder options
                for ($optIdx = 0; $optIdx < $optionsCount; $optIdx++) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'label'       => chr(65 + $optIdx),
                        'content'     => '',
                        'is_correct'  => $optIdx === 0, // default A as correct
                        'order'       => $optIdx + 1,
                    ]);
                }

                $created++;
            }

            $this->examService->syncTotalQuestions($part->exam);

            return $created;
        });
    }
}
