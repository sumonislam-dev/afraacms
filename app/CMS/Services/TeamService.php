<?php

namespace App\CMS\Services;

use App\CMS\Services\Concerns\CachesForFrontend;
use App\Models\TeamMember;

class TeamService
{
    use CachesForFrontend;

    protected function cacheKey(): string
    {
        return 'team.active';
    }

    /**
     * Get every active team member, from cache where possible.
     *
     * Returns plain arrays, not TeamMember models: the cache store's
     * serializable_classes hardening (see config/cache.php) strips objects
     * down to __PHP_Incomplete_Class on read, so only arrays/scalars may be
     * cached here (see PageService/ProjectService for the same pattern).
     *
     * @return array<int, array{id: int, name: string, role: ?string, bio: ?string, photo_url: ?string, link: ?string, category_id: ?int, category: ?array}>
     */
    public function all(): array
    {
        return $this->rememberForever(fn () => TeamMember::query()
            ->where('is_active', true)
            ->with('category')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (TeamMember $member) => [
                'id' => $member->id,
                'name' => $member->name,
                'role' => $member->role,
                'bio' => $member->bio,
                'photo_url' => $member->photo_url,
                'link' => $member->link,
                'category_id' => $member->category_id,
                'category' => $member->category ? [
                    'name' => $member->category->name,
                    'slug' => $member->category->slug,
                ] : null,
            ])
            ->all());
    }

    /**
     * Create a new team member.
     */
    public function create(array $data): TeamMember
    {
        $member = TeamMember::create($data);

        $this->forget();

        return $member;
    }

    /**
     * Update an existing team member.
     */
    public function update(TeamMember $member, array $data): TeamMember
    {
        $member->update($data);

        $this->forget();

        return $member;
    }

    /**
     * Delete a team member.
     */
    public function delete(TeamMember $member): void
    {
        $member->delete();

        $this->forget();
    }

    /**
     * Restore a soft-deleted team member.
     */
    public function restore(TeamMember $member): TeamMember
    {
        $member->restore();

        $this->forget();

        return $member;
    }

    /**
     * Permanently delete a soft-deleted team member.
     */
    public function forceDelete(TeamMember $member): void
    {
        $member->forceDelete();

        $this->forget();
    }
}
