<?php

namespace App\Mail;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArticlePublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Article $article,
    ) {
        $this->article->loadMissing(['author', 'release', 'sectionModel']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ваша статья опубликована',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.article-published',
            with: [
                'authorName' => $this->article->author?->name,
                'title' => (string) ($this->article->title ?? $this->article->title_ru ?? ('#' . $this->article->id)),
                'doi' => $this->article->doi,
                'doiUrl' => $this->article->doi ? 'https://doi.org/' . ltrim($this->article->doi, '/') : null,
                'releaseUrl' => route('journal.release'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
