<?php

namespace App\MoonShine\Layouts\Components;

use MoonShine\UI\Components\MoonShineComponent;

class CustomFavicon extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.favicon';

    /**
     * @param array{
     *     apple-touch?: string,
     *     32?: string,
     *     16?: string,
     *     safari-pinned-tab?: string,
     *     web-manifest?: string,
     * } $customAssets
     */
    public function __construct(
        private array   $customAssets = [],
        private ?string $bodyColor = null
    )
    {
        parent::__construct();
    }

    /**
     * @param array{
     *     apple-touch: string,
     *     32: string,
     *     16: string,
     *     safari-pinned-tab: string,
     *     web-manifest: string,
     * } $assets
     */
    public function customAssets(array $assets): self
    {
        $this->customAssets = $assets;

        return $this;
    }

    public function bodyColor(string $color): self
    {
        $this->bodyColor = $color;

        return $this;
    }

    protected function viewData(): array
    {
        return [
            'assets' => $this->customAssets ?: [
                'apple-touch' => $this->getAssetManager()->getAsset('/assets/img/favicon-180.png'),
                '32' => $this->getAssetManager()->getAsset('/assets/img/favicon-32.png'),
                '16' => $this->getAssetManager()->getAsset('/assets/img/favicon-16.png'),
                'safari-pinned-tab' => $this->getAssetManager()->getAsset('/vendor/moonshine/safari-pinned-tab.svg'),
                'web-manifest' => $this->getAssetManager()->getAsset('/vendor/moonshine/site.webmanifest'),
            ],
            'bodyColor' => $this->bodyColor,
        ];
    }
}
