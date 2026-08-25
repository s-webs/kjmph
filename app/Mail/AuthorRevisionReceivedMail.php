<?php

namespace App\Mail;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuthorRevisionReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Article $article,
        public string $authorMessage,
    ) {
        $this->article->loadMissing('author');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Автор загрузил исправленную версию статьи #' . $this->article->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.author-revision-received',
            with: [
                'articleId' => $this->article->id,
                'title' => (string) ($this->article->title ?? $this->article->title_ru ?? ('#' . $this->article->id)),
                'authorName' => $this->article->author?->name,
                'authorMessage' => $this->authorMessage,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
