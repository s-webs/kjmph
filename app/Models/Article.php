<?php

namespace App\Models;

use App\Models\Concerns\HasLocaleColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasLocaleColumns;

    protected $fillable = [
        'author_id',
        'section',
        'material_lang',
        'cover',
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
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
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
}
