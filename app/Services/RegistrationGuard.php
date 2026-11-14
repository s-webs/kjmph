<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Защита формы регистрации от ботов:
 *  1) honeypot-поле, которое человек не видит;
 *  2) время заполнения формы (боты отправляют мгновенно);
 *  3) Cloudflare Turnstile, если заданы TURNSTILE_SITE_KEY и TURNSTILE_SECRET_KEY.
 */
class RegistrationGuard
{
    private const MIN_SECONDS = 3;
    private const MAX_SECONDS = 86400;

    public static function turnstileEnabled(): bool
    {
        return filled(config('services.turnstile.site_key')) && filled(config('services.turnstile.secret'));
    }

    /** HTML для вставки внутрь формы: honeypot, метка времени и виджет Turnstile. */
    public static function fieldsHtml(): string
    {
        $token = e(Crypt::encryptString((string) now()->timestamp));

        $html = '<div style="position:absolute;left:-9999px;top:-9999px;height:0;overflow:hidden" aria-hidden="true">'
            . '<label>Website<input type="text" name="website" value="" tabindex="-1" autocomplete="off"></label>'
            . '</div>'
            . '<input type="hidden" name="reg_token" value="' . $token . '">';

        if (self::turnstileEnabled()) {
            $html .= '<div class="cf-turnstile" data-sitekey="' . e(config('services.turnstile.site_key')) . '"></div>'
                . '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
        }

        return $html;
    }

    /** @return string|null сообщение об ошибке или null, если проверка пройдена */
    public static function check(Request $request): ?string
    {
        $message = __('auth.captcha_failed');

        if (filled($request->input('website'))) {
            return $message;
        }

        try {
            $startedAt = (int) Crypt::decryptString((string) $request->input('reg_token'));
        } catch (Throwable) {
            return $message;
        }

        $elapsed = now()->timestamp - $startedAt;
        if ($elapsed < self::MIN_SECONDS || $elapsed > self::MAX_SECONDS) {
            return $message;
        }

        if (self::turnstileEnabled() && ! self::verifyTurnstile((string) $request->input('cf-turnstile-response'), $request->ip())) {
            return $message;
        }

        return null;
    }

    private static function verifyTurnstile(string $response, ?string $ip): bool
    {
        if ($response === '') {
            return false;
        }

        try {
            $result = Http::asForm()->timeout(5)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret'),
                'response' => $response,
                'remoteip' => $ip,
            ]);

            return (bool) $result->json('success', false);
        } catch (Throwable $e) {
            // Если Cloudflare недоступен, не блокируем людей: остаются honeypot и таймер.
            Log::warning('Turnstile verify failed: ' . $e->getMessage());

            return true;
        }
    }
}
