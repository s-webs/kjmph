<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Message extends Model
{
    protected $fillable = [
        'thread_id',
        'sender_type',
        'body',
        'raw_email',
        'reply_token',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Message $message): void {
            if (! filled($message->reply_token)) {
                $message->reply_token = (string) Str::uuid();
            }

            if ($message->sent_at === null) {
                $message->sent_at = now();
            }
        });
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(MessageThread::class, 'thread_id');
    }

    public function replyToAddress(): string
    {
        $domain = (string) config('mail.relay.domain', 'kjmph.kz');

        return 'reply+' . $this->reply_token . '@' . $domain;
    }
}
