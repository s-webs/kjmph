<?php

namespace App\Models;

use App\Models\Concerns\HasLocaleColumns;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Collection extends Model
{
    use HasLocaleColumns, Searchable;
    protected $fillable = [
        'title_en', 'title_ru', 'title_kk',
        'description_en', 'description_ru', 'description_kk',
        'slug_en', 'slug_ru', 'slug_kk',
        'cover', 'file', 'url_id', 'doi', 'published_at',
        'volume', 'number', 'year', 'created_at', 'updated_at',
    ];

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

        return $data;
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}

