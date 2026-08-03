<?php

namespace App\CMS\Services;

use App\CMS\Services\Concerns\CachesForFrontend;
use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Models\SeoMeta;
use Illuminate\Support\Facades\DB;

class GalleryService
{
    use CachesForFrontend;

    public function __construct(private readonly ProjectService $projects)
    {
    }

    protected function cacheKey(): string
    {
        return 'galleries.active';
    }

    /**
     * Find an active album by slug, from cache where possible.
     *
     * Returns a plain array, not a Gallery model: the cache store's
     * serializable_classes hardening (see config/cache.php) strips objects
     * down to __PHP_Incomplete_Class on read, so only arrays/scalars may be
     * cached here (see PageService/MenuService for the same pattern).
     *
     * @return array{title: string, slug: string, description: ?string, cover_image_url: ?string, updated_at: ?string, seo: array, items: array}|null
     */
    public function find(string $slug): ?array
    {
        return $this->allCached()[$slug] ?? null;
    }

    /**
     * @return array<int, array>
     */
    public function all(): array
    {
        return array_values($this->allCached());
    }

    /**
     * @return array<string, array>
     */
    private function allCached(): array
    {
        return $this->rememberForever(fn () => Gallery::active()
            ->orderBy('sort_order')
            ->with(['items', 'seo'])
            ->get()
            ->mapWithKeys(fn (Gallery $gallery) => [
                $gallery->slug => [
                    'id' => $gallery->id,
                    'title' => $gallery->title,
                    'slug' => $gallery->slug,
                    'description' => $gallery->description,
                    'cover_image_url' => $gallery->cover_image_url,
                    'updated_at' => $gallery->updated_at?->toIso8601String(),
                    'seo' => SeoMeta::toCacheArray($gallery->seo),
                    'items' => $gallery->items->map(fn (GalleryItem $item) => [
                        'type' => $item->type,
                        'image_url' => $item->image_url,
                        'embed_url' => $item->embed_url,
                        'caption' => $item->caption,
                    ])->all(),
                ],
            ])
            ->all());
    }

    /**
     * Create a new, empty album.
     */
    public function createAlbum(array $data): Gallery
    {
        $album = Gallery::create([
            ...$data,
            'sort_order' => Gallery::count(),
        ]);

        SeoMeta::syncFor($album, $data);

        $this->forget();

        return $album;
    }

    /**
     * Update an existing album's own fields.
     */
    public function updateAlbum(Gallery $album, array $data): Gallery
    {
        $album->update($data);

        SeoMeta::syncFor($album, $data);

        $this->forget();

        return $album;
    }

    /**
     * Delete an album (and, via cascading foreign keys, its items).
     */
    public function deleteAlbum(Gallery $album): void
    {
        $album->delete();

        $this->forget();
        $this->projects->forget();
    }

    /**
     * Persist a drag-and-drop reordered, flat list of album ids.
     *
     * @param  array<int, int>  $orderedIds
     */
    public function reorderAlbums(array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $index => $id) {
                Gallery::whereKey($id)->update(['sort_order' => $index]);
            }
        });

        $this->forget();
    }

    /**
     * Add a new item to the end of an album's repeatable list.
     */
    public function createItem(Gallery $album, array $data): GalleryItem
    {
        $item = $album->items()->create([
            ...$data,
            'sort_order' => $album->items()->count(),
        ]);

        $this->forget();
        $this->projects->forget();

        return $item;
    }

    /**
     * Update an existing item's own fields.
     */
    public function updateItem(GalleryItem $item, array $data): GalleryItem
    {
        $item->update($data);

        $this->forget();
        $this->projects->forget();

        return $item;
    }

    /**
     * Delete an item.
     */
    public function deleteItem(GalleryItem $item): void
    {
        $item->delete();

        $this->forget();
        $this->projects->forget();
    }

    /**
     * Persist a drag-and-drop reordered, flat list of item ids within an album.
     *
     * @param  array<int, int>  $orderedIds
     */
    public function reorderItems(Gallery $album, array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $index => $id) {
                GalleryItem::whereKey($id)->update(['sort_order' => $index]);
            }
        });

        $this->forget();
        $this->projects->forget();
    }
}
