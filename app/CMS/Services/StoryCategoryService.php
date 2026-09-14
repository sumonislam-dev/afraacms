<?php

namespace App\CMS\Services;

use App\Models\StoryCategory;

class StoryCategoryService
{
    public function __construct(private readonly StoryService $stories) {}

    /**
     * Create a new category.
     */
    public function create(array $data): StoryCategory
    {
        $category = StoryCategory::create($data);

        $this->stories->forget();

        return $category;
    }

    /**
     * Update an existing category.
     *
     * Renaming/re-slugging a category changes what's embedded in the
     * cached public story list, so that cache must be invalidated too.
     */
    public function update(StoryCategory $category, array $data): StoryCategory
    {
        $category->update($data);

        $this->stories->forget();

        return $category;
    }

    /**
     * Delete a category (stories in it fall back to uncategorized via
     * the nullOnDelete foreign key).
     */
    public function delete(StoryCategory $category): void
    {
        $category->delete();

        $this->stories->forget();
    }
}
