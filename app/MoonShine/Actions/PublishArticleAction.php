<?php

declare(strict_types=1);

namespace App\MoonShine\Actions;

use App\Mail\ArticlePublishedMail;
use App\Models\Article;
use App\Models\Release;
use App\Models\Section;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Support\Enums\ToastType;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;

final class PublishArticleAction
{
    public static function button(?Article $article = null): ActionButtonContract
    {
        return ActionButton::make('Опубликовать')
            ->method('publishArticle', message: 'Статья опубликована')
            ->icon('globe-alt')
            ->primary()
            ->canSee(static fn (mixed $item): bool => $item instanceof Article
                && $item->current_status === 'ready_to_publish')
            ->withConfirm(
                title: 'Опубликовать статью',
                content: 'Статья появится в выбранном выпуске на сайте.',
                button: 'Опубликовать',
                fields: static fn (): array => [
                    Select::make('Выпуск', 'release_id')
                        ->options(self::releaseOptions())
                        ->searchable()
                        ->required(),
                    Select::make('Раздел', 'section_id')
                        ->options(self::sectionOptions())
                        ->searchable()
                        ->required(),
                    Text::make('DOI', 'doi'),
                ],
            );
    }

    public static function handle(Article $article, mixed $releaseId, mixed $sectionId, mixed $doi): void
    {
        if ($article->current_status !== 'ready_to_publish') {
            throw ValidationException::withMessages([
                'status' => 'Статья должна быть в статусе ready_to_publish',
            ]);
        }

        if (! filled($releaseId) || ! filled($sectionId)) {
            throw ValidationException::withMessages([
                'release_id' => 'Выберите выпуск и раздел',
            ]);
        }

        $release = Release::query()->find((int) $releaseId);
        $section = Section::query()->find((int) $sectionId);

        if (! $release || ! $section) {
            throw ValidationException::withMessages([
                'release_id' => 'Выпуск или раздел не найдены',
            ]);
        }

        $article->forceFill([
            'release_id' => $release->id,
            'section_id' => $section->id,
            'doi' => filled($doi) ? (string) $doi : null,
        ])->save();

        $article->addProcessStep('published', 'published', [
            'release_id' => $release->id,
            'section_id' => $section->id,
            'doi' => $article->doi,
        ]);

        if ($article->author?->email) {
            Mail::to($article->author->email)->send(
                new ArticlePublishedMail($article->fresh(['author', 'release', 'sectionModel']))
            );
        }

        toast('Статья опубликована', ToastType::SUCCESS);
    }

    /**
     * @return array<int, string>
     */
    private static function releaseOptions(): array
    {
        return Release::query()
            ->orderByDesc('published_at')
            ->orderByDesc('year')
            ->get()
            ->mapWithKeys(static function (Release $release): array {
                $label = trim(implode(' · ', array_filter([
                    (string) ($release->title ?? $release->title_ru ?? ('#' . $release->id)),
                    $release->year ? (string) $release->year : null,
                    $release->number ? ('№' . $release->number) : null,
                ])));

                return [$release->id => $label];
            })
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private static function sectionOptions(): array
    {
        return Section::query()
            ->orderBy('id')
            ->get()
            ->mapWithKeys(static fn (Section $section): array => [
                $section->id => (string) ($section->name ?? $section->name_ru ?? ('#' . $section->id)),
            ])
            ->all();
    }
}
