<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_part_id',
        'passage_id',
        'audio_file_id',
        'question_number',
        'content',
        'image_path',
        'question_type',
        'explanation',
        'order',
    ];

    protected $casts = [
        'question_number' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Part this question belongs to.
     */
    public function examPart(): BelongsTo
    {
        return $this->belongsTo(ExamPart::class);
    }

    /**
     * Passage this question belongs to (nullable, for grouped questions).
     */
    public function passage(): BelongsTo
    {
        return $this->belongsTo(Passage::class);
    }

    /**
     * Audio file for this question (nullable, for Part 1,2).
     */
    public function audioFile(): BelongsTo
    {
        return $this->belongsTo(AudioFile::class);
    }

    /**
     * Answer options (A, B, C, D).
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order');
    }

    /**
     * User answers for this question.
     */
    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserAnswer::class);
    }

    /**
     * Get the correct option for this question.
     */
    public function correctOption()
    {
        return $this->options()->where('is_correct', true)->first();
    }
}
