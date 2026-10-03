<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_id',
        'status',
        'started_at',
        'completed_at',
        'time_spent_seconds',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'time_spent_seconds' => 'integer',
    ];

    /**
     * User who took this attempt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Exam attempted.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Answers given in this attempt.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(UserAnswer::class);
    }

    /**
     * Result of this attempt (1:1).
     */
    public function result(): HasOne
    {
        return $this->hasOne(AttemptResult::class);
    }

    /**
     * Check if attempt is still in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if attempt is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Scope: in-progress attempts.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }
}
