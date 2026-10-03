<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamPart extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'part_number',
        'title',
        'section',
        'instructions',
        'order',
    ];

    protected $casts = [
        'part_number' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Default TOEIC part titles.
     */
    public const PART_TITLES = [
        1 => 'Photographs',
        2 => 'Question-Response',
        3 => 'Conversations',
        4 => 'Talks',
        5 => 'Incomplete Sentences',
        6 => 'Text Completion',
        7 => 'Reading Comprehension',
    ];

    /**
     * Map part numbers to sections.
     */
    public const PART_SECTIONS = [
        1 => 'listening',
        2 => 'listening',
        3 => 'listening',
        4 => 'listening',
        5 => 'reading',
        6 => 'reading',
        7 => 'reading',
    ];

    /**
     * Exam this part belongs to.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Passages in this part.
     */
    public function passages(): HasMany
    {
        return $this->hasMany(Passage::class)->orderBy('order');
    }

    /**
     * Questions in this part.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('question_number');
    }

    /**
     * Audio files in this part.
     */
    public function audioFiles(): HasMany
    {
        return $this->hasMany(AudioFile::class)->orderBy('order');
    }

    /**
     * Check if this is a listening part.
     */
    public function isListening(): bool
    {
        return $this->section === 'listening';
    }
}
