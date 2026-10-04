<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamPart;
use App\Models\Passage;
use App\Services\QuestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PassageController extends Controller
{
    public function __construct(
        private QuestionService $questionService
    ) {}

    /**
     * Show form to create a new passage for an exam part.
     */
    public function create(ExamPart $part): View
    {
        $part->load('exam', 'audioFiles');
        return view('admin.passages.create', compact('part'));
    }

    /**
     * Store a newly created passage.
     */
    public function store(Request $request, ExamPart $part): RedirectResponse
    {
        $data = $request->validate([
            'title'         => ['nullable', 'string', 'max:255'],
            'content'       => ['nullable', 'string', 'max:20000'],
            'audio_file_id' => ['nullable', 'exists:audio_files,id'],
            'image'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ]);

        $this->questionService->createPassage($part, $data, $request->file('image'));

        return redirect()
            ->route('admin.exams.questions.index', ['exam' => $part->exam_id, 'part_id' => $part->id])
            ->with('success', 'Đoạn văn / Bài đọc đã được thêm thành công.');
    }

    /**
     * Show form to edit a passage.
     */
    public function edit(Passage $passage): View
    {
        $passage->load('examPart.exam', 'examPart.audioFiles');
        $part = $passage->examPart;

        return view('admin.passages.edit', compact('passage', 'part'));
    }

    /**
     * Update an existing passage.
     */
    public function update(Request $request, Passage $passage): RedirectResponse
    {
        $data = $request->validate([
            'title'         => ['nullable', 'string', 'max:255'],
            'content'       => ['nullable', 'string', 'max:20000'],
            'audio_file_id' => ['nullable', 'exists:audio_files,id'],
            'image'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'remove_image'  => ['nullable', 'boolean'],
        ]);

        $this->questionService->updatePassage($passage, $data, $request->file('image'));

        return redirect()
            ->route('admin.exams.questions.index', ['exam' => $passage->examPart->exam_id, 'part_id' => $passage->exam_part_id])
            ->with('success', 'Nội dung bài đọc đã được cập nhật.');
    }

    /**
     * Delete a passage.
     */
    public function destroy(Passage $passage): RedirectResponse
    {
        $partId = $passage->exam_part_id;
        $examId = $passage->examPart->exam_id;

        $this->questionService->deletePassage($passage);

        return redirect()
            ->route('admin.exams.questions.index', ['exam' => $examId, 'part_id' => $partId])
            ->with('success', 'Bài đọc đã được xóa.');
    }
}
