<?php

use App\CMS\Services\BannerService;

if (! function_exists('banner')) {
    /**
     * Get the current active banner for a placement, e.g. banner('cta'),
     * banner('popup'). Returns null if no banner is active for that type.
     *
     * @return array{id: int, title: ?string, subtitle: ?string, image_url: ?string, button_text: ?string, button_url: ?string}|null
     */
    function banner(string $type): ?array
    {
        return app(BannerService::class)->current($type);
    }
}
