<?php

namespace App\Mail;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EditorReviewsCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Article $article,
    ) {
        $this->article->loadMissing(['reviewAssignments.reviewer', 'reviewAssignments.review']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Все рецензии получены: статья #' . $this->article->id,
        );
    }

    public function content(): Content
    {
        $title = (string) ($this->article->title ?? $this->article->title_ru ?? $this->article->title_en ?? ('#' . $this->article->id));

        return new Content(
            markdown: 'emails.editor-reviews-completed',
            with: [
                'articleId' => $this->article->id,
                'title' => $title,
                'assignments' => $this->article->reviewAssignments,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
