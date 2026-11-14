<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Author;

use App\Models\User;
use App\MoonShine\Resources\Author\Pages\AuthorDetailPage;
use App\MoonShine\Resources\Author\Pages\AuthorFormPage;
use App\MoonShine\Resources\Author\Pages\AuthorIndexPage;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\Resources\ModelResource;

/**
 * Авторы сайта (таблица users). Не путать с администраторами MoonShine.
 *
 * @extends ModelResource<User, AuthorIndexPage, AuthorFormPage, AuthorDetailPage>
 */
class AuthorResource extends ModelResource
{
    protected string $model = User::class;

    protected string $title = 'Авторы';

    protected string $column = 'name';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            AuthorIndexPage::class,
            AuthorFormPage::class,
            AuthorDetailPage::class,
        ];
    }

    protected function modifyQueryBuilder(Builder $builder): Builder
    {
        return $builder->withCount('articles');
    }

    /** Автор без доступа на сайт: пароль не нужен, задаём случайный. */
    protected function beforeCreating(DataWrapperContract $item): DataWrapperContract
    {
        /** @var User $user */
        $user = $item->getOriginal();

        if (blank($user->password)) {
            $user->password = Str::random(40);
        }

        return $item;
    }

    protected function search(): array
    {
        return [
            'id',
            'name',
            'email',
            'organisation',
            'country',
        ];
    }
}
