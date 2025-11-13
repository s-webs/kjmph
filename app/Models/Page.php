<?php

namespace App\Models;

use App\Models\Concerns\HasLocaleColumns;
use Illuminate\Database\Eloquent\Model;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class Page extends Model
{
    use HasLocaleColumns;

    protected $fillable = [
        'name_en', 'name_ru', 'name_kk',
        'text_en', 'text_ru', 'text_kk',
        'slug_en', 'slug_ru', 'slug_kk',
        'seo_title_en', 'seo_title_ru', 'seo_title_kk',
        'seo_keywords_en', 'seo_keywords_ru', 'seo_keywords_kk',
        'seo_description_en', 'seo_description_ru', 'seo_description_kk',
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        $locales = LaravelLocalization::getSupportedLanguagesKeys() ?: ['en', 'ru', 'kk'];

        return static::query()->where(function ($q) use ($locales, $value) {
            foreach ($locales as $l) {
                $q->orWhere("slug_{$l}", $value);
            }
        })->first();
    }

// и метод для хелпера
    public function slugFor(string $locale): ?string
    {
        return $this->t('slug', $locale); // из трейта HasLocaleColumns
    }
}
