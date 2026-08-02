<?php

use App\CMS\Services\SettingService;

if (! function_exists('setting')) {
    /**
     * Get a cached setting's value by key, e.g. setting('site_name'), setting('logo').
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return app(SettingService::class)->get($key, $default);
    }
}
