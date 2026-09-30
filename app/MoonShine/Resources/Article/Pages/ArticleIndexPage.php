<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Article\Pages;

use App\Models\Article;
use App\MoonShine\Resources\Article\ArticleResource;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;
use Throwable;

/**
 * @extends IndexPage<ArticleResource>
 */
class ArticleIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Заголовок', 'title_ru', formatted: static function (Article $item): string {
                return (string) ($item->title ?? $item->title_ru ?? $item->title_en ?? $item->title_kk ?? '—');
            }),
            Text::make('Автор', 'author_id', formatted: static function (Article $item): string {
                return (string) ($item->author?->name ?? '—');
            }),
            Text::make('Выпуск', 'release_id', formatted: static function (Article $item): string {
                return (string) ($item->release?->title_ru ?? $item->release?->title_en ?? '—');
            }),
            Preview::make('Статус', 'publishing_process', formatted: static function (Article $item): string {
                return (string) ($item->current_status ?? '—');
            })->badge('blue'),
            Date::make('Дата подачи', 'created_at')->format('d.m.Y H:i')->sortable(),
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    /**
     * @return list<FieldContract>
     */
    protected function filters(): iterable
    {
        return [
            Select::make('Статус', 'status')
                ->options([
                    'submitted' => 'Подана (submitted)',
                    'antiplagiat_pending' => 'Антиплагиат (pending)',
                    'antiplagiat_done' => 'Антиплагиат (done)',
                    'antiplagiat_failed' => 'Антиплагиат (failed)',
                    'under_review' => 'На рецензии (under_review)',
                    'revision_required' => 'Требуется правка (revision_required)',
                    'accepted' => 'Принята (accepted)',
                    'ready_to_publish' => 'Ожидает выпуска (ready_to_publish)',
                    'published' => 'Опубликована (published)',
                    'rejected' => 'Отклонена (rejected)',
                ])
                ->nullable()
                ->onApply(static function (Builder $query, mixed $value): void {
                    if (filled($value)) {
                        $query->whereCurrentStatus((string) $value);
                    }
                }),
        ];
    }

    /**
     * @return list<QueryTag>
     */
    protected function queryTags(): array
    {
        return [
            QueryTag::make('Все', static fn (Builder $query) => $query)->default(),
            QueryTag::make(
                'Новые',
                static fn (Builder $query) => $query->whereCurrentStatus('submitted')
            ),
            QueryTag::make(
                'На рецензии',
                static fn (Builder $query) => $query->whereCurrentStatus('under_review')
            ),
            QueryTag::make(
                'Ожидают выпуск',
                static fn (Builder $query) => $query->whereCurrentStatus('ready_to_publish')
            ),
            QueryTag::make(
                'Опубликованные',
                static fn (Builder $query) => $query->whereCurrentStatus('published')
            ),
        ];
    }

    /**
     * @return list<Metric>
     */
    protected function metrics(): array
    {
        return [];
    }

    /**
     * @param  TableBuilder  $component
     *
     * @return TableBuilder
     */
    protected function modifyListComponent(ComponentContract $component): ComponentContract
    {
        return $component;
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function topLayer(): array
    {
        return [
            ...parent::topLayer(),
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer(),
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer(),
        ];
    }
}
