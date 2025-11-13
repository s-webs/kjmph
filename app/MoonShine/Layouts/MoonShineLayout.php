<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use App\MoonShine\Resources\Setting\SettingResource;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\ColorManager\Palettes\NeutralPalette;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;
use MoonShine\MenuManager\MenuItem;
use App\MoonShine\Resources\Menu\MenuResource;
use App\MoonShine\Resources\Indexer\IndexerResource;
use App\MoonShine\Resources\Page\PageResource;

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
            MenuItem::make(MenuResource::class, 'Меню')->icon('bars-3-bottom-left'),
            MenuItem::make(PageResource::class, 'Страницы')->icon('newspaper'),
            MenuItem::make(IndexerResource::class, 'Индексаторы')->icon('globe-alt'),
            MenuItem::make(SettingResource::class)->icon('adjustments-vertical'),
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
