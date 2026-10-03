<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AudioFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_part_id',
        'file_path',
        'original_name',
        'file_size',
        'duration_seconds',
        'order',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'duration_seconds' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Part this audio belongs to.
     */
    public function examPart(): BelongsTo
    {
        return $this->belongsTo(ExamPart::class);
    }

    /**
     * Passages that use this audio.
     */
    public function passages(): HasMany
    {
        return $this->hasMany(Passage::class);
    }

    /**
     * Questions directly linked to this audio (Part 1,2).
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
