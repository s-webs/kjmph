<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\MoonShine\Layouts\AutorLayout;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;


class SubmissionArticlePage extends Page
{

    protected ?string $layout = AutorLayout::class;

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle()
        ];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'SubmissionArticlePage';
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {
        return [];
    }
}
