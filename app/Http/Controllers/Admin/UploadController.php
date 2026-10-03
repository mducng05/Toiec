<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAsset;
use App\Models\AudioFile;
use App\Models\ExamPart;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UploadController extends Controller
{
    public function __construct(
        private FileUploadService $uploadService
    ) {}

    /**
     * Show the upload management page for an exam.
     */
    public function index(Exam $exam): View
    {
        $exam->load([
            'assets' => fn($q) => $q->orderBy('type')->orderBy('order'),
            'parts.audioFiles' => fn($q) => $q->orderBy('order'),
        ]);

        return view('admin.uploads.index', compact('exam'));
    }

    /**
     * Upload exam PDF (đề thi).
     */
    public function uploadExamPdf(Request $request, Exam $exam): RedirectResponse
    {
        $request->validate([
            'pdf_file' => ['required', 'file', 'mimes:pdf', 'max:51200'],
        ]);

        try {
            $this->uploadService->uploadExamPdf(
                $request->file('pdf_file'),
                $exam->id,
                'exam_pdf'
            );

            return back()->with('success', 'PDF đề thi đã được upload thành công.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Upload answer PDF (đáp án).
     */
    public function uploadAnswerPdf(Request $request, Exam $exam): RedirectResponse
    {
        $request->validate([
            'pdf_file' => ['required', 'file', 'mimes:pdf', 'max:51200'],
        ]);

        try {
            $this->uploadService->uploadExamPdf(
                $request->file('pdf_file'),
                $exam->id,
                'answer_pdf'
            );

            return back()->with('success', 'PDF đáp án đã được upload thành công.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Upload one or more MP3 audio files to an exam part.
     */
    public function uploadAudio(Request $request, ExamPart $part): RedirectResponse
    {
        $request->validate([
            'audio_files'   => ['required', 'array', 'max:20'],
            'audio_files.*' => ['file', 'mimes:mp3,mpeg,wav,ogg', 'max:102400'],
        ]);

        $uploaded = 0;
        $order = $part->audioFiles()->max('order') ?? 0;

        foreach ($request->file('audio_files') as $file) {
            try {
                $this->uploadService->uploadAudio($file, $part->id, ++$order);
                $uploaded++;
            } catch (\InvalidArgumentException $e) {
                return back()->with('error', "Lỗi file \"{$file->getClientOriginalName()}\": {$e->getMessage()}");
            }
        }

        return back()->with('success', "{$uploaded} file audio đã được upload thành công.");
    }

    /**
     * Delete an exam asset (PDF).
     */
    public function deleteAsset(ExamAsset $asset): RedirectResponse
    {
        $examId = $asset->exam_id;
        $this->uploadService->deleteFile($asset->file_path);
        $asset->delete();

        return back()->with('success', 'File đã được xóa.');
    }

    /**
     * Delete an audio file.
     */
    public function deleteAudio(AudioFile $audio): RedirectResponse
    {
        $this->uploadService->deleteFile($audio->file_path);
        $audio->delete();

        return back()->with('success', 'File audio đã được xóa.');
    }

    /**
     * Serve a private file securely (with auth check).
     * Route: GET /admin/files/{path}
     */
    public function serveFile(string $encodedPath): Response
    {
        $path = base64_decode($encodedPath);

        // Security: only allow paths within the private disk
        if (!Storage::disk('private')->exists($path)) {
            abort(404, 'File not found.');
        }

        $content  = Storage::disk('private')->get($path);
        $mimeType = Storage::disk('private')->mimeType($path);

        return response($content, 200, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline',
        ]);
    }

    /**
     * Update audio order (AJAX).
     */
    public function reorderAudio(Request $request, ExamPart $part): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        foreach ($request->order as $position => $audioId) {
            AudioFile::where('id', $audioId)
                ->where('exam_part_id', $part->id)
                ->update(['order' => $position]);
        }

        return response()->json(['success' => true]);
    }
}
