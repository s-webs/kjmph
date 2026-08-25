<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Reviewer;

use App\Models\Reviewer;
use App\MoonShine\Resources\Reviewer\Pages\ReviewerDetailPage;
use App\MoonShine\Resources\Reviewer\Pages\ReviewerFormPage;
use App\MoonShine\Resources\Reviewer\Pages\ReviewerIndexPage;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Resources\ModelResource;

/**
 * @extends ModelResource<Reviewer, ReviewerIndexPage, ReviewerFormPage, ReviewerDetailPage>
 */
class ReviewerResource extends ModelResource
{
    protected string $model = Reviewer::class;

    protected string $title = 'Рецензенты';

    protected string $column = 'name';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            ReviewerIndexPage::class,
            ReviewerFormPage::class,
            ReviewerDetailPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'name',
            'email',
            'organisation',
            'specialization',
        ];
    }
}
