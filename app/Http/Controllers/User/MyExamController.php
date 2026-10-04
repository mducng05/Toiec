<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamPart;
use App\Services\DocumentParserService;
use App\Services\ExamService;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyExamController extends Controller
{
    public function __construct(
        private ExamService $examService,
        private FileUploadService $uploadService,
        private DocumentParserService $parserService
    ) {}

    /**
     * List user's private/custom exams.
     */
    public function index(): View
    {
        $exams = Exam::where('created_by', auth()->id())
            ->withCount(['parts', 'attempts'])
            ->latest()
            ->paginate(12);

        return view('user.my-exams.index', compact('exams'));
    }

    /**
     * Form to create a custom/private exam.
     */
    public function create(): View
    {
        $partTitles = ExamPart::PART_TITLES;
        return view('user.my-exams.create', compact('partTitles'));
    }

    /**
     * Store custom exam.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:2000'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:480'],
            'is_full_test'     => ['boolean'],
            'is_public'        => ['boolean'],
            'parts'            => ['nullable', 'array'],
            'parts.*'          => ['integer', 'between:1,7'],
        ]);

        $exam = $this->examService->createExam($data, auth()->id());

        // Status: published if user wants it public, or draft for private practice
        if (!empty($data['is_public'])) {
            $exam->update(['status' => 'published']);
        }

        return redirect()
            ->route('my-exams.show', $exam)
            ->with('success', "Đề thi \"{$exam->title}\" đã được tạo thành công! Bạn có thể tải lên tài liệu PDF hoặc audio ngay bây giờ.");
    }

    /**
     * Manage user's exam workspace.
     */
    public function show(Exam $exam): View
    {
        $this->authorizeOwner($exam);

        $exam->load([
            'parts' => fn($q) => $q->withCount('questions')->orderBy('order'),
            'assets',
        ]);

        return view('user.my-exams.show', compact('exam'));
    }

    /**
     * Edit form.
     */
    public function edit(Exam $exam): View
    {
        $this->authorizeOwner($exam);
        return view('user.my-exams.edit', compact('exam'));
    }

    /**
     * Update metadata.
     */
    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $this->authorizeOwner($exam);

        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:2000'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:480'],
            'is_full_test'     => ['boolean'],
            'is_public'        => ['boolean'],
        ]);

        $this->examService->updateExam($exam, $data);
        $exam->update(['status' => !empty($data['is_public']) ? 'published' : 'draft']);

        return redirect()
            ->route('my-exams.show', $exam)
            ->with('success', 'Thông tin đề thi đã được cập nhật.');
    }

    /**
     * Delete user exam.
     */
    public function destroy(Exam $exam): RedirectResponse
    {
        $this->authorizeOwner($exam);
        $exam->delete();

        return redirect()
            ->route('my-exams.index')
            ->with('success', "Đề thi \"{$exam->title}\" đã được xóa.");
    }

    /**
     * User uploads view (PDFs & Audio).
     */
    public function uploads(Exam $exam): View
    {
        $this->authorizeOwner($exam);

        $exam->load([
            'assets' => fn($q) => $q->orderBy('type'),
            'parts.audioFiles' => fn($q) => $q->orderBy('order'),
        ]);

        return view('user.my-exams.uploads', compact('exam'));
    }

    /**
     * User upload PDF đề thi.
     */
    public function uploadExamPdf(Request $request, Exam $exam): RedirectResponse
    {
        $this->authorizeOwner($exam);

        $request->validate([
            'pdf_file' => ['required', 'file', 'mimes:pdf', 'max:51200'],
        ]);

        $this->uploadService->uploadExamPdf($request->file('pdf_file'), $exam->id, 'exam_pdf');

        return back()->with('success', 'PDF đề thi đã được tải lên thành công.');
    }

    /**
     * User upload PDF đáp án.
     */
    public function uploadAnswerPdf(Request $request, Exam $exam): RedirectResponse
    {
        $this->authorizeOwner($exam);

        $request->validate([
            'pdf_file' => ['required', 'file', 'mimes:pdf', 'max:51200'],
        ]);

        $this->uploadService->uploadExamPdf($request->file('pdf_file'), $exam->id, 'answer_pdf');

        return back()->with('success', 'PDF đáp án đã được tải lên thành công.');
    }

    /**
     * User upload Audio MP3 for a part.
     */
    public function uploadAudio(Request $request, ExamPart $part): RedirectResponse
    {
        $this->authorizeOwner($part->exam);

        $request->validate([
            'audio_files'   => ['required', 'array', 'max:20'],
            'audio_files.*' => ['file', 'mimes:mp3,mpeg,wav,ogg', 'max:102400'],
        ]);

        $order = $part->audioFiles()->max('order') ?? 0;
        foreach ($request->file('audio_files') as $file) {
            $this->uploadService->uploadAudio($file, $part->id, ++$order);
        }

        return back()->with('success', 'Audio nghe đã được tải lên thành công.');
    }

    /**
     * User Automation & Parser for their own exam.
     */
    public function parser(Exam $exam): View
    {
        $this->authorizeOwner($exam);

        $exam->load([
            'parts' => fn($q) => $q->withCount('questions')->orderBy('order'),
            'assets' => fn($q) => $q->orderBy('type'),
        ]);

        $examPdf = $exam->assets->firstWhere('type', 'exam_pdf');
        $answerPdf = $exam->assets->firstWhere('type', 'answer_pdf');

        return view('user.my-exams.parser', compact('exam', 'examPdf', 'answerPdf'));
    }

    /**
     * Parse raw text answer keys for user's own exam.
     */
    public function parseAnswers(Request $request, Exam $exam): JsonResponse|RedirectResponse
    {
        $this->authorizeOwner($exam);

        $request->validate([
            'raw_text' => ['required', 'string'],
            'create_missing' => ['nullable', 'boolean'],
            'apply' => ['nullable', 'boolean'],
        ]);

        $keys = $this->parserService->parseAnswerKeys($request->raw_text);

        if ($request->wantsJson() && !$request->apply) {
            return response()->json([
                'success' => true,
                'count'   => count($keys),
                'keys'    => $keys,
            ]);
        }

        if (empty($keys)) {
            return back()->with('error', 'Không nhận diện được đáp án nào từ văn bản đã dán. Vui lòng kiểm tra lại định dạng (VD: 1A 2B 3C...).');
        }

        $result = $this->parserService->applyAnswerKeys(
            $exam,
            $keys,
            (bool) $request->input('create_missing', false)
        );

        return back()->with('success', "Đã áp dụng thành công bảng đáp án cho {$result['updated']} câu hỏi (và tạo mới {$result['created']} câu hỏi).");
    }

    /**
     * Parse raw questions block for user's own exam.
     */
    public function parseQuestions(Request $request, Exam $exam): JsonResponse|RedirectResponse
    {
        $this->authorizeOwner($exam);

        $request->validate([
            'exam_part_id' => ['required', 'exists:exam_parts,id'],
            'raw_text'     => ['required', 'string'],
            'passage_id'   => ['nullable', 'exists:passages,id'],
            'apply'        => ['nullable', 'boolean'],
        ]);

        $part = $exam->parts()->findOrFail($request->exam_part_id);
        $questions = $this->parserService->parseQuestionBlocks($request->raw_text);

        if ($request->wantsJson() && !$request->apply) {
            return response()->json([
                'success'   => true,
                'count'     => count($questions),
                'questions' => $questions,
            ]);
        }

        if (empty($questions)) {
            return back()->with('error', 'Không bóc tách được câu hỏi nào từ văn bản đã dán. Vui lòng kiểm tra lại định dạng (VD: 101. Prompt (A)... (B)...).');
        }

        $imported = $this->parserService->importParsedQuestions($part, $questions, $request->passage_id);

        return back()->with('success', "Đã bóc tách và nhập thành công {$imported} câu hỏi vào Part {$part->part_number}.");
    }

    /**
     * Security check: ensure current user owns this exam (or is admin).
     */
    private function authorizeOwner(Exam $exam): void
    {
        if ($exam->created_by !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Bạn không có quyền chỉnh sửa bộ đề thi này.');
        }
    }
}
