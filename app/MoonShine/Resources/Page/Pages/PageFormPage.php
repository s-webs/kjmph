<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Page\Pages;

use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use App\MoonShine\Resources\Page\PageResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Throwable;


/**
 * @extends FormPage<PageResource>
 */
class PageFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Tabs::make([
                Tab::make('EN', [
                    Grid::make([
                        Column::make([
                            Box::make('Контент', [
                                Text::make('Название', 'name_en')->unescape(),
                                TinyMce::make('Контент', 'text_en'),
                                Slug::make('Slug', 'slug_en')->from('name_en')->unescape(),
                            ])
                        ])->columnSpan(8),
                        Column::make([
                            Box::make('Seo', [
                                Text::make('Seo title', 'seo_title_en')->unescape(),
                                Text::make('Seo keywords', 'seo_keywords_en')->unescape(),
                                Textarea::make('Seo description', 'seo_description_en')->unescape(),
                            ]),
                        ])->columnSpan(4)
                    ])
                ]),
                Tab::make('RU', [
                    Grid::make([
                        Column::make([
                            Box::make('Контент', [
                                Text::make('Название', 'name_ru')->unescape(),
                                TinyMce::make('Контент', 'text_ru'),
                                Slug::make('Slug', 'slug_ru')->from('name_ru')->unescape(),
                            ])
                        ])->columnSpan(8),
                        Column::make([
                            Box::make('Seo', [
                                Text::make('Seo title', 'seo_title_ru')->unescape(),
                                Text::make('Seo keywords', 'seo_keywords_ru')->unescape(),
                                Textarea::make('Seo description', 'seo_description_ru')->unescape(),
                            ])
                        ])->columnSpan(4)
                    ])
                ]),
                Tab::make('KK', [
                    Grid::make([
                        Column::make([
                            Box::make('Контент', [
                                Text::make('Название', 'name_kk')->unescape(),
                                TinyMce::make('Контент', 'text_kk'),
                                Slug::make('Slug', 'slug_kk')->from('name_kk')->unescape(),
                            ])
                        ])->columnSpan(8),
                        Column::make([
                            Box::make('Seo', [
                                Text::make('Seo title', 'seo_title_kk')->unescape(),
                                Text::make('Seo keywords', 'seo_keywords_kk')->unescape(),
                                Textarea::make('Seo description', 'seo_description_kk')->unescape(),
                            ])
                        ])->columnSpan(4)
                    ])
                ]),
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
     * @param FormBuilder $component
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
