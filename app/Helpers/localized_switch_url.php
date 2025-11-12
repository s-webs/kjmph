<?php

use Illuminate\Support\Arr;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

if (! function_exists('localized_switch_url')) {
    /**
     * Построить URL текущей страницы на другом языке.
     * - Если в роуте есть модель с методом slugFor($locale), подставим её slug для целевой локали.
     * - Если slug отсутствует — отправим на fallback-роут (index) этой сущности.
     * - Сохраняем query-параметры.
     */
    function localized_switch_url(string $locale): string
    {
        $route = request()->route();
        $targetPath = null;

        // Карта fallback-роутов (когда slug для нужной локали отсутствует)
        $fallbackMap = [
            'news.show'     => 'news.index',
            'projects.show' => 'projects.index',
        ];

        if ($route) {
            $name   = $route->getName();          // например 'news.show'
            $params = $route->parameters();       // ['article' => Article, ...]

            $missingSlug = false;

            // Пробегаем параметры и подменяем модели их slug'ом для нужной локали
            foreach ($params as $key => $value) {
                if (is_object($value) && method_exists($value, 'slugFor')) {
                    $slug = $value->slugFor($locale);
                    if (! $slug) {
                        $missingSlug = true;
                        break;
                    }
                    $params[$key] = $slug; // строка вместо модели
                }
            }

            if ($missingSlug && isset($fallbackMap[$name])) {
                // Уводим на индекс соответствующего раздела
                $targetPath = route($fallbackMap[$name], [], false);
            } else {
                // Если имя роута известно — собираем относительный путь
                $targetPath = $name ? route($name, $params, false) : request()->getPathInfo();
            }
        }

        // Оборачиваем в LaravelLocalization с префиксом /ru|/kk|/en и переносом ?query
        return LaravelLocalization::getLocalizedURL($locale, $targetPath, request()->query(), true);
    }
}
