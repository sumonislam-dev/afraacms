<?php

namespace App\CMS\Services;

use App\Models\ProjectCategory;

class ProjectCategoryService
{
    public function __construct(private readonly ProjectService $projects) {}

    /**
     * Create a new category.
     */
    public function create(array $data): ProjectCategory
    {
        $category = ProjectCategory::create($data);

        $this->projects->forget();

        return $category;
    }

    /**
     * Update an existing category.
     *
     * Renaming/re-slugging a category changes what's embedded in the
     * cached public project list, so that cache must be invalidated too.
     */
    public function update(ProjectCategory $category, array $data): ProjectCategory
    {
        $category->update($data);

        $this->projects->forget();

        return $category;
    }

    /**
     * Delete a category (projects in it fall back to uncategorized via
     * the nullOnDelete foreign key).
     */
    public function delete(ProjectCategory $category): void
    {
        $category->delete();

        $this->projects->forget();
    }
}
