<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

namespace App\MoonShine\Pages;
use App\MoonShine\Layouts\FormLayout;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
class RegisterPage extends Page
{
    protected ?string $layout = FormLayout::class;
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle()
        ];
    }
    public function getTitle(): string
    {
        return $this->title ?: 'RegisterPage';
    }
    protected function components(): iterable
    {
        return [
            FormBuilder::make()
                ->class('authentication-form')
                ->action(route('register.store'))
                ->fields([
                    Text::make(__('Name'), 'name')->required(),
                    Text::make(__('auth.organisation'), 'organisation')->required(),
                    Text::make(__('auth.country'), 'country')->required(),
                    Text::make('E-mail', 'email')
                        ->required()
                        ->customAttributes([
                            'autofocus' => true,
                            'autocomplete' => 'off',
                        ]),
                    Password::make(__('Password'), 'password')
                        ->required(),
                    PasswordRepeat::make(__('Repeat password'), 'password_confirmation')
                        ->required(),
                ])->submit(__('Create account'), [
                    'class' => 'btn-primary btn-lg w-full',
                ])->buttons([
                    ActionButton::make(__('Log in'), route('login'))
                ])
        ];
    }
}
