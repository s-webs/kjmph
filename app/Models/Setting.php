<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Setting extends Model
{
    protected $fillable = [
        'logo',
        'cover',
        'name',
        'options',
    ];

    // json -> array (или 'collection' по желанию)
    protected $casts = [
        'options' => 'array',
    ];

    protected array $i18nOptionBases = [
        'description',
        'journal_name',
        'year_start',
        // добавляй при желании
    ];

    /* ===== i18n helpers ===== */

    protected function currentLocale(): string
    {
        try { return LaravelLocalization::getCurrentLocale() ?: app()->getLocale(); }
        catch (\Throwable) { return app()->getLocale() ?? config('app.locale', 'en'); }
    }

    protected function supportedLocales(): array
    {
        try {
            $keys = LaravelLocalization::getSupportedLanguagesKeys(); // ['en','ru','kk',...]
            if (!empty($keys)) return $keys;
        } catch (\Throwable) {}
        // запасной вариант
        $fallbacks = array_filter([app()->getLocale(), config('app.fallback_locale'), 'en', 'ru', 'kk']);
        return array_values(array_unique($fallbacks));
    }

    protected function isTranslatableBaseKey(string $base): bool
    {
        if (in_array($base, $this->i18nOptionBases, true)) return true;

        $opts = $this->options ?? [];
        foreach ($this->supportedLocales() as $l) {
            if (array_key_exists("{$base}_{$l}", $opts)) return true;
        }
        return false;
    }

    protected function transOption(string $base, ?string $locale = null): mixed
    {
        $opts = $this->options ?? [];
        $loc  = $locale ?: $this->currentLocale();

        $candidates = ["{$base}_{$loc}"];
        // фоллбэки: другие поддерживаемые локали, потом “сырой” base
        foreach ($this->supportedLocales() as $l) {
            if ($l !== $loc) $candidates[] = "{$base}_{$l}";
        }
        $candidates[] = $base;

        foreach ($candidates as $k) {
            if (Arr::has($opts, $k)) return Arr::get($opts, $k);
        }
        return null;
    }

    protected function setTransOption(string $base, mixed $value, ?string $locale = null): void
    {
        $loc  = $locale ?: $this->currentLocale();
        $opts = $this->options ?? [];
        Arr::set($opts, "{$base}_{$loc}", $value);
        $this->options = $opts;
    }

    /* ===== ПЕРЕОПРЕДЕЛЯЕМ чтение/запись ===== */

    public function getAttribute($key)
    {
        // 1) реальные столбцы/аксессоры/relations
        $value = parent::getAttribute($key);
        if (!is_null($value)) return $value;

        $opts = $this->getAttributeValue('options') ?? [];

        if (is_array($opts)) {
            // 2) прямой ключ в options (eissn, eissn_link и т.п.)
            if (Arr::has($opts, $key)) return Arr::get($opts, $key);

            // 3) auto-i18n: base -> base_{locale}
            if ($this->isTranslatableBaseKey($key)) {
                return $this->transOption($key);
            }
        }

        return null;
    }

    public function setAttribute($key, $value)
    {
        // реальные поля/касты/мутации — по-умолчанию
        if (
            (method_exists($this, 'hasSetMutator') && $this->hasSetMutator($key)) ||
            (method_exists($this, 'hasAttributeMutator') && $this->hasAttributeMutator($key)) ||
            $this->isClassCastable($key) ||
            array_key_exists($key, $this->attributes)
        ) {
            return parent::setAttribute($key, $value);
        }

        // если это локализуемый base-ключ — кладём в base_{currentLocale}
        if ($this->isTranslatableBaseKey($key)) {
            $this->setTransOption($key, $value);
            return $this;
        }

        // иначе обычный ключ в options (поддерживает dot-нотацию)
        $opts = $this->options ?? [];
        Arr::set($opts, $key, $value);
        $this->options = $opts;

        return $this;
    }

    public function toArray()
    {
        $array = parent::toArray();
        if (isset($array['options']) && is_array($array['options'])) {
            $array = array_merge($array['options'], $array);
            unset($array['options']);
        }
        return $array;
    }

    /* (Опционально) метод для явного запроса любой базовой i18n-настройки */
    public function t(string $base, ?string $locale = null): mixed
    {
        return $this->transOption($base, $locale);
    }
}
