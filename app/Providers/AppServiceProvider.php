<?php

namespace App\Providers;

use App\CMS\Cache\CmsCacheManager;
use App\CMS\Services\BannerService;
use App\CMS\Services\FeaturedVisitorService;
use App\CMS\Services\GalleryService;
use App\CMS\Services\MenuService;
use App\CMS\Services\NewsService;
use App\CMS\Services\PageService;
use App\CMS\Services\ProjectService;
use App\CMS\Services\SettingService;
use App\CMS\Services\StoryService;
use App\CMS\Services\TeamService;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // MySQL/MariaDB with utf8mb4 caps indexed key length at ~1000 bytes;
        // an unconstrained string() column (255 chars) needs 1020 bytes for a
        // unique/composite index, which overflows it. 191 chars keeps every
        // indexed string column (email, slugs, status+published_at, etc.)
        // under that limit. Columns that need to hold long unindexed text
        // (e.g. sections.subheading) use text() instead, so this cap never
        // truncates real content - it only ever narrows the implicit default
        // for columns that don't specify their own length.
        SchemaBuilder::defaultStringLength(191);

        $cache = $this->app->make(CmsCacheManager::class);
        $cache->register('settings', fn () => $this->app->make(SettingService::class)->forget());
        $cache->register('menus', fn () => $this->app->make(MenuService::class)->forget());
        $cache->register('pages', fn () => $this->app->make(PageService::class)->forget());
        $cache->register('banners', fn () => $this->app->make(BannerService::class)->forget());
        $cache->register('galleries', fn () => $this->app->make(GalleryService::class)->forget());
        $cache->register('projects', fn () => $this->app->make(ProjectService::class)->forget());
        $cache->register('team', fn () => $this->app->make(TeamService::class)->forget());
        $cache->register('featured_visitors', fn () => $this->app->make(FeaturedVisitorService::class)->forget());
        $cache->register('news', fn () => $this->app->make(NewsService::class)->forget());
        $cache->register('stories', fn () => $this->app->make(StoryService::class)->forget());

        Password::defaults(function () {
            $rule = Password::min(10)->mixedCase()->numbers();

            return $this->app->isProduction() ? $rule->uncompromised() : $rule;
        });
    }
}
