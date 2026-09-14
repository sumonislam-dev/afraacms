<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Shared helpers for controllers that list a fully-cached array (News,
 * Stories, Projects, Gallery) rather than a live Eloquent query - filtering
 * and paginating happen in memory so the "all published items" cache these
 * lists are built from stays a single, reusable payload.
 */
trait PaginatesArrays
{
    /**
     * Keep only items whose given fields contain the search query
     * (case-insensitive). A blank/absent query returns the array unchanged.
     *
     * @param  array<int, array>  $items
     * @param  array<int, string>  $fields
     * @return array<int, array>
     */
    private function searchArray(array $items, ?string $query, array $fields = ['title', 'excerpt']): array
    {
        $query = trim((string) $query);

        if ($query === '') {
            return $items;
        }

        $needle = mb_strtolower($query);

        return array_values(array_filter($items, function (array $item) use ($fields, $needle) {
            foreach ($fields as $field) {
                if (str_contains(mb_strtolower((string) ($item[$field] ?? '')), $needle)) {
                    return true;
                }
            }

            return false;
        }));
    }

    /**
     * Slice a flat items array to the current page, per the given "items
     * per page" setting key (see GalleryController for the same pattern).
     *
     * @param  array<int, array>  $items
     */
    private function paginateArray(array $items, string $settingKey, int $default): LengthAwarePaginator
    {
        $perPage = max(1, (int) setting($settingKey, $default));
        $page = LengthAwarePaginator::resolveCurrentPage();

        return (new LengthAwarePaginator(
            array_slice($items, ($page - 1) * $perPage, $perPage),
            count($items),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        ))->appends(request()->except('page'));
    }
}
