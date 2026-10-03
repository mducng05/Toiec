<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'duration_minutes',
        'total_questions',
        'status',
        'is_full_test',
    ];

    protected $casts = [
        'is_full_test' => 'boolean',
        'duration_minutes' => 'integer',
        'total_questions' => 'integer',
    ];

    /**
     * Auto-generate slug from title on creation.
     */
    protected static function booted(): void
    {
        static::creating(function (Exam $exam) {
            if (empty($exam->slug)) {
                $exam->slug = Str::slug($exam->title) . '-' . Str::random(6);
            }
        });
    }

    /**
     * Owner of this exam.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Parts in this exam.
     */
    public function parts(): HasMany
    {
        return $this->hasMany(ExamPart::class)->orderBy('order');
    }

    /**
     * Assets (PDFs, images) attached to this exam.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(ExamAsset::class);
    }

    /**
     * Attempts on this exam.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * Check if exam is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Scope: only published exams.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
