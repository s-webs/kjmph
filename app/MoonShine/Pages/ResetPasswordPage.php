<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;
use App\MoonShine\Layouts\FormLayout;
use Illuminate\Contracts\Routing\UrlRoutable;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Divider;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Fields\Hidden;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Text;
class ResetPasswordPage extends Page
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
        return $this->title ?: 'ForgotPage';
    }
    protected function components(): iterable
    {
        return [
            FormBuilder::make()
                ->class('authentication-form')
                ->action(route('password.update'))
                ->fields([
                    Hidden::make('token')->setValue(request()->route('token')),
                    Text::make('E-mail', 'email')
                        ->setValue(request()->input('email'))
                        ->required()
                        ->readonly(),
                    Password::make(__('Password'), 'password')
                        ->required(),
                    PasswordRepeat::make(__('Repeat password'), 'password_confirmation')
                        ->required(),
                ])->submit(__('Reset password'), [
                    'class' => 'btn-primary btn-lg w-full',
                ]),
        ];
    }
}
