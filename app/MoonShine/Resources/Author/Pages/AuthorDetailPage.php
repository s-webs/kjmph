<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Author\Pages;

use App\Models\User;
use App\MoonShine\Resources\Author\AuthorResource;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Text;

/**
 * @extends DetailPage<AuthorResource>
 */
class AuthorDetailPage extends DetailPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                Text::make('Имя', 'name'),
                Email::make('Email', 'email'),
                Text::make('Организация', 'organisation'),
                Text::make('Страна', 'country'),
                Preview::make('Статьи', 'id', formatted: static function (User $item): string {
                    $titles = $item->articles()->latest('id')->get()
                        ->map(static fn ($a) => e((string) ($a->title ?? '#' . $a->id)))
                        ->all();

                    return $titles === [] ? '—' : implode('<br>', $titles);
                })->unescape(),
                Date::make('Регистрация', 'created_at')->format('d.m.Y H:i'),
            ]),
        ];
    }
}
