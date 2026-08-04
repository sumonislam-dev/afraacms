<?php

namespace App\CMS\Services;

use App\CMS\Services\Concerns\CachesForFrontend;
use App\Models\Page;
use App\Models\Section;
use App\Models\SeoMeta;
use Illuminate\Support\Collection;

class PageService
{
    use CachesForFrontend;

    protected function cacheKey(): string
    {
        return 'pages.published';
    }

    /**
     * Find a published page by slug, from cache where possible.
     *
     * Returns a plain array, not a Page model: the cache store's
     * serializable_classes hardening (see config/cache.php) strips objects
     * down to __PHP_Incomplete_Class on read, so only arrays/scalars may be
     * cached here (see MenuService for the same pattern).
     *
     * @return array{id: int, title: string, slug: string, template: string, content: ?string, updated_at: ?string, seo: array, sections: array}|null
     */
    public function findPublished(string $slug): ?array
    {
        return $this->allCached()[$slug] ?? null;
    }

    /**
     * Get the page currently configured as the homepage (settings.homepage_page_id),
     * or null if none is set or the selected page is no longer published.
     *
     * @return array{id: int, title: string, slug: string, template: string, content: ?string, updated_at: ?string, seo: array, sections: array}|null
     */
    public function homepage(): ?array
    {
        $id = setting('homepage_page_id');

        if (! $id) {
            return null;
        }

        return collect($this->allCached())->firstWhere('id', (int) $id);
    }

    /**
     * @return array<int, array>
     */
    public function all(): array
    {
        return array_values($this->allCached());
    }

    /**
     * Resolve the Blade template a cached page array should render with,
     * falling back to "default" if its stored template is no longer valid.
     */
    public function templateFor(array $page): string
    {
        return array_key_exists($page['template'], config('pages.templates', []))
            ? $page['template']
            : 'default';
    }

    /**
     * @return array<string, array>
     */
    private function allCached(): array
    {
        return $this->rememberForever(fn () => Page::published()
            ->with([
                'sections' => fn ($query) => $query->where('is_active', true)->with(['items', 'galleries']),
                'seo',
            ])
            ->get()
            ->mapWithKeys(fn (Page $page) => [
                $page->slug => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'template' => $page->template,
                    'content' => $page->content,
                    'banner_eyebrow' => $page->banner_eyebrow,
                    'banner_image_url' => $page->banner_image_url,
                    'updated_at' => $page->updated_at?->toIso8601String(),
                    'seo' => SeoMeta::toCacheArray($page->seo),
                    'sections' => $page->sections->map(fn (Section $section) => [
                        'type' => $section->type,
                        'anchor' => $section->anchor,
                        'heading' => $section->heading,
                        'subheading' => $section->subheading,
                        'body' => $section->body,
                        'image_url' => $section->image_url,
                        'button_text' => $section->button_text,
                        'button_url' => $section->button_url,
                        'layout' => $section->layout,
                        'gallery_ids' => $section->galleries->pluck('id')->all(),
                        'items' => $section->items->map(fn ($item) => [
                            'title' => $item->title,
                            'subtitle' => $item->subtitle,
                            'body' => $item->body,
                            'image_url' => $item->image_url,
                            'value' => $item->value,
                            'url' => $item->url,
                            'icon' => $item->icon,
                        ])->all(),
                    ])->all(),
                ],
            ])
            ->all());
    }

    /**
     * Create a new page.
     */
    public function create(array $data): Page
    {
        $page = Page::create($data);

        SeoMeta::syncFor($page, $data);

        $this->forget();

        return $page;
    }

    /**
     * Update an existing page.
     */
    public function update(Page $page, array $data): Page
    {
        $page->update($data);

        SeoMeta::syncFor($page, $data);

        $this->forget();

        return $page;
    }

    /**
     * Delete a page.
     */
    public function delete(Page $page): void
    {
        $page->delete();

        $this->forget();
    }

    /**
     * Restore a soft-deleted page.
     */
    public function restore(Page $page): Page
    {
        $page->restore();

        $this->forget();

        return $page;
    }

    /**
     * Permanently delete a soft-deleted page.
     */
    public function forceDelete(Page $page): void
    {
        $page->forceDelete();

        $this->forget();
    }
}
