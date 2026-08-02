<?php

namespace App\Providers;

use App\CMS\Cache\CmsCacheManager;
use App\CMS\Services\MenuService;
use App\CMS\Services\PageService;
use App\CMS\Services\SettingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CmsCacheManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $cache = $this->app->make(CmsCacheManager::class);
        $cache->register('settings', fn () => $this->app->make(SettingService::class)->forget());
        $cache->register('menus', fn () => $this->app->make(MenuService::class)->forget());
        $cache->register('pages', fn () => $this->app->make(PageService::class)->forget());
    }
}
