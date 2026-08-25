<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Article\Pages;

use App\Models\Article;
use App\MoonShine\Resources\Article\ArticleResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Divider;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Throwable;

/**
 * @extends DetailPage<ArticleResource>
 */
class ArticleDetailPage extends DetailPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Основное', [
                ID::make(),
                Preview::make('Статус', 'publishing_process', formatted: static function (Article $item): string {
                    return (string) ($item->current_status ?? '—');
                })->badge('blue'),
                Text::make('Автор', 'author_id', formatted: static function (Article $item): string {
                    return (string) ($item->author?->name ?? '—');
                }),
                Text::make('Раздел', 'section'),
                Text::make('Язык материала', 'material_lang'),
                Text::make('DOI', 'doi'),
                Image::make('Обложка', 'cover')->disk('public'),
                File::make('Файл статьи', 'file')->disk('public'),
                Date::make('Дата подачи', 'created_at')->format('d.m.Y H:i'),
            ]),

            Divider::make(),

            Tabs::make([
                Tab::make('EN', [
                    Text::make('Заголовок', 'title_en'),
                    Text::make('Подзаголовок', 'subtitle_en'),
                    Textarea::make('Аннотация', 'annotation_en'),
                    Textarea::make('Литература', 'literature_en'),
                ]),
                Tab::make('RU', [
                    Text::make('Заголовок', 'title_ru'),
                    Text::make('Подзаголовок', 'subtitle_ru'),
                    Textarea::make('Аннотация', 'annotation_ru'),
                    Textarea::make('Литература', 'literature_ru'),
                ]),
                Tab::make('KK', [
                    Text::make('Заголовок', 'title_kk'),
                    Text::make('Подзаголовок', 'subtitle_kk'),
                    Textarea::make('Аннотация', 'annotation_kk'),
                    Textarea::make('Литература', 'literature_kk'),
                ]),
            ]),

            Divider::make(),

            Box::make('Соавторы и метаданные', [
                Json::make('Соавторы', 'coauthors'),
                Json::make('Метаданные', 'metadata'),
            ]),

            Divider::make(),

            Box::make('Процесс публикации', [
                Json::make('Таймлайн', 'publishing_process'),
                Json::make('История файлов', 'history'),
            ]),
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    /**
     * @param  TableBuilder  $component
     *
     * @return TableBuilder
     */
    protected function modifyDetailComponent(ComponentContract $component): ComponentContract
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
