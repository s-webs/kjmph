<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Collection\Pages;

use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use App\MoonShine\Resources\Collection\CollectionResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Divider;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;
use Throwable;


/**
 * @extends FormPage<CollectionResource>
 */
class CollectionFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Grid::make([
                Column::make([
                    Box::make([
                        Tabs::make([
                            Tab::make('EN', [
                                Text::make('Название', 'title_en')->unescape(),
                                TinyMce::make('Описание', 'description_en'),
                                Slug::make('Slug', 'slug_en')->from('title_en')->unique(),
                            ]),
                            Tab::make('RU', [
                                Text::make('Название', 'title_ru')->unescape(),
                                TinyMce::make('Описание', 'description_ru'),
                                Slug::make('Slug', 'slug_ru')->from('title_ru')->unique(),
                            ]),
                            Tab::make('KK', [
                                Text::make('Название', 'title_kk')->unescape(),
                                TinyMce::make('Описание', 'description_kk'),
                                Slug::make('Slug', 'slug_kk')->from('title_kk')->unique(),
                            ]),
                        ])
                    ])
                ])->columnSpan(8),
                Column::make([
                    Box::make([
                        Image::make('Обложка', 'cover')
                            ->disk('public')
                            ->dir('collections/covers'),
                        File::make('Файл выпуска', 'file')
                            ->allowedExtensions(['pdf', 'doc', 'txt'])
                            ->disk('public')
                            ->dir('collections/issues'),
                        Divider::make(),
                        Grid::make([
                            Column::make([
                                Number::make('Том', 'volume')
                            ])->columnSpan(4),
                            Column::make([
                                Number::make('Номер', 'number')
                            ])->columnSpan(4),
                            Column::make([
                                Number::make('Год', 'year')
                            ])->columnSpan(4),
                        ]),
                        Divider::make(),
                        Text::make('DOI', 'doi'),
                        Divider::make(),
                        Date::make('Дата публикации', 'published_at')
                    ])
                ])->columnSpan(4)
            ])
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
        return [];
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
            ...parent::topLayer()
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer()
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer()
        ];
    }
}
