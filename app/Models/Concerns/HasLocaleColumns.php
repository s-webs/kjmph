<?php

namespace App\Models\Concerns;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

trait HasLocaleColumns
{
    // Необязательно. Если хочешь жёстко задать базовые ключи — задай здесь в модели.
    // protected array $i18nBases = ['name','text', ...];

    /** Кэш авто-детекта базовых ключей */
    protected ?array $__i18nBasesCache = null;

    protected function currentLocale(): string
    {
        try { $l = LaravelLocalization::getCurrentLocale(); }
        catch (\Throwable) { $l = app()->getLocale(); }
        return $l ?: config('app.locale', 'en');
    }

    protected function supportedLocales(): array
    {
        try {
            $keys = LaravelLocalization::getSupportedLanguagesKeys(); // ['en','ru','kk',...]
            if ($keys) return array_values(array_unique($keys));
        } catch (\Throwable) {}
        // Запасной вариант (если пакет не активен)
        $fallbacks = array_filter([app()->getLocale(), config('app.fallback_locale'), 'en', 'ru', 'kk']);
        return array_values(array_unique($fallbacks));
    }

    protected function fallbackLocales(): array
    {
        $cur = $this->currentLocale();
        return array_values(array_diff($this->supportedLocales(), [$cur]));
    }

    /** Авто-детект баз: по колонкам вида base_{locale} в fillable/attributes */
    protected function i18nBases(): array
    {
        if ($this->__i18nBasesCache !== null) return $this->__i18nBasesCache;

        $bases = property_exists($this, 'i18nBases') ? (array) $this->i18nBases : [];
        $cols  = array_merge($this->getFillable(), array_keys($this->attributes));
        $locs  = $this->supportedLocales();

        foreach ($cols as $col) {
            foreach ($locs as $loc) {
                $suffix = "_{$loc}";
                if (str_ends_with($col, $suffix) && strlen($col) > strlen($suffix)) {
                    $bases[] = substr($col, 0, -strlen($suffix));
                    break;
                }
            }
        }
        $this->__i18nBasesCache = array_values(array_unique($bases));
        return $this->__i18nBasesCache;
    }

    protected function isTransBase(string $base): bool
    {
        return in_array($base, $this->i18nBases(), true);
    }

    protected function columnExists(string $col): bool
    {
        return array_key_exists($col, $this->attributes) || in_array($col, $this->getFillable(), true);
    }

    /* -------- Перехват чтения -------- */
    public function getAttribute($key)
    {
        // 1) если есть реальная колонка/аксессор — стандартно
        $value = parent::getAttribute($key);
        if (!is_null($value) || $this->columnExists($key)) return $value;

        // 2) auto-i18n: base -> base_{locale} (+ фоллбэки)
        if ($this->isTransBase($key)) {
            $locCol = "{$key}_{$this->currentLocale()}";
            $v = parent::getAttribute($locCol);
            if ($v !== null && $v !== '') return $v;

            foreach ($this->fallbackLocales() as $fb) {
                $v = parent::getAttribute("{$key}_{$fb}");
                if ($v !== null && $v !== '') return $v;
            }

            // 3) последний шанс — первая доступная base_*
            foreach ($this->i18nBases() as $b) {
                if ($b !== $key) continue;
                foreach ($this->supportedLocales() as $l) {
                    $v = parent::getAttribute("{$key}_{$l}");
                    if ($v !== null && $v !== '') return $v;
                }
            }
            return null;
        }

        return null;
    }

    /* -------- Перехват записи -------- */
    public function setAttribute($key, $value)
    {
        if ($this->isTransBase($key)) {
            return parent::setAttribute("{$key}_{$this->currentLocale()}", $value);
        }
        return parent::setAttribute($key, $value);
    }

    /** Явный доступ к нужной локали: $model->t('name','kk') */
    public function t(string $base, ?string $locale = null): mixed
    {
        if (!$this->isTransBase($base)) return parent::getAttribute($base);

        $loc = $locale ?: $this->currentLocale();
        $v = parent::getAttribute("{$base}_{$loc}");
        if ($v !== null && $v !== '') return $v;

        foreach ($this->fallbackLocales() as $fb) {
            $v = parent::getAttribute("{$base}_{$fb}");
            if ($v !== null && $v !== '') return $v;
        }
        return null;
    }
}
