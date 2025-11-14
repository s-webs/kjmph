<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Section;

use Illuminate\Database\Eloquent\Model;
use App\Models\Section;
use App\MoonShine\Resources\Section\Pages\SectionIndexPage;
use App\MoonShine\Resources\Section\Pages\SectionFormPage;
use App\MoonShine\Resources\Section\Pages\SectionDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<Section, SectionIndexPage, SectionFormPage, SectionDetailPage>
 */
class SectionResource extends ModelResource
{
    protected string $model = Section::class;

    protected bool $createInModal = true;

    protected bool $editInModal = true;

    protected bool $detailInModal = true;

    protected string $title = 'Разделы';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            SectionIndexPage::class,
            SectionFormPage::class,
            SectionDetailPage::class,
        ];
    }
}
