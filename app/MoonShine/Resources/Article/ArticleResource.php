<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Article;

use App\Models\Article;
use App\MoonShine\Resources\Article\Pages\ArticleDetailPage;
use App\MoonShine\Resources\Article\Pages\ArticleFormPage;
use App\MoonShine\Resources\Article\Pages\ArticleIndexPage;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
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
        return parent::activeActions()->except(Action::MASS_DELETE);
    }

    protected function afterCreated(DataWrapperContract $item): DataWrapperContract
    {
        /** @var Article $article */
        $article = $item->getOriginal();
        $article->section_id = (int) $article->section;
        $article->save();
        $published = $article->release_id !== null;
        $article->addProcessStep($published ? 'published' : 'ready_to_publish', $published
            ? 'article_published_by_editor' : 'article_added_by_editor', [
            'release_id' => $article->release_id,
            'section_id' => $article->section_id,
        ]);

        return $item;
    }

    protected function afterUpdated(DataWrapperContract $item): DataWrapperContract
    {
        /** @var Article $article */
        $article = $item->getOriginal();
        $article->section_id = (int) $article->section;
        $article->save();

        $lastStep = last($article->publishing_process ?? []);
        $timelineStatus = is_array($lastStep) ? ($lastStep['status'] ?? null) : null;

        if ($article->release_id === null && $timelineStatus === 'published') {
            $article->addProcessStep('ready_to_publish', 'article_removed_from_release');
        } elseif ($article->release_id !== null && $timelineStatus !== 'published') {
            $article->addProcessStep('published', 'article_published_by_editor', [
                'release_id' => $article->release_id,
                'section_id' => $article->section_id,
            ]);
        }

        return $item;
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
