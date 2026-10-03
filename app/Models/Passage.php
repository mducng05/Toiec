<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Passage extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_part_id',
        'audio_file_id',
        'title',
        'content',
        'image_path',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Part this passage belongs to.
     */
    public function examPart(): BelongsTo
    {
        return $this->belongsTo(ExamPart::class);
    }

    /**
     * Audio file for this passage (listening Parts 3,4).
     */
    public function audioFile(): BelongsTo
    {
        return $this->belongsTo(AudioFile::class);
    }

    /**
     * Questions that belong to this passage.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('question_number');
    }
}
