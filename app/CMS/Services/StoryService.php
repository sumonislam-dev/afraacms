<?php

namespace App\CMS\Services;

use App\CMS\Services\Concerns\CachesForFrontend;
use App\Models\SeoMeta;
use App\Models\Story;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class StoryService
{
    use CachesForFrontend;

    protected function cacheKey(): string
    {
        return 'stories.published';
    }

    /**
     * Find a published story by slug, from cache where possible.
     *
     * Returns a plain array, not a Story model: the cache store's
     * serializable_classes hardening (see config/cache.php) strips objects
     * down to __PHP_Incomplete_Class on read, so only arrays/scalars may be
     * cached here (see NewsService for the same pattern).
     *
     * @return array{id: int, title: string, slug: string, excerpt: ?string, content: ?string, cover_image_url: ?string, attachment_url: ?string, published_at: ?string, is_featured: bool, updated_at: ?string, seo: array, category_id: ?int, category: ?array, project: ?array}|null
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
        return $this->rememberForever(fn () => Story::published()
            ->with(['project', 'category', 'seo'])
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->get()
            ->mapWithKeys(fn (Story $story) => [
                $story->slug => [
                    'id' => $story->id,
                    'title' => $story->title,
                    'slug' => $story->slug,
                    'excerpt' => $story->excerpt,
                    'content' => $story->content,
                    'cover_image_url' => $story->cover_image_url,
                    'attachment_url' => $story->attachment_url,
                    'published_at' => $story->published_at?->toDateString(),
                    'is_featured' => $story->is_featured,
                    'updated_at' => $story->updated_at?->toIso8601String(),
                    'seo' => SeoMeta::toCacheArray($story->seo),
                    'category_id' => $story->category_id,
                    'category' => $story->category ? [
                        'name' => $story->category->name,
                        'slug' => $story->category->slug,
                    ] : null,
                    'project' => $story->project ? [
                        'title' => $story->project->title,
                        'slug' => $story->project->slug,
                    ] : null,
                ],
            ])
            ->all());
    }

    /**
     * Create a new story.
     */
    public function create(array $data): Story
    {
        $attachment = Arr::pull($data, 'attachment');

        $story = Story::create($data);

        if ($attachment instanceof UploadedFile) {
            $story->addMedia($attachment)->toMediaCollection('attachment');
        }

        SeoMeta::syncFor($story, $data);

        $this->forget();

        return $story;
    }

    /**
     * Update an existing story.
     */
    public function update(Story $story, array $data): Story
    {
        $attachment = Arr::pull($data, 'attachment');
        $removeAttachment = Arr::pull($data, 'remove_attachment', false);

        $story->update($data);

        if ($attachment instanceof UploadedFile) {
            // singleFile() collection: adding a new one replaces the old.
            $story->addMedia($attachment)->toMediaCollection('attachment');
        } elseif ($removeAttachment) {
            $story->clearMediaCollection('attachment');
        }

        SeoMeta::syncFor($story, $data);

        $this->forget();

        return $story;
    }

    /**
     * Delete a story.
     */
    public function delete(Story $story): void
    {
        $story->delete();

        $this->forget();
    }

    /**
     * Restore a soft-deleted story.
     */
    public function restore(Story $story): Story
    {
        $story->restore();

        $this->forget();

        return $story;
    }

    /**
     * Permanently delete a soft-deleted story.
     */
    public function forceDelete(Story $story): void
    {
        $story->forceDelete();

        $this->forget();
    }
}
