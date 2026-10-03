<?php

namespace App\Services;

use App\Models\AudioFile;
use App\Models\ExamAsset;
use App\Models\ExamPart;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Allowed MIME types per file type.
     */
    private const ALLOWED_TYPES = [
        'pdf'   => ['application/pdf'],
        'audio' => ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/x-wav'],
        'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
    ];

    /**
     * Max file sizes in KB.
     */
    private const MAX_SIZES = [
        'pdf'   => 51200,   // 50 MB
        'audio' => 102400,  // 100 MB
        'image' => 5120,    // 5 MB
    ];

    /**
     * Upload a PDF (exam paper or answer key) and create an ExamAsset record.
     *
     * @param  UploadedFile  $file
     * @param  int           $examId
     * @param  string        $type   'exam_pdf' | 'answer_pdf'
     * @return ExamAsset
     */
    public function uploadExamPdf(UploadedFile $file, int $examId, string $type = 'exam_pdf'): ExamAsset
    {
        $this->validateFile($file, 'pdf');

        $path = $this->storeFile($file, "exams/{$examId}/pdf");

        return ExamAsset::create([
            'exam_id'       => $examId,
            'type'          => $type,
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'file_size'     => $file->getSize(),
            'mime_type'     => $file->getMimeType(),
        ]);
    }

    /**
     * Upload an MP3 audio file and create an AudioFile record.
     *
     * @param  UploadedFile  $file
     * @param  int           $examPartId
     * @param  int           $order
     * @return AudioFile
     */
    public function uploadAudio(UploadedFile $file, int $examPartId, int $order = 0): AudioFile
    {
        $this->validateFile($file, 'audio');

        $path = $this->storeFile($file, "parts/{$examPartId}/audio");

        return AudioFile::create([
            'exam_part_id'  => $examPartId,
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'file_size'     => $file->getSize(),
            'order'         => $order,
        ]);
    }

    /**
     * Upload an image (for question or passage) and return the storage path.
     *
     * @param  UploadedFile  $file
     * @param  string        $folder  e.g. "exams/{id}/images"
     * @return string  storage path
     */
    public function uploadImage(UploadedFile $file, string $folder): string
    {
        $this->validateFile($file, 'image');

        return $this->storeFile($file, $folder);
    }

    /**
     * Delete a file from storage by path.
     */
    public function deleteFile(string $path): bool
    {
        if (Storage::disk('private')->exists($path)) {
            return Storage::disk('private')->delete($path);
        }
        return false;
    }

    /**
     * Get a temporary URL or serve URL for a private file.
     */
    public function getServeUrl(string $path): string
    {
        return route('file.serve', ['path' => base64_encode($path)]);
    }

    /**
     * Store the uploaded file and return the relative path.
     */
    private function storeFile(UploadedFile $file, string $folder): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        return Storage::disk('private')->putFileAs($folder, $file, $filename);
    }

    /**
     * Validate file type and size.
     *
     * @throws \InvalidArgumentException
     */
    private function validateFile(UploadedFile $file, string $type): void
    {
        $allowedMimes = self::ALLOWED_TYPES[$type] ?? [];
        $maxSizeKb    = self::MAX_SIZES[$type] ?? 10240;

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException(
                "File type not allowed. Received: {$file->getMimeType()}. Allowed: " . implode(', ', $allowedMimes)
            );
        }

        if ($file->getSize() > $maxSizeKb * 1024) {
            $maxMb = $maxSizeKb / 1024;
            throw new \InvalidArgumentException(
                "File too large. Maximum size is {$maxMb}MB."
            );
        }
    }
}
