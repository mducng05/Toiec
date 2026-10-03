<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'type',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
        'order',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Exam this asset belongs to.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}
