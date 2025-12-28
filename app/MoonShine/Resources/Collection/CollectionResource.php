<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Collection;

use Illuminate\Database\Eloquent\Model;
use App\Models\Collection;
use App\MoonShine\Resources\Collection\Pages\CollectionIndexPage;
use App\MoonShine\Resources\Collection\Pages\CollectionFormPage;
use App\MoonShine\Resources\Collection\Pages\CollectionDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<Collection, CollectionIndexPage, CollectionFormPage, CollectionDetailPage>
 */
class CollectionResource extends ModelResource
{
    protected string $model = Collection::class;

    protected string $title = 'Сборники';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            CollectionIndexPage::class,
            CollectionFormPage::class,
            CollectionDetailPage::class,
        ];
    }
}
