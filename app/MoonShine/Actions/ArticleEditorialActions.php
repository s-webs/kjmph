<?php

declare(strict_types=1);

namespace App\MoonShine\Actions;

use App\Mail\ArticleAcceptedMail;
use App\Mail\ArticleLiteraryEditingMail;
use App\Mail\ArticleRejectedMail;
use App\Models\Article;
use App\Models\ArticleComment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Support\Enums\ToastType;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Fields\Textarea;

final class ArticleEditorialActions
{
    public static function sendToLiteraryEditingButton(?Article $article = null): ActionButtonContract
    {
        return ActionButton::make('Передать на литературную правку')
            ->method('sendToLiteraryEditing', message: 'Статья передана на литературную правку')
            ->icon('pencil-square')
            ->warning()
            ->canSee(static fn (mixed $item): bool => $item instanceof Article
                && in_array($item->current_status, ['review_completed', 'revision_submitted'], true))
            ->withConfirm(
                title: 'Литературная правка',
                content: 'Автор получит email с вашим комментарием.',
                button: 'Отправить',
                fields: static fn (): array => [
                    Textarea::make('Комментарий автору', 'message')->required(),
                ],
            );
    }

    public static function rejectButton(?Article $article = null): ActionButtonContract
    {
        return ActionButton::make('Отклонить статью')
            ->method('rejectArticle', message: 'Статья отклонена')
            ->icon('x-circle')
            ->error()
            ->canSee(static fn (mixed $item): bool => $item instanceof Article
                && ! in_array($item->current_status, ['rejected', 'published', 'ready_to_publish'], true))
            ->withConfirm(
                title: 'Отклонить статью',
                content: 'Автор получит уведомление с обоснованием.',
                button: 'Отклонить',
                fields: static fn (): array => [
                    Textarea::make('Обоснование', 'message')->required(),
                ],
            );
    }

    public static function acceptWithoutRevisionsButton(?Article $article = null): ActionButtonContract
    {
        return ActionButton::make('Принять без правок')
            ->method('acceptWithoutRevisions', message: 'Статья принята к публикации')
            ->icon('check-circle')
            ->success()
            ->canSee(static fn (mixed $item): bool => $item instanceof Article
                && in_array($item->current_status, ['review_completed', 'revision_submitted', 'literary_editing'], true))
            ->withConfirm(
                title: 'Принять без правок',
                content: 'Статья получит статус ready_to_publish. Автор будет уведомлён.',
                button: 'Принять',
            );
    }

    public static function sendToLiteraryEditing(Article $article, ?string $message): void
    {
        $message = trim((string) $message);

        if ($message === '') {
            throw ValidationException::withMessages([
                'message' => 'Укажите комментарий для автора',
            ]);
        }

        ArticleComment::query()->create([
            'article_id' => $article->id,
            'sender_type' => 'editor',
            'message' => $message,
        ]);

        $article->addProcessStep('literary_editing', 'literary_editing');

        if ($article->author?->email) {
            Mail::to($article->author->email)->send(
                new ArticleLiteraryEditingMail($article, $message)
            );
        }

        toast('Статья передана на литературную правку', ToastType::SUCCESS);
    }

    public static function reject(Article $article, ?string $message): void
    {
        $message = trim((string) $message);

        if ($message === '') {
            throw ValidationException::withMessages([
                'message' => 'Укажите обоснование отклонения',
            ]);
        }

        ArticleComment::query()->create([
            'article_id' => $article->id,
            'sender_type' => 'editor',
            'message' => $message,
        ]);

        $article->addProcessStep('rejected', 'rejected', [
            'reason' => $message,
        ]);

        if ($article->author?->email) {
            Mail::to($article->author->email)->send(
                new ArticleRejectedMail($article, $message)
            );
        }

        toast('Статья отклонена', ToastType::SUCCESS);
    }

    public static function acceptWithoutRevisions(Article $article): void
    {
        $article->addProcessStep('ready_to_publish', 'ready_to_publish');

        if ($article->author?->email) {
            Mail::to($article->author->email)->send(
                new ArticleAcceptedMail($article)
            );
        }

        toast('Статья принята к публикации', ToastType::SUCCESS);
    }
}
