<?php

namespace App\CMS\Services;

use App\CMS\Services\Concerns\CachesForFrontend;
use App\Models\Banner;

class BannerService
{
    use CachesForFrontend;

    protected function cacheKey(): string
    {
        return 'banners.active';
    }

    /**
     * Get the current active banner for a placement, from cache where possible.
     *
     * Returns a plain array, not a Banner model: the cache store's
     * serializable_classes hardening (see config/cache.php) strips objects
     * down to __PHP_Incomplete_Class on read, so only arrays/scalars may be
     * cached here (see MenuService/PageService for the same pattern).
     *
     * @return array{id: int, title: ?string, subtitle: ?string, image_url: ?string, button_text: ?string, button_url: ?string}|null
     */
    public function current(string $type): ?array
    {
        return $this->allCached()[$type] ?? null;
    }

    /**
     * @return array<string, array>
     */
    private function allCached(): array
    {
        return $this->rememberForever(fn () => Banner::active()
            ->orderBy('sort_order')
            ->get()
            ->groupBy('type')
            ->map(fn ($banners) => $banners->first())
            ->map(fn (Banner $banner) => [
                'id' => $banner->id,
                'title' => $banner->title,
                'subtitle' => $banner->subtitle,
                'image_url' => $banner->image_url,
                'button_text' => $banner->button_text,
                'button_url' => $banner->button_url,
            ])
            ->all());
    }

    /**
     * Create a new banner.
     */
    public function create(array $data): Banner
    {
        $banner = Banner::create($data);

        $this->forget();

        return $banner;
    }

    /**
     * Update an existing banner.
     */
    public function update(Banner $banner, array $data): Banner
    {
        $banner->update($data);

        $this->forget();

        return $banner;
    }

    /**
     * Delete a banner.
     */
    public function delete(Banner $banner): void
    {
        $banner->delete();

        $this->forget();
    }
}
