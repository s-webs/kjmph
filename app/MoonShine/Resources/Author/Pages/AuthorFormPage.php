<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Author\Pages;

use App\MoonShine\Resources\Author\AuthorResource;
use Illuminate\Validation\Rule;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<AuthorResource>
 */
class AuthorFormPage extends FormPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Box::make('Автор', [
                Text::make('Имя', 'name')->required(),
                Email::make('Email', 'email')->required(),
                Text::make('Организация', 'organisation')->required(),
                Text::make('Страна', 'country')->required(),
                Password::make('Пароль', 'password')
                    ->hint('Необязательно. Если при создании оставить пустым, автор не сможет войти на сайт (пароль будет случайным). При редактировании пустое поле пароль не меняет'),
            ]),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($item->getKey())],
            'organisation' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }
}
