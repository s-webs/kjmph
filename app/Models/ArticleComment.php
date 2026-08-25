<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleComment extends Model
{
    protected $fillable = [
        'article_id',
        'sender_type',
        'message',
        'attachment',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function isEditor(): bool
    {
        return $this->sender_type === 'editor';
    }

    public function isAuthor(): bool
    {
        return $this->sender_type === 'author';
    }
}
