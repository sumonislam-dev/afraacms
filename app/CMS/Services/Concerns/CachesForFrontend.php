<?php

namespace App\CMS\Services\Concerns;

use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * The "cache one array/collection forever under a fixed key, invalidate on
 * demand" pattern shared by every CMS Service that backs a frontend read
 * (Pages, Menus, Galleries, Projects, Banners, Settings): compute once,
 * cache forever, and forget on the next write so it recomputes.
 */
trait CachesForFrontend
{
    /**
     * The cache key this service's data is stored under.
     */
    abstract protected function cacheKey(): string;

    /**
     * Get the cached value, computing and storing it forever on a miss.
     */
    protected function rememberForever(Closure $callback): mixed
    {
        return Cache::rememberForever($this->cacheKey(), $callback);
    }

    /**
     * Forget the cached value so the next read repopulates it.
     */
    public function forget(): void
    {
        Cache::forget($this->cacheKey());
    }
}
