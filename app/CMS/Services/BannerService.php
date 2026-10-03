<?php

namespace App\CMS\Services;

use App\CMS\Services\Concerns\CachesForFrontend;
use App\Models\Banner;
use Illuminate\Support\Carbon;

class BannerService
{
    use CachesForFrontend;

    protected function cacheKey(): string
    {
        // Bump the version whenever the cached shape changes, so an entry
        // cached in the old shape is never read after deploy (v2: added
        // refresh_at; v3: added the popup_* options and version).
        return 'banners.active.v3';
    }

    /**
     * Get the current active banner for a placement, from cache where possible.
     *
     * Returns a plain array, not a Banner model: the cache store's
     * serializable_classes hardening (see config/cache.php) strips objects
     * down to __PHP_Incomplete_Class on read, so only arrays/scalars may be
     * cached here (see MenuService/PageService for the same pattern).
     *
     * @return array{id: int, title: ?string, subtitle: ?string, image_url: ?string, button_text: ?string, button_url: ?string, popup_frequency: string, popup_pages: string, popup_delay: int, version: ?int}|null
     */
    public function current(string $type): ?array
    {
        return $this->allCached()[$type] ?? null;
    }

    /**
     * The cached set is stamped with the next moment a banner's schedule
     * starts or ends (refresh_at, a Unix timestamp); once that has passed
     * it is rebuilt, so starts_at/ends_at take effect without an admin edit.
     *
     * @return array<string, array>
     */
    private function allCached(): array
    {
        $cached = $this->rememberForever(fn () => $this->build());

        if ($cached['refresh_at'] !== null && now()->getTimestamp() >= $cached['refresh_at']) {
            $this->forget();
            $cached = $this->rememberForever(fn () => $this->build());
        }

        return $cached['banners'];
    }

    /**
     * @return array{banners: array<string, array>, refresh_at: ?int}
     */
    private function build(): array
    {
        $banners = Banner::active()
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
                'popup_frequency' => $banner->popup_frequency,
                'popup_pages' => $banner->popup_pages,
                'popup_delay' => $banner->popup_delay,
                // Changes on every edit, so a popup's dismissal memory resets
                // and visitors see the updated content.
                'version' => $banner->updated_at?->getTimestamp(),
            ])
            ->all();

        // The next time the active set can change: a scheduled banner
        // starting, or an active one passing its (inclusive) ends_at.
        $nextStart = Banner::where('is_active', true)->where('starts_at', '>', now())->min('starts_at');
        $nextEnd = Banner::active()->whereNotNull('ends_at')->min('ends_at');

        $refreshAt = collect([
            $nextStart ? Carbon::parse($nextStart)->getTimestamp() : null,
            $nextEnd ? Carbon::parse($nextEnd)->getTimestamp() + 1 : null,
        ])->filter()->min();

        return ['banners' => $banners, 'refresh_at' => $refreshAt];
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
