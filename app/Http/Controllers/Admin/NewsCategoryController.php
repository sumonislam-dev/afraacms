<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\NewsCategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNewsCategoryRequest;
use App\Http\Requests\Admin\UpdateNewsCategoryRequest;
use App\Models\NewsCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NewsCategoryController extends Controller
{
    public function __construct(private readonly NewsCategoryService $categories)
    {
        $this->authorizeResource(NewsCategory::class, 'category');
    }

    /**
     * Display a listing of the categories.
     */
    public function index(): View
    {
        $categories = NewsCategory::withCount('posts')->orderBy('name')->get();

        return view('admin.news-categories.index', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreNewsCategoryRequest $request): RedirectResponse
    {
        $this->categories->create($request->validated());

        return redirect()->route('admin.news-categories.index')->with('success', __('Category created successfully.'));
    }

    /**
     * Update the given category.
     */
    public function update(UpdateNewsCategoryRequest $request, NewsCategory $category): RedirectResponse
    {
        $this->categories->update($category, $request->validated());

        return redirect()->route('admin.news-categories.index')->with('success', __('Category updated successfully.'));
    }

    /**
     * Delete the given category.
     */
    public function destroy(NewsCategory $category): RedirectResponse
    {
        $this->categories->delete($category);

        return redirect()->route('admin.news-categories.index')->with('success', __('Category deleted successfully.'));
    }
}
