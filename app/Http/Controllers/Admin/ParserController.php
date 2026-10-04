<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamPart;
use App\Services\DocumentParserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParserController extends Controller
{
    public function __construct(
        private DocumentParserService $parserService
    ) {}

    /**
     * Show the Parser & Document Automation Workspace.
     */
    public function index(Exam $exam): View
    {
        $exam->load([
            'parts' => fn($q) => $q->withCount('questions')->orderBy('order'),
            'assets' => fn($q) => $q->orderBy('type'),
        ]);

        $examPdf = $exam->assets->firstWhere('type', 'exam_pdf');
        $answerPdf = $exam->assets->firstWhere('type', 'answer_pdf');

        return view('admin.parser.index', compact('exam', 'examPdf', 'answerPdf'));
    }

    /**
     * Parse raw text answer keys (Preview or Apply).
     */
    public function parseAnswers(Request $request, Exam $exam): JsonResponse|RedirectResponse
    {
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
     * Parse raw questions block (Preview or Apply).
     */
    public function parseQuestions(Request $request, Exam $exam): JsonResponse|RedirectResponse
    {
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
}
