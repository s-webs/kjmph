<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRole\MoonShineUserRoleResource;
use App\MoonShine\Resources\Setting\SettingResource;
use App\MoonShine\Resources\Menu\MenuResource;
use App\MoonShine\Resources\Indexer\IndexerResource;
use App\MoonShine\Resources\Page\PageResource;
use App\MoonShine\Resources\Release\ReleaseResource;
use App\MoonShine\Resources\Section\SectionResource;
use App\MoonShine\Pages\TestPage;
use App\MoonShine\Pages\TestPage2;
use App\MoonShine\Pages\ResetPasswordPage;
use App\MoonShine\Pages\LoginPage;
use App\MoonShine\Pages\RegisterPage;
use App\MoonShine\Pages\ForgotPage;
use App\MoonShine\Pages\ProfilePage;
use App\MoonShine\Pages\SubmissionArticlePage;
use App\MoonShine\Pages\SubmittedArticlesPage;
use App\MoonShine\Resources\Collection\CollectionResource;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  CoreContract<MoonShineConfigurator>  $core
     */
    public function boot(CoreContract $core): void
    {
        $core
            ->resources([
                MoonShineUserResource::class,
                MoonShineUserRoleResource::class,
                SettingResource::class,
                MenuResource::class,
                IndexerResource::class,
                PageResource::class,
                ReleaseResource::class,
                SectionResource::class,
                CollectionResource::class,
            ])
            ->pages([
                ...$core->getConfig()->getPages(),
                TestPage::class,
                TestPage2::class,
                ResetPasswordPage::class,
                LoginPage::class,
                RegisterPage::class,
                ForgotPage::class,
                ProfilePage::class,
                SubmissionArticlePage::class,
                SubmittedArticlesPage::class,
            ])
        ;
    }
}
