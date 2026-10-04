<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttemptResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_attempt_id',
        'total_questions',
        'correct_answers',
        'wrong_answers',
        'unanswered',
        'listening_correct',
        'reading_correct',
        'listening_score',
        'reading_score',
        'total_score',
        'accuracy_percentage',
        'part_results',
        'scored_at',
    ];

    protected $casts = [
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'wrong_answers' => 'integer',
        'unanswered' => 'integer',
        'listening_correct' => 'integer',
        'reading_correct' => 'integer',
        'listening_score' => 'integer',
        'reading_score' => 'integer',
        'total_score' => 'integer',
        'accuracy_percentage' => 'decimal:2',
        'part_results' => 'array',
        'scored_at' => 'datetime',
    ];

    /**
     * Attempt this result belongs to.
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }
}
