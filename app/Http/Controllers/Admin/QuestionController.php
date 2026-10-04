<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamPart;
use App\Models\Question;
use App\Services\QuestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function __construct(
        private QuestionService $questionService
    ) {}

    /**
     * Display list of questions for an exam, filtered by part.
     */
    public function index(Request $request, Exam $exam): View
    {
        $exam->load(['parts' => fn($q) => $q->orderBy('order')]);

        // Default to first part or requested part
        $activePartId = $request->query('part_id');
        $activePart = $activePartId
            ? $exam->parts->firstWhere('id', (int) $activePartId)
            : $exam->parts->first();

        if (!$activePart && $exam->parts->isNotEmpty()) {
            $activePart = $exam->parts->first();
        }

        $questions = collect();
        $passages = collect();

        if ($activePart) {
            $passages = $activePart->passages()
                ->with(['questions.options', 'audioFile'])
                ->get();

            // Questions not belonging to any passage (or all if standalone part)
            $questions = $activePart->questions()
                ->whereNull('passage_id')
                ->with(['options', 'audioFile'])
                ->orderBy('question_number')
                ->get();
        }

        return view('admin.questions.index', compact('exam', 'activePart', 'questions', 'passages'));
    }

    /**
     * Show form to create a new question for a part.
     */
    public function create(ExamPart $part): View
    {
        $part->load('exam', 'passages', 'audioFiles');

        // Suggest next question number
        $maxNum = Question::whereHas('examPart', fn($q) => $q->where('exam_id', $part->exam_id))
            ->max('question_number');
        $nextNumber = $maxNum ? ($maxNum + 1) : 1;

        // Part 2 has 3 choices, others have 4
        $optionCount = ($part->part_number === 2) ? 3 : 4;

        return view('admin.questions.create', compact('part', 'nextNumber', 'optionCount'));
    }

    /**
     * Store a newly created question.
     */
    public function store(Request $request, ExamPart $part): RedirectResponse
    {
        $data = $request->validate([
            'question_number' => ['required', 'integer', 'min:1', 'max:200'],
            'content'         => ['nullable', 'string', 'max:5000'],
            'passage_id'      => ['nullable', 'exists:passages,id'],
            'audio_file_id'   => ['nullable', 'exists:audio_files,id'],
            'correct_option'  => ['required', 'string', 'in:A,B,C,D'],
            'options'         => ['required', 'array', 'min:3', 'max:4'],
            'options.*.label' => ['required', 'string', 'max:5'],
            'options.*.content'=> ['nullable', 'string', 'max:1000'],
            'explanation'     => ['nullable', 'string', 'max:5000'],
            'image'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ]);

        $this->questionService->createQuestion($part, $data, $request->file('image'));

        return redirect()
            ->route('admin.exams.questions.index', ['exam' => $part->exam_id, 'part_id' => $part->id])
            ->with('success', "Câu hỏi #{$data['question_number']} đã được thêm thành công.");
    }

    /**
     * Show form to edit a question.
     */
    public function edit(Question $question): View
    {
        $question->load(['examPart.exam', 'examPart.passages', 'options']);
        $part = $question->examPart;

        return view('admin.questions.edit', compact('question', 'part'));
    }

    /**
     * Update an existing question.
     */
    public function update(Request $request, Question $question): RedirectResponse
    {
        $data = $request->validate([
            'question_number' => ['required', 'integer', 'min:1', 'max:200'],
            'content'         => ['nullable', 'string', 'max:5000'],
            'passage_id'      => ['nullable', 'exists:passages,id'],
            'audio_file_id'   => ['nullable', 'exists:audio_files,id'],
            'correct_option'  => ['required', 'string', 'in:A,B,C,D'],
            'options'         => ['required', 'array', 'min:3', 'max:4'],
            'options.*.label' => ['required', 'string', 'max:5'],
            'options.*.content'=> ['nullable', 'string', 'max:1000'],
            'explanation'     => ['nullable', 'string', 'max:5000'],
            'image'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'remove_image'    => ['nullable', 'boolean'],
        ]);

        $this->questionService->updateQuestion($question, $data, $request->file('image'));

        return redirect()
            ->route('admin.exams.questions.index', ['exam' => $question->examPart->exam_id, 'part_id' => $question->exam_part_id])
            ->with('success', "Câu hỏi #{$question->question_number} đã được cập nhật.");
    }

    /**
     * Delete a question.
     */
    public function destroy(Question $question): RedirectResponse
    {
        $partId = $question->exam_part_id;
        $examId = $question->examPart->exam_id;
        $num = $question->question_number;

        $this->questionService->deleteQuestion($question);

        return redirect()
            ->route('admin.exams.questions.index', ['exam' => $examId, 'part_id' => $partId])
            ->with('success', "Câu hỏi #{$num} đã được xóa.");
    }

    /**
     * Quick AJAX update of correct answer.
     */
    public function quickAnswer(Request $request, Question $question): JsonResponse
    {
        $request->validate([
            'correct_label' => ['required', 'string', 'in:A,B,C,D'],
        ]);

        $label = strtoupper($request->correct_label);

        // Update all options for this question
        foreach ($question->options as $option) {
            $option->update(['is_correct' => ($option->label === $label)]);
        }

        return response()->json([
            'success' => true,
            'question_id' => $question->id,
            'correct_label' => $label,
        ]);
    }

    /**
     * Auto generate standard slots for this part.
     */
    public function generateSlots(Request $request, ExamPart $part): RedirectResponse
    {
        // TOEIC Standard part question ranges:
        $standards = [
            1 => ['start' => 1, 'count' => 6, 'options' => 4],
            2 => ['start' => 7, 'count' => 25, 'options' => 3],
            3 => ['start' => 32, 'count' => 39, 'options' => 4],
            4 => ['start' => 71, 'count' => 30, 'options' => 4],
            5 => ['start' => 101, 'count' => 30, 'options' => 4],
            6 => ['start' => 131, 'count' => 16, 'options' => 4],
            7 => ['start' => 147, 'count' => 54, 'options' => 4],
        ];

        $cfg = $standards[$part->part_number] ?? ['start' => 1, 'count' => 10, 'options' => 4];

        $created = $this->questionService->generateQuestionSlots(
            $part,
            $cfg['start'],
            $cfg['count'],
            $cfg['options']
        );

        return back()->with('success', "Đã tạo nhanh {$created} câu hỏi mẫu cho Part {$part->part_number}.");
    }
}
