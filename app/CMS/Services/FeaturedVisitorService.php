<?php

namespace App\CMS\Services;

use App\CMS\Services\Concerns\CachesForFrontend;
use App\Models\FeaturedVisitor;

class FeaturedVisitorService
{
    use CachesForFrontend;

    protected function cacheKey(): string
    {
        return 'featured_visitors.active';
    }

    /**
     * Get every active featured visitor, from cache where possible.
     *
     * Returns plain arrays, not FeaturedVisitor models: the cache store's
     * serializable_classes hardening (see config/cache.php) strips objects
     * down to __PHP_Incomplete_Class on read, so only arrays/scalars may be
     * cached here (see TeamService for the same pattern).
     *
     * @return array<int, array{id: int, name: string, organization: ?string, country: string, visited_at: string, photo_url: ?string}>
     */
    public function all(): array
    {
        return $this->rememberForever(fn () => FeaturedVisitor::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('visited_at')
            ->get()
            ->map(fn (FeaturedVisitor $visitor) => [
                'id' => $visitor->id,
                'name' => $visitor->name,
                'organization' => $visitor->organization,
                'country' => $visitor->country,
                'visited_at' => $visitor->visited_at?->toDateString(),
                'photo_url' => $visitor->photo_url,
            ])
            ->all());
    }

    /**
     * Create a new featured visitor.
     */
    public function create(array $data): FeaturedVisitor
    {
        $visitor = FeaturedVisitor::create($data);

        $this->forget();

        return $visitor;
    }

    /**
     * Update an existing featured visitor.
     */
    public function update(FeaturedVisitor $visitor, array $data): FeaturedVisitor
    {
        $visitor->update($data);

        $this->forget();

        return $visitor;
    }

    /**
     * Delete a featured visitor.
     */
    public function delete(FeaturedVisitor $visitor): void
    {
        $visitor->delete();

        $this->forget();
    }

    /**
     * Restore a soft-deleted featured visitor.
     */
    public function restore(FeaturedVisitor $visitor): FeaturedVisitor
    {
        $visitor->restore();

        $this->forget();

        return $visitor;
    }

    /**
     * Permanently delete a soft-deleted featured visitor.
     */
    public function forceDelete(FeaturedVisitor $visitor): void
    {
        $visitor->forceDelete();

        $this->forget();
    }
}
