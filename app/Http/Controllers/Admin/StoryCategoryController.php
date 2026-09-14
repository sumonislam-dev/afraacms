<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\StoryCategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStoryCategoryRequest;
use App\Http\Requests\Admin\UpdateStoryCategoryRequest;
use App\Models\StoryCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StoryCategoryController extends Controller
{
    public function __construct(private readonly StoryCategoryService $categories)
    {
        $this->authorizeResource(StoryCategory::class, 'category');
    }

    /**
     * Display a listing of the categories.
     */
    public function index(): View
    {
        $categories = StoryCategory::withCount('stories')->orderBy('name')->get();

        return view('admin.story-categories.index', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreStoryCategoryRequest $request): RedirectResponse
    {
        $this->categories->create($request->validated());

        return redirect()->route('admin.story-categories.index')->with('success', __('Category created successfully.'));
    }

    /**
     * Update the given category.
     */
    public function update(UpdateStoryCategoryRequest $request, StoryCategory $category): RedirectResponse
    {
        $this->categories->update($category, $request->validated());

        return redirect()->route('admin.story-categories.index')->with('success', __('Category updated successfully.'));
    }

    /**
     * Delete the given category.
     */
    public function destroy(StoryCategory $category): RedirectResponse
    {
        $this->categories->delete($category);

        return redirect()->route('admin.story-categories.index')->with('success', __('Category deleted successfully.'));
    }
}
