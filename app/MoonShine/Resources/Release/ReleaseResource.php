<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Release;

use Illuminate\Database\Eloquent\Model;
use App\Models\Release;
use App\MoonShine\Resources\Release\Pages\ReleaseIndexPage;
use App\MoonShine\Resources\Release\Pages\ReleaseFormPage;
use App\MoonShine\Resources\Release\Pages\ReleaseDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<Release, ReleaseIndexPage, ReleaseFormPage, ReleaseDetailPage>
 */
class ReleaseResource extends ModelResource
{
    protected string $model = Release::class;

    protected string $title = 'Releases';
    
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            ReleaseIndexPage::class,
            ReleaseFormPage::class,
            ReleaseDetailPage::class,
        ];
    }
}
