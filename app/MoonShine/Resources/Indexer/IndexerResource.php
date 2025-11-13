<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Indexer;

use Illuminate\Database\Eloquent\Model;
use App\Models\Indexer;
use App\MoonShine\Resources\Indexer\Pages\IndexerIndexPage;
use App\MoonShine\Resources\Indexer\Pages\IndexerFormPage;
use App\MoonShine\Resources\Indexer\Pages\IndexerDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<Indexer, IndexerIndexPage, IndexerFormPage, IndexerDetailPage>
 */
class IndexerResource extends ModelResource
{
    protected string $model = Indexer::class;

    protected string $title = 'Индексаторы';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            IndexerIndexPage::class,
            IndexerFormPage::class,
            IndexerDetailPage::class,
        ];
    }
}
