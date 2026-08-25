<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Article\Pages;

use App\Models\Article;
use App\MoonShine\Actions\SendToAntiplagiatAction;
use App\MoonShine\Resources\Article\ArticleResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Support\Attributes\AsyncMethod;
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
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Url;
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

            Box::make('Антиплагиат', [
                Number::make('Оригинальность, %', 'antiplagiat_percent')
                    ->badge(static function (mixed $value): string {
                        if ($value === null || $value === '') {
                            return 'gray';
                        }

                        $percent = (float) $value;

                        return $percent >= 70 ? 'green' : ($percent >= 50 ? 'yellow' : 'red');
                    }),
                Date::make('Дата проверки', 'antiplagiat_checked_at')->format('d.m.Y H:i'),
                Text::make('Doc ID', 'antiplagiat_doc_id'),
                Url::make(
                    'Отчёт',
                    'antiplagiat_report',
                    formatted: static function (Article $item): ?string {
                        $url = data_get($item->antiplagiat_report, 'report_url');

                        return is_string($url) ? $url : null;
                    }
                )->blank(),
                Json::make('Отчёт (JSON)', 'antiplagiat_report'),
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
        /** @var Article|null $article */
        $article = $this->getResource()->getItem();

        return parent::buttons()->add(
            SendToAntiplagiatAction::button($article instanceof Article ? $article : null),
        );
    }

    #[AsyncMethod]
    public function sendToAntiplagiat(): void
    {
        /** @var Article|null $article */
        $article = $this->getResource()->getItem();

        if (! $article instanceof Article) {
            throw new \RuntimeException('Статья не найдена');
        }

        SendToAntiplagiatAction::handle($article);
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
