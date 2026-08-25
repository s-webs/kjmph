<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'invited_at' => 'datetime',
        ];
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
}
