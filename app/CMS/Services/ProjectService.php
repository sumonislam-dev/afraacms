<?php

namespace App\CMS\Services;

use App\Models\GalleryItem;
use App\Models\Project;
use Illuminate\Support\Facades\Cache;

class ProjectService
{
    /**
     * Cache key holding every published project (with its category and
     * attached gallery items, if any), keyed by slug.
     */
    private const CACHE_KEY = 'projects.published';

    /**
     * Find a published project by slug, from cache where possible.
     *
     * Returns a plain array, not a Project model: the cache store's
     * serializable_classes hardening (see config/cache.php) strips objects
     * down to __PHP_Incomplete_Class on read, so only arrays/scalars may be
     * cached here (see PageService/GalleryService for the same pattern).
     *
     * @return array{title: string, slug: string, excerpt: ?string, content: ?string, cover_image_url: ?string, is_featured: bool, category: ?array, gallery_items: array}|null
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
        return Cache::rememberForever(self::CACHE_KEY, fn () => Project::published()
            ->with(['category', 'gallery.items'])
            ->orderByDesc('is_featured')
            ->latest()
            ->get()
            ->mapWithKeys(fn (Project $project) => [
                $project->slug => [
                    'title' => $project->title,
                    'slug' => $project->slug,
                    'excerpt' => $project->excerpt,
                    'content' => $project->content,
                    'cover_image_url' => $project->cover_image_url,
                    'is_featured' => $project->is_featured,
                    'category' => $project->category ? [
                        'name' => $project->category->name,
                        'slug' => $project->category->slug,
                    ] : null,
                    'gallery_items' => $project->gallery
                        ? $project->gallery->items->map(fn (GalleryItem $item) => [
                            'type' => $item->type,
                            'image_url' => $item->image_url,
                            'embed_url' => $item->embed_url,
                            'caption' => $item->caption,
                        ])->all()
                        : [],
                ],
            ])
            ->all());
    }

    /**
     * Create a new project.
     */
    public function create(array $data): Project
    {
        $project = Project::create($data);

        $this->forget();

        return $project;
    }

    /**
     * Update an existing project.
     */
    public function update(Project $project, array $data): Project
    {
        $project->update($data);

        $this->forget();

        return $project;
    }

    /**
     * Delete a project.
     */
    public function delete(Project $project): void
    {
        $project->delete();

        $this->forget();
    }

    /**
     * Forget the cached project map so the next read repopulates it.
     */
    public function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
