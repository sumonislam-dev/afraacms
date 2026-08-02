<?php

namespace App\Providers;

use App\CMS\Cache\CmsCacheManager;
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
        $this->app->make(CmsCacheManager::class)
            ->register('settings', fn () => $this->app->make(SettingService::class)->forget());
    }
}
