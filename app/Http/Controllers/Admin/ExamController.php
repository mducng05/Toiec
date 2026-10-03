<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamPart;
use App\Services\ExamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function __construct(
        private ExamService $examService
    ) {}

    /**
     * List all exams.
     */
    public function index(): View
    {
        $exams = Exam::withCount(['parts', 'attempts'])
            ->withTrashed()
            ->latest()
            ->paginate(15);

        return view('admin.exams.index', compact('exams'));
    }

    /**
     * Show form to create a new exam.
     */
    public function create(): View
    {
        $partTitles = ExamPart::PART_TITLES;
        return view('admin.exams.create', compact('partTitles'));
    }

    /**
     * Store a new exam.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:2000'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:480'],
            'is_full_test'     => ['boolean'],
            'parts'            => ['nullable', 'array'],
            'parts.*'          => ['integer', 'between:1,7'],
        ]);

        $exam = $this->examService->createExam($data, auth()->id());

        return redirect()
            ->route('admin.exams.show', $exam)
            ->with('success', "Đề thi \"{$exam->title}\" đã được tạo thành công.");
    }

    /**
     * Show exam detail (parts, assets, question count).
     */
    public function show(Exam $exam): View
    {
        $exam->load([
            'parts' => fn($q) => $q->withCount('questions')->orderBy('order'),
            'assets',
        ]);

        return view('admin.exams.show', compact('exam'));
    }

    /**
     * Show form to edit exam metadata.
     */
    public function edit(Exam $exam): View
    {
        return view('admin.exams.edit', compact('exam'));
    }

    /**
     * Update exam metadata.
     */
    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:2000'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:480'],
            'is_full_test'     => ['boolean'],
        ]);

        $this->examService->updateExam($exam, $data);

        return redirect()
            ->route('admin.exams.show', $exam)
            ->with('success', 'Thông tin đề thi đã được cập nhật.');
    }

    /**
     * Soft-delete an exam.
     */
    public function destroy(Exam $exam): RedirectResponse
    {
        $exam->delete();

        return redirect()
            ->route('admin.exams.index')
            ->with('success', "Đề thi \"{$exam->title}\" đã được xóa.");
    }

    /**
     * Toggle publish/draft status.
     */
    public function togglePublish(Exam $exam): RedirectResponse
    {
        $result = $this->examService->togglePublish($exam);

        $flashType = $result['status'] === 'error' ? 'error' : 'success';
        return redirect()
            ->back()
            ->with($flashType, $result['message']);
    }

    /**
     * Add a part to an exam.
     */
    public function addPart(Request $request, Exam $exam): RedirectResponse
    {
        $data = $request->validate([
            'part_number' => ['required', 'integer', 'between:1,7'],
        ]);

        $partNumber = (int) $data['part_number'];

        // Check if part already exists
        if ($exam->parts()->where('part_number', $partNumber)->exists()) {
            return back()->with('error', "Part {$partNumber} đã tồn tại trong đề thi này.");
        }

        ExamPart::create([
            'exam_id'     => $exam->id,
            'part_number' => $partNumber,
            'title'       => ExamPart::PART_TITLES[$partNumber] ?? "Part {$partNumber}",
            'section'     => ExamPart::PART_SECTIONS[$partNumber] ?? 'reading',
            'order'       => $partNumber,
        ]);

        return back()->with('success', "Part {$partNumber} đã được thêm.");
    }
}
