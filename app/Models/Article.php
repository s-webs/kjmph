<?php

namespace App\Models;

use App\Models\Concerns\HasLocaleColumns;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Article extends Model
{
    use HasLocaleColumns, Searchable;

    protected $fillable = [
        'author_id',
        'author_name',
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

    protected static function booted(): void
    {
        // Состав индекса авторов зависит от опубликованных статей.
        static::saved(static fn (self $article) => $article->syncAuthorSearch());
        static::deleted(static fn (self $article) => $article->syncAuthorSearch());
    }

    public function syncAuthorSearch(): void
    {
        // Текущий и прежний автор (при смене автора старый тоже нужно пересчитать).
        $ids = array_filter(array_unique([$this->author_id, $this->getOriginal('author_id')]));

        foreach (User::query()->whereIn('id', $ids)->get() as $author) {
            $author->shouldBeSearchable() ? $author->searchable() : $author->unsearchable();
        }
    }

    /** Имя автора для показа: заданное вручную имеет приоритет над именем пользователя. */
    public function getAuthorDisplayNameAttribute(): ?string
    {
        return filled($this->author_name) ? $this->author_name : $this->author?->name;
    }

    /** Статья попадает в поиск, только если выпуск назначен и дата публикации выпуска задана. */
    public function shouldBeSearchable(): bool
    {
        return $this->release_id !== null && $this->release?->published_at !== null;
    }

    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with(['author', 'release']);
    }

    public function toSearchableArray(): array
    {
        $authors = collect([$this->author_display_name])
            ->merge(collect($this->coauthors ?? [])->pluck('fullname'))
            ->filter(static fn ($name) => filled($name))
            ->values()
            ->all();

        $data = ['id' => $this->id, 'authors' => $authors, 'doi' => $this->doi];

        foreach (['en', 'ru', 'kk'] as $locale) {
            foreach (['title', 'subtitle', 'annotation'] as $field) {
                $value = $this->getAttributes()["{$field}_{$locale}"] ?? null;
                $data["{$field}_{$locale}"] = filled($value)
                    ? trim(html_entity_decode(strip_tags((string) $value)))
                    : null;
            }
        }

        // Unix-время: по нему при поиске отсекаются выпуски с будущей датой публикации.
        $data['release_published_at'] = $this->release?->published_at?->timestamp;

        return $data;
    }

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
        if ($this->release_id !== null) {
            return 'published';
        }

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
     * A release assignment publishes the article, including legacy records
     * whose timeline has not yet been updated.
     */
    public function scopeWhereCurrentStatus(Builder $query, string $status): Builder
    {
        if ($status === 'published') {
            return $query->whereNotNull('release_id');
        }

        return $query->whereNull('release_id')->whereRaw(
            "JSON_UNQUOTE(JSON_EXTRACT(publishing_process, CONCAT('\$[', JSON_LENGTH(publishing_process) - 1, '].status'))) = ?",
            [$status]
        );
    }
}
