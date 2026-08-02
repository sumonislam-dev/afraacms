<?php

namespace App\CMS\Cache;

class CmsCacheManager
{
    /**
     * Named cache-clearing callbacks registered by each CMS module.
     *
     * @var array<string, callable>
     */
    private array $clearers = [];

    /**
     * Register a module's cache-clearing callback under a unique name.
     *
     * Called from a service provider's boot(), e.g.:
     * $cache->register('settings', fn () => app(SettingService::class)->forget());
     */
    public function register(string $name, callable $clearer): void
    {
        $this->clearers[$name] = $clearer;
    }

    /**
     * Run every registered clearer and return the names that were cleared.
     *
     * @return array<int, string>
     */
    public function clear(): array
    {
        foreach ($this->clearers as $clearer) {
            $clearer();
        }

        return array_keys($this->clearers);
    }
}
