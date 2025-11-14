<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use App\MoonShine\Resources\Setting\Pages\SettingFormPage;
use App\MoonShine\Resources\Setting\SettingResource;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\ColorManager\Palettes\NeutralPalette;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use App\MoonShine\Resources\Menu\MenuResource;
use App\MoonShine\Resources\Indexer\IndexerResource;
use App\MoonShine\Resources\Page\PageResource;
use App\MoonShine\Resources\Release\ReleaseResource;
use App\MoonShine\Resources\Section\SectionResource;

final class MoonShineLayout extends CustomAppLayout
{
    /**
     * @var null|class-string<PaletteContract>
     */
    protected ?string $palette = NeutralPalette::class;

    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    protected function menu(): array
    {
        return [
            ...parent::menu(),
            MenuGroup::make('Сайт', [
                MenuItem::make(MenuResource::class)->icon('bars-3-bottom-left'),
                MenuItem::make(PageResource::class)->icon('newspaper'),
                MenuItem::make(IndexerResource::class)->icon('globe-alt'),
                MenuItem::make('/jms/resource/setting-resource/setting-form-page/1', 'Настройки')->icon('adjustments-vertical'),
            ]),

            MenuGroup::make('Журнал', [
                MenuItem::make(SectionResource::class)->icon('list-bullet'),
                MenuItem::make(ReleaseResource::class)->icon('document-text'),
            ]),
        ];
    }

    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        // $colorManager->primary('#00000');
    }
}
