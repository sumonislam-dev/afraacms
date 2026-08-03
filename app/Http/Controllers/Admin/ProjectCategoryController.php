<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\ProjectCategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectCategoryRequest;
use App\Http\Requests\Admin\UpdateProjectCategoryRequest;
use App\Models\ProjectCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectCategoryController extends Controller
{
    public function __construct(private readonly ProjectCategoryService $categories)
    {
        $this->authorizeResource(ProjectCategory::class, 'category');
    }

    /**
     * Display a listing of the categories.
     */
    public function index(): View
    {
        $categories = ProjectCategory::withCount('projects')->orderBy('name')->get();

        return view('admin.project-categories.index', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreProjectCategoryRequest $request): RedirectResponse
    {
        $this->categories->create($request->validated());

        return redirect()->route('admin.project-categories.index')->with('success', __('Category created successfully.'));
    }

    /**
     * Update the given category.
     */
    public function update(UpdateProjectCategoryRequest $request, ProjectCategory $category): RedirectResponse
    {
        $this->categories->update($category, $request->validated());

        return redirect()->route('admin.project-categories.index')->with('success', __('Category updated successfully.'));
    }

    /**
     * Delete the given category.
     */
    public function destroy(ProjectCategory $category): RedirectResponse
    {
        $this->categories->delete($category);

        return redirect()->route('admin.project-categories.index')->with('success', __('Category deleted successfully.'));
    }
}
