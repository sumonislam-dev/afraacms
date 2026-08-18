<?php

namespace App\CMS\Services;

use App\Models\NewsCategory;

class NewsCategoryService
{
    public function __construct(private readonly NewsService $news) {}

    /**
     * Create a new category.
     */
    public function create(array $data): NewsCategory
    {
        $category = NewsCategory::create($data);

        $this->news->forget();

        return $category;
    }

    /**
     * Update an existing category.
     *
     * Renaming/re-slugging a category changes what's embedded in the
     * cached public news list, so that cache must be invalidated too.
     */
    public function update(NewsCategory $category, array $data): NewsCategory
    {
        $category->update($data);

        $this->news->forget();

        return $category;
    }

    /**
     * Delete a category (posts in it fall back to uncategorized via
     * the nullOnDelete foreign key).
     */
    public function delete(NewsCategory $category): void
    {
        $category->delete();

        $this->news->forget();
    }
}
