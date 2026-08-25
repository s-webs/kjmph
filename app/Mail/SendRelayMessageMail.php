<?php

namespace App\Mail;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendRelayMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Message $message,
        public string $subjectLine,
    ) {
        $this->message->loadMissing(['thread.article']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                (string) config('mail.relay.from_address'),
                (string) config('mail.relay.from_name'),
            ),
            replyTo: [
                new Address($this->message->replyToAddress(), (string) config('mail.relay.from_name')),
            ],
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.relay-message',
            with: [
                'body' => $this->message->body,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
