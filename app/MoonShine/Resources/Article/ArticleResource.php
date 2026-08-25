<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Article;

use App\Models\Article;
use App\MoonShine\Resources\Article\Pages\ArticleDetailPage;
use App\MoonShine\Resources\Article\Pages\ArticleFormPage;
use App\MoonShine\Resources\Article\Pages\ArticleIndexPage;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

/**
 * @extends ModelResource<Article, ArticleIndexPage, ArticleFormPage, ArticleDetailPage>
 */
class ArticleResource extends ModelResource
{
    protected string $model = Article::class;

    protected string $title = 'Статьи';

    protected string $column = 'title_ru';

    protected array $with = ['author', 'reviewAssignments.reviewer', 'comments', 'release', 'sectionModel'];

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            ArticleIndexPage::class,
            ArticleFormPage::class,
            ArticleDetailPage::class,
        ];
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::CREATE, Action::MASS_DELETE);
    }

    protected function search(): array
    {
        return [
            'id',
            'title_en',
            'title_ru',
            'title_kk',
            'doi',
        ];
    }
}
