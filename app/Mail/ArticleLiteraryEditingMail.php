<?php

namespace App\Mail;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArticleLiteraryEditingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Article $article,
        public string $editorMessage,
    ) {
        $this->article->loadMissing('author');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Требуется литературная правка статьи',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.article-literary-editing',
            with: [
                'authorName' => $this->article->author?->name,
                'title' => (string) ($this->article->title ?? $this->article->title_ru ?? ('#' . $this->article->id)),
                'editorMessage' => $this->editorMessage,
                'detailsUrl' => route('author.article.details', $this->article),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
