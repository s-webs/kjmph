<?php

namespace App\Models;

use App\Models\Concerns\HasLocaleColumns;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasLocaleColumns;

    protected $fillable = [
        'author_id',
        'section',
        'release_id',
        'section_id',
        'material_lang',
        'cover',
        'file',
        'antiplagiat_doc_id',
        'antiplagiat_percent',
        'antiplagiat_checked_at',
        'antiplagiat_report',
        'doi',

        'title_en',
        'title_ru',
        'title_kk',

        'subtitle_en',
        'subtitle_ru',
        'subtitle_kk',

        'annotation_en',
        'annotation_ru',
        'annotation_kk',

        'literature_en',
        'literature_ru',
        'literature_kk',

        'coauthors',
        'metadata',
        'publishing_process',
        'history',
    ];

    protected $casts = [
        'coauthors' => 'array',
        'metadata' => 'array',
        'publishing_process' => 'array',
        'history' => 'array',
        'antiplagiat_report' => 'array',
        'antiplagiat_percent' => 'float',
        'antiplagiat_checked_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class);
    }

    public function sectionModel(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function reviewAssignments(): HasMany
    {
        return $this->hasMany(ReviewAssignment::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ArticleComment::class)->latest();
    }

    public function messageThreads(): HasMany
    {
        return $this->hasMany(MessageThread::class);
    }

    public function addProcessStep(string $status, ?string $label = null, array $extra = []): void
    {
        $timeline = $this->publishing_process ?? [];

        $timeline[] = array_merge([
            'status' => $status,
            'label' => $label ?? $status,
            'at' => now()->toDateTimeString(),
        ], $extra);

        $this->publishing_process = $timeline;
        $this->save();
    }

    public function getCurrentStatus(): ?string
    {
        return $this->current_status;
    }

    public function getCurrentStatusAttribute(): ?string
    {
        $timeline = $this->publishing_process ?? [];

        if (empty($timeline)) {
            return null;
        }

        $last = last($timeline);

        return $last['status'] ?? null;
    }

    public function getCurrentStepAttribute()
    {
        $process = $this->publishing_process ?? [];

        if (empty($process)) {
            return null;
        }

        return collect($process)->last();
    }

    /**
     * Filter by the latest publishing_process status (MySQL JSON).
     */
    public function scopeWhereCurrentStatus(Builder $query, string $status): Builder
    {
        return $query->whereRaw(
            "JSON_UNQUOTE(JSON_EXTRACT(publishing_process, CONCAT('\$[', JSON_LENGTH(publishing_process) - 1, '].status'))) = ?",
            [$status]
        );
    }
}
