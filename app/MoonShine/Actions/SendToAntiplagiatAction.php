<?php

declare(strict_types=1);

namespace App\MoonShine\Actions;

use App\Jobs\CheckAntiplagiatJob;
use App\Models\Article;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Support\Enums\ToastType;
use MoonShine\UI\Components\ActionButton;
use RuntimeException;

final class SendToAntiplagiatAction
{
    public static function button(?Article $article = null): ActionButtonContract
    {
        $button = ActionButton::make('Отправить на антиплагиат')
            ->method(
                method: 'sendToAntiplagiat',
                message: 'Статья отправлена на проверку',
            )
            ->icon('shield-check')
            ->primary()
            ->canSee(static fn (mixed $item): bool => $item instanceof Article && filled($item->file));

        if ($article instanceof Article && filled($article->antiplagiat_doc_id)) {
            $button->withConfirm(
                title: 'Повторная проверка',
                content: 'Статья уже отправлялась на антиплагиат. Отправить повторно?',
                button: 'Отправить',
            );
        }

        return $button;
    }

    public static function handle(Article $article): void
    {
        if (! filled($article->file)) {
            throw new RuntimeException('У статьи нет файла для проверки антиплагиатом');
        }

        CheckAntiplagiatJob::dispatch($article->id);

        toast('Статья отправлена на проверку', ToastType::SUCCESS);
    }
}
