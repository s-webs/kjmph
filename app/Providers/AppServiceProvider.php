<?php

namespace App\Providers;

use App\Models\Indexer;
use App\Models\Menu;
use App\Models\Setting;
use App\Services\Antiplagiat\AntiplagiatClient;
use App\Services\Antiplagiat\AntiplagiatService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AntiplagiatClient::class, function (): AntiplagiatClient {
            $config = config('antiplagiat');

            return new AntiplagiatClient(
                $config['login'],
                $config['password'],
                $config['company_name'],
                $config['apicorp_address'],
            );
        });

        $this->app->singleton(AntiplagiatService::class, function ($app): AntiplagiatService {
            return new AntiplagiatService(
                $app->make(AntiplagiatClient::class),
                (string) config('antiplagiat.company_name'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $settings = Setting::query()->first();
        $menuTree = Menu::roots()
            ->with('childrenRecursive')
            ->get();
        $indexers = Indexer::all();

        view()->share('settings', $settings);
        view()->share('menuTree', $menuTree);
        view()->share('indexers', $indexers);
    }
}
