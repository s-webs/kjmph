<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Article\Pages;

use App\Models\Article;
use App\MoonShine\Resources\Article\ArticleResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Divider;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Text;
use Throwable;

/**
 * @extends FormPage<ArticleResource>
 */
class ArticleFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Box::make('Служебные поля', [
                Preview::make('Заголовок', 'title_ru', formatted: static function (Article $item): string {
                    return (string) ($item->title ?? $item->title_ru ?? $item->title_en ?? '—');
                }),
                Preview::make('Автор', 'author_id', formatted: static function (Article $item): string {
                    return (string) ($item->author?->name ?? '—');
                }),
                Preview::make('Статус', 'publishing_process', formatted: static function (Article $item): string {
                    return (string) ($item->current_status ?? '—');
                }),
                Divider::make(),
                Text::make('Раздел', 'section'),
                Text::make('Язык материала', 'material_lang'),
                Text::make('DOI', 'doi'),
                File::make('Файл статьи', 'file')
                    ->disk('public')
                    ->dir('articles/files')
                    ->allowedExtensions(['pdf', 'doc', 'docx'])
                    ->disableDeleteFiles(),
            ]),
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    protected function formButtons(): ListOf
    {
        return parent::formButtons();
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'section' => ['nullable', 'string', 'max:255'],
            'material_lang' => ['nullable', 'string', 'max:255'],
            'doi' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @param  FormBuilder  $component
     *
     * @return FormBuilder
     */
    protected function modifyFormComponent(FormBuilderContract $component): FormBuilderContract
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
