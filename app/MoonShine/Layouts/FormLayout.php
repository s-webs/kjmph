<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\UI\Components\{Components,
    FlexibleRender,
    Heading,
    Layout\Div,
    Layout\Body,
    Layout\Content,
    Layout\Flash,
    Layout\Html,
    Layout\Layout,
    Layout\Wrapper};
final class FormLayout extends AppLayout
{
    protected function getHomeUrl(): string
    {
        return route('home');
    }
    public function build(): Layout
    {
        return Layout::make([
            Html::make([
                $this->getHeadComponent(),
                Body::make([
                    Div::make([
                        Div::make([
                            $this->getLogoComponent(),
                        ])->class('authentication-logo'),
                        Div::make([
                            Flash::make(),
                            Components::make($this->getPage()->getComponents()),
                        ])->class('authentication-content'),
                    ])->class('authentication'),
                ]),
            ])
                ->customAttributes([
                    'lang' => $this->getHeadLang(),
                ])
                ->withAlpineJs()
                ->withThemes(),
        ]);
    }
}
