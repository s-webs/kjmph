<?php

namespace App\Models;

use App\Models\Concerns\HasLocaleColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Release extends Model
{
    use HasLocaleColumns, Searchable;

    protected $fillable = [
        'title_en', 'title_ru', 'title_kk',
        'description_en', 'description_ru', 'description_kk',
        'slug_en', 'slug_ru', 'slug_kk',
        'cover', 'file', 'url_id', 'doi', 'published_at',
        'volume', 'number', 'year', 'created_at', 'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Дата публикации выпуска влияет на видимость его статей в поиске.
        static::saved(static function (self $release) {
            if ($release->wasChanged('published_at')) {
                $release->articles()->with(['author', 'release'])->get()->each->searchable();
            }
        });
    }

    public function shouldBeSearchable(): bool
    {
        return $this->published_at !== null;
    }

    public function toSearchableArray(): array
    {
        $data = ['id' => $this->id, 'doi' => $this->doi, 'year' => $this->year];

        foreach (['en', 'ru', 'kk'] as $locale) {
            foreach (['title', 'description'] as $field) {
                $value = $this->getAttributes()["{$field}_{$locale}"] ?? null;
                $data["{$field}_{$locale}"] = filled($value)
                    ? trim(html_entity_decode(strip_tags((string) $value)))
                    : null;
            }
        }

        $data['published_at'] = $this->published_at?->timestamp;

        return $data;
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
