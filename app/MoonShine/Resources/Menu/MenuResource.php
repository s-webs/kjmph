<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Menu;

use Illuminate\Database\Eloquent\Model;
use App\Models\Menu;
use App\MoonShine\Resources\Menu\Pages\MenuIndexPage;
use App\MoonShine\Resources\Menu\Pages\MenuFormPage;
use App\MoonShine\Resources\Menu\Pages\MenuDetailPage;

use Leeto\MoonShineTree\Resources\TreeResource;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<Menu, MenuIndexPage, MenuFormPage, MenuDetailPage>
 */
class MenuResource extends TreeResource
{
    protected string $model = Menu::class;

    protected string $sortColumn = 'sort_order';

    protected string $title = 'Меню';

    protected string $column = 'name_ru';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            MenuIndexPage::class,
            MenuFormPage::class,
            MenuDetailPage::class,
        ];
    }

    public function treeKey(): ?string
    {
        return 'parent_id';
    }

    public function sortKey(): string
    {
        return 'sort_order';
    }
}
