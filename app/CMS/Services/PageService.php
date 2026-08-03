<?php

namespace App\CMS\Services;

use App\Models\Page;
use App\Models\Section;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PageService
{
    /**
     * Cache key holding every published page, keyed by slug.
     */
    private const CACHE_KEY = 'pages.published';

    /**
     * Find a published page by slug, from cache where possible.
     *
     * Returns a plain array, not a Page model: the cache store's
     * serializable_classes hardening (see config/cache.php) strips objects
     * down to __PHP_Incomplete_Class on read, so only arrays/scalars may be
     * cached here (see MenuService for the same pattern).
     *
     * @return array{id: int, title: string, slug: string, template: string, content: ?string, sections: array}|null
     */
    public function findPublished(string $slug): ?array
    {
        return $this->allCached()[$slug] ?? null;
    }

    /**
     * Get the page currently configured as the homepage (settings.homepage_page_id),
     * or null if none is set or the selected page is no longer published.
     *
     * @return array{id: int, title: string, slug: string, template: string, content: ?string, sections: array}|null
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
        return Cache::rememberForever(self::CACHE_KEY, fn () => Page::published()
            ->with(['sections' => fn ($query) => $query->where('is_active', true)->with('items')])
            ->get()
            ->mapWithKeys(fn (Page $page) => [
                $page->slug => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'template' => $page->template,
                    'content' => $page->content,
                    'sections' => $page->sections->map(fn (Section $section) => [
                        'type' => $section->type,
                        'heading' => $section->heading,
                        'subheading' => $section->subheading,
                        'body' => $section->body,
                        'image_url' => $section->image_url,
                        'button_text' => $section->button_text,
                        'button_url' => $section->button_url,
                        'layout' => $section->layout,
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

        $this->forget();

        return $page;
    }

    /**
     * Update an existing page.
     */
    public function update(Page $page, array $data): Page
    {
        $page->update($data);

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
     * Forget the cached page map so the next read repopulates it.
     */
    public function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
