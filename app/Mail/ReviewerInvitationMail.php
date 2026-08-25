<?php

namespace App\Mail;

use App\Models\ReviewAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewerInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ReviewAssignment $assignment,
    ) {
        $this->assignment->loadMissing(['reviewer', 'article']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Приглашение на рецензирование статьи',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reviewer-invitation',
            with: [
                'reviewerName' => $this->assignment->reviewer?->name,
                'title' => $this->assignment->anonymized_title,
                'deadline' => $this->assignment->deadline instanceof \DateTimeInterface
                    ? $this->assignment->deadline->format('d.m.Y')
                    : null,
                'portalUrl' => $this->assignment->portalUrl(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
