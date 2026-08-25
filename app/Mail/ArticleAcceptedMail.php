<?php

namespace App\Mail;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArticleAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Article $article,
    ) {
        $this->article->loadMissing('author');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Статья принята к публикации',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.article-accepted',
            with: [
                'authorName' => $this->article->author?->name,
                'title' => (string) ($this->article->title ?? $this->article->title_ru ?? ('#' . $this->article->id)),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
