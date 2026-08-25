<?php

namespace App\Jobs;

use App\Mail\SendRelayMessageMail;
use App\Models\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use MoonShine\Laravel\Models\MoonshineUser;

class ForwardRelayMessageJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $messageId,
    ) {
    }

    public function handle(): void
    {
        $message = Message::query()->with(['thread.article.author', 'thread.reviewer'])->find($this->messageId);

        if ($message === null) {
            return;
        }

        $thread = $message->thread;
        $article = $thread?->article;

        if ($thread === null || $article === null) {
            return;
        }

        $recipientEmail = match ($message->sender_type) {
            'author', 'reviewer' => $this->editorEmail(),
            'editor' => $thread->type === 'editor_reviewer'
                ? $thread->reviewer?->email
                : $article->author?->email,
            default => null,
        };

        if (! filled($recipientEmail)) {
            Log::warning('Relay forward skipped: recipient not found', [
                'message_id' => $message->id,
                'sender_type' => $message->sender_type,
            ]);

            return;
        }

        $title = (string) ($article->title ?? $article->title_ru ?? $article->title_en ?? ('#' . $article->id));
        $subject = 'Re: ' . $title;

        Mail::to($recipientEmail)->send(new SendRelayMessageMail($message, $subject));
    }

    private function editorEmail(): ?string
    {
        $configured = config('mail.editor_address') ?: config('mail.relay.from_address');

        if (filled($configured)) {
            return (string) $configured;
        }

        return MoonshineUser::query()->value('email');
    }
}
