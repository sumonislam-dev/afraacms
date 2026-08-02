<?php

use App\CMS\Services\MenuService;

if (! function_exists('menu')) {
    /**
     * Get a menu (with its active items, nested into a tree) by slug, e.g.
     * menu('header'), menu('footer'). Returns null if no such menu exists.
     *
     * @return array{name: string, slug: string, tree: array}|null
     */
    function menu(string $slug): ?array
    {
        return app(MenuService::class)->render($slug);
    }
}
