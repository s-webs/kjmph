<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Menu\Pages;

use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use App\MoonShine\Resources\Menu\MenuResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use Throwable;


/**
 * @extends FormPage<MenuResource>
 */
class MenuFormPage extends FormPage
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
                    Box::make('English', [
                        Text::make('Название на EN', 'name_en')->unescape(),
                        Text::make('Ссылка EN', 'link_en')->unescape(),
                    ])
                ])->columnSpan(4),
                Column::make([
                    Box::make('Russian', [
                        Text::make('Название на RU', 'name_ru')->unescape(),
                        Text::make('Ссылка RU', 'link_ru')->unescape(),
                    ])
                ])->columnSpan(4),
                Column::make([
                    Box::make('Kazakh', [
                        Text::make('Название на KK', 'name_kk')->unescape(),
                        Text::make('Ссылка KK', 'link_kk')->unescape(),
                    ])
                ])->columnSpan(4),
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
