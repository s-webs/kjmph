<?php

namespace App\Console\Commands;

use App\Jobs\ForwardRelayMessageJob;
use App\Models\Message;
use App\Models\Reviewer;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use MoonShine\Laravel\Models\MoonshineUser;

class ReceiveEmailCommand extends Command
{
    protected $signature = 'email:receive';

    protected $description = 'Receive inbound relay email from Postfix (STDIN)';

    public function handle(): int
    {
        $raw = stream_get_contents(STDIN);

        if (! is_string($raw) || trim($raw) === '') {
            $this->warn('Empty stdin');

            return self::SUCCESS;
        }

        $parsed = $this->parseRawEmail($raw);
        $token = $this->extractReplyToken($parsed['to'] ?? '', $raw);

        if ($token === null) {
            Log::info('email:receive: reply token not found');

            return self::SUCCESS;
        }

        $parent = Message::query()->with('thread')->where('reply_token', $token)->first();

        if ($parent === null || $parent->thread === null) {
            Log::info('email:receive: message/thread not found', ['token' => $token]);

            return self::SUCCESS;
        }

        $fromEmail = strtolower((string) ($parsed['from'] ?? ''));

        if ($fromEmail === '') {
            return self::SUCCESS;
        }

        $senderType = $this->resolveSenderType($fromEmail);

        if ($senderType === null) {
            Log::info('email:receive: unknown sender', ['from' => $fromEmail]);

            return self::SUCCESS;
        }

        $body = $this->cleanBody((string) ($parsed['body'] ?? ''));

        if ($body === '') {
            Log::info('email:receive: empty body after cleanup');

            return self::SUCCESS;
        }

        $message = Message::query()->create([
            'thread_id' => $parent->thread_id,
            'sender_type' => $senderType,
            'body' => $body,
            'raw_email' => $raw,
            'sent_at' => now(),
        ]);

        ForwardRelayMessageJob::dispatch($message->id);

        $this->info('Relay message #' . $message->id . ' queued');

        return self::SUCCESS;
    }

    /**
     * @return array{from:?string,to:?string,body:string}
     */
    private function parseRawEmail(string $raw): array
    {
        $raw = str_replace(["\r\n", "\r"], "\n", $raw);
        $parts = preg_split("/\n\n/", $raw, 2);
        $headerBlock = $parts[0] ?? '';
        $body = $parts[1] ?? '';

        // Unfold folded headers
        $headerBlock = preg_replace("/\n[ \t]+/", ' ', $headerBlock) ?? $headerBlock;
        $headers = [];

        foreach (explode("\n", $headerBlock) as $line) {
            if (! str_contains($line, ':')) {
                continue;
            }

            [$name, $value] = explode(':', $line, 2);
            $headers[strtolower(trim($name))] = trim($value);
        }

        return [
            'from' => $this->extractAddress($headers['from'] ?? null),
            'to' => $this->extractAddress($headers['to'] ?? null),
            'body' => $body,
        ];
    }

    private function extractAddress(?string $headerValue): ?string
    {
        if (! filled($headerValue)) {
            return null;
        }

        if (preg_match('/<([^>]+)>/', $headerValue, $matches)) {
            return strtolower(trim($matches[1]));
        }

        if (preg_match('/([a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,})/i', $headerValue, $matches)) {
            return strtolower($matches[1]);
        }

        return null;
    }

    private function extractReplyToken(?string $to, string $raw): ?string
    {
        $domain = preg_quote((string) config('mail.relay.domain', 'kjmph.kz'), '/');
        $pattern = '/reply\+([0-9a-fA-F-]{36})@' . $domain . '/i';

        if (is_string($to) && preg_match($pattern, $to, $matches)) {
            return $matches[1];
        }

        if (preg_match($pattern, $raw, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function resolveSenderType(string $fromEmail): ?string
    {
        if (User::query()->whereRaw('LOWER(email) = ?', [$fromEmail])->exists()) {
            return 'author';
        }

        if (MoonshineUser::query()->whereRaw('LOWER(email) = ?', [$fromEmail])->exists()) {
            return 'editor';
        }

        if (Reviewer::query()->whereRaw('LOWER(email) = ?', [$fromEmail])->exists()) {
            return 'reviewer';
        }

        return null;
    }

    private function cleanBody(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = explode("\n", $text);
        $clean = [];

        foreach ($lines as $line) {
            $trimmed = ltrim($line);

            if (str_starts_with($trimmed, '>')
                || (str_starts_with($trimmed, 'On ') && str_contains($trimmed, ' wrote:'))
                || str_starts_with($trimmed, '---- Original Message ----')
                || str_starts_with($trimmed, 'From:')
            ) {
                break;
            }

            $clean[] = $line;
        }

        return trim(implode("\n", $clean));
    }
}
