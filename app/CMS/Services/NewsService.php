<?php

namespace App\CMS\Services;

use App\CMS\Services\Concerns\CachesForFrontend;
use App\Models\NewsPost;
use App\Models\SeoMeta;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class NewsService
{
    use CachesForFrontend;

    protected function cacheKey(): string
    {
        return 'news.published';
    }

    /**
     * Find a published post by slug, from cache where possible.
     *
     * Returns a plain array, not a NewsPost model: the cache store's
     * serializable_classes hardening (see config/cache.php) strips objects
     * down to __PHP_Incomplete_Class on read, so only arrays/scalars may be
     * cached here (see PageService/ProjectService for the same pattern).
     *
     * @return array{id: int, title: string, slug: string, excerpt: ?string, content: ?string, cover_image_url: ?string, attachment_url: ?string, published_at: ?string, is_featured: bool, updated_at: ?string, seo: array, category_id: ?int, category: ?array}|null
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
        return $this->rememberForever(fn () => NewsPost::published()
            ->with(['category', 'seo'])
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->get()
            ->mapWithKeys(fn (NewsPost $post) => [
                $post->slug => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => $post->excerpt,
                    'content' => $post->content,
                    'cover_image_url' => $post->cover_image_url,
                    'attachment_url' => $post->attachment_url,
                    'published_at' => $post->published_at?->toDateString(),
                    'is_featured' => $post->is_featured,
                    'updated_at' => $post->updated_at?->toIso8601String(),
                    'seo' => SeoMeta::toCacheArray($post->seo),
                    'category_id' => $post->category_id,
                    'category' => $post->category ? [
                        'name' => $post->category->name,
                        'slug' => $post->category->slug,
                    ] : null,
                ],
            ])
            ->all());
    }

    /**
     * Create a new post.
     */
    public function create(array $data): NewsPost
    {
        $attachment = Arr::pull($data, 'attachment');

        $post = NewsPost::create($data);

        if ($attachment instanceof UploadedFile) {
            $post->addMedia($attachment)->toMediaCollection('attachment');
        }

        SeoMeta::syncFor($post, $data);

        $this->forget();

        return $post;
    }

    /**
     * Update an existing post.
     */
    public function update(NewsPost $post, array $data): NewsPost
    {
        $attachment = Arr::pull($data, 'attachment');
        $removeAttachment = Arr::pull($data, 'remove_attachment', false);

        $post->update($data);

        if ($attachment instanceof UploadedFile) {
            // singleFile() collection: adding a new one replaces the old.
            $post->addMedia($attachment)->toMediaCollection('attachment');
        } elseif ($removeAttachment) {
            $post->clearMediaCollection('attachment');
        }

        SeoMeta::syncFor($post, $data);

        $this->forget();

        return $post;
    }

    /**
     * Delete a post.
     */
    public function delete(NewsPost $post): void
    {
        $post->delete();

        $this->forget();
    }

    /**
     * Restore a soft-deleted post.
     */
    public function restore(NewsPost $post): NewsPost
    {
        $post->restore();

        $this->forget();

        return $post;
    }

    /**
     * Permanently delete a soft-deleted post.
     */
    public function forceDelete(NewsPost $post): void
    {
        $post->forceDelete();

        $this->forget();
    }
}
