<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Author\Pages;

use App\Models\User;
use App\MoonShine\Resources\Author\AuthorResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<AuthorResource>
 */
class AuthorIndexPage extends IndexPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Имя', 'name')->sortable(),
            Email::make('Email', 'email')->sortable(),
            Text::make('Организация', 'organisation'),
            Text::make('Страна', 'country'),
            Text::make('Статей', 'articles_count', formatted: static fn (User $item): string => (string) ($item->articles_count ?? 0))
                ->sortable(),
            Date::make('Регистрация', 'created_at')->format('d.m.Y H:i')->sortable(),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function filters(): iterable
    {
        return [
            Text::make('Имя', 'name'),
            Text::make('Email', 'email'),
            Text::make('Организация', 'organisation'),
            Text::make('Страна', 'country'),
        ];
    }
}
