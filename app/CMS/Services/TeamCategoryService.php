<?php

namespace App\CMS\Services;

use App\Models\TeamCategory;

class TeamCategoryService
{
    public function __construct(private readonly TeamService $team)
    {
    }

    /**
     * Create a new category.
     */
    public function create(array $data): TeamCategory
    {
        $category = TeamCategory::create($data);

        $this->team->forget();

        return $category;
    }

    /**
     * Update an existing category.
     *
     * Renaming/re-slugging a category changes what's embedded in the
     * cached team member list, so that cache must be invalidated too.
     */
    public function update(TeamCategory $category, array $data): TeamCategory
    {
        $category->update($data);

        $this->team->forget();

        return $category;
    }

    /**
     * Delete a category (members in it fall back to uncategorized via
     * the nullOnDelete foreign key).
     */
    public function delete(TeamCategory $category): void
    {
        $category->delete();

        $this->team->forget();
    }
}
