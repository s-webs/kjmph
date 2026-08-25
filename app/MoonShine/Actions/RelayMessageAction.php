<?php

declare(strict_types=1);

namespace App\MoonShine\Actions;

use App\Jobs\ForwardRelayMessageJob;
use App\Models\Article;
use App\Models\Message;
use App\Models\MessageThread;
use App\Models\Reviewer;
use Illuminate\Validation\ValidationException;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Support\Enums\ToastType;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Textarea;

final class RelayMessageAction
{
    public static function writeToAuthorButton(?Article $article = null): ActionButtonContract
    {
        return ActionButton::make('Написать автору')
            ->method('relayMessageToAuthor', message: 'Сообщение отправлено автору')
            ->icon('chat-bubble-left-right')
            ->canSee(static function (mixed $item): bool {
                return $item instanceof Article && (bool) $item->author_id;
            })
            ->withConfirm(
                title: 'Написать автору',
                content: 'Письмо уйдёт от имени редакции. Ответ автора придёт через relay.',
                button: 'Отправить',
                fields: static function (): array {
                    return [
                        Textarea::make('Сообщение', 'body')->required(),
                    ];
                },
            );
    }

    public static function writeToReviewerButton(?Article $article = null): ActionButtonContract
    {
        $options = self::reviewerOptions($article);
        $hasOptions = $options !== [];

        return ActionButton::make('Написать рецензенту')
            ->method('relayMessageToReviewer', message: 'Сообщение отправлено рецензенту')
            ->icon('chat-bubble-oval-left-ellipsis')
            ->canSee(function (mixed $item) use ($hasOptions): bool {
                return $item instanceof Article && $hasOptions;
            })
            ->withConfirm(
                title: 'Написать рецензенту',
                content: 'Письмо уйдёт анонимно от имени редакции.',
                button: 'Отправить',
                fields: function () use ($options): array {
                    return [
                        Select::make('Рецензент', 'reviewer_id')
                            ->options($options)
                            ->required(),
                        Textarea::make('Сообщение', 'body')->required(),
                    ];
                },
            );
    }

    public static function sendToAuthor(Article $article, ?string $body): void
    {
        $body = trim((string) $body);

        if ($body === '') {
            throw ValidationException::withMessages(['body' => 'Введите сообщение']);
        }

        $thread = MessageThread::query()
            ->where('article_id', $article->id)
            ->where('type', 'editor_author')
            ->whereNull('reviewer_id')
            ->first();

        if ($thread === null) {
            $thread = MessageThread::query()->create([
                'article_id' => $article->id,
                'type' => 'editor_author',
                'reviewer_id' => null,
            ]);
        }

        $message = Message::query()->create([
            'thread_id' => $thread->id,
            'sender_type' => 'editor',
            'body' => $body,
            'sent_at' => now(),
        ]);

        ForwardRelayMessageJob::dispatch($message->id);

        toast('Сообщение поставлено в очередь на отправку автору', ToastType::SUCCESS);
    }

    public static function sendToReviewer(Article $article, mixed $reviewerId, ?string $body): void
    {
        $body = trim((string) $body);
        $reviewerId = (int) $reviewerId;

        if ($body === '') {
            throw ValidationException::withMessages(['body' => 'Введите сообщение']);
        }

        $reviewer = Reviewer::query()->find($reviewerId);

        if (! $reviewer) {
            throw ValidationException::withMessages(['reviewer_id' => 'Рецензент не найден']);
        }

        $assigned = $article->reviewAssignments()
            ->where('reviewer_id', $reviewer->id)
            ->exists();

        if (! $assigned) {
            throw ValidationException::withMessages(['reviewer_id' => 'Рецензент не назначен на эту статью']);
        }

        $thread = MessageThread::query()->firstOrCreate([
            'article_id' => $article->id,
            'type' => 'editor_reviewer',
            'reviewer_id' => $reviewer->id,
        ]);

        $message = Message::query()->create([
            'thread_id' => $thread->id,
            'sender_type' => 'editor',
            'body' => $body,
            'sent_at' => now(),
        ]);

        ForwardRelayMessageJob::dispatch($message->id);

        toast('Сообщение поставлено в очередь на отправку рецензенту', ToastType::SUCCESS);
    }

    /**
     * @return array<int, string>
     */
    private static function reviewerOptions(?Article $article): array
    {
        if (! $article instanceof Article) {
            return [];
        }

        return $article->reviewAssignments()
            ->with('reviewer')
            ->get()
            ->filter(static fn ($assignment) => $assignment->reviewer)
            ->mapWithKeys(static fn ($assignment): array => [
                $assignment->reviewer_id => $assignment->reviewer->name . ' (' . $assignment->reviewer->email . ')',
            ])
            ->all();
    }
}
