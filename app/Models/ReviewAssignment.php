<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property Carbon|null $deadline
 * @property Carbon|null $invited_at
 */
class ReviewAssignment extends Model
{
    protected $fillable = [
        'article_id',
        'reviewer_id',
        'status',
        'deadline',
        'anonymized_title',
        'anonymized_file',
        'invited_at',
        'token',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'invited_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ReviewAssignment $assignment): void {
            if (! filled($assignment->token)) {
                $assignment->token = (string) Str::uuid();
            }
        });
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Reviewer::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function portalUrl(): string
    {
        return route('reviewer.show', $this->token);
    }
}
