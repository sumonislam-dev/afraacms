<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\TeamCategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeamCategoryRequest;
use App\Http\Requests\Admin\UpdateTeamCategoryRequest;
use App\Models\TeamCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeamCategoryController extends Controller
{
    public function __construct(private readonly TeamCategoryService $categories)
    {
        $this->authorizeResource(TeamCategory::class, 'category');
    }

    /**
     * Display a listing of the categories.
     */
    public function index(): View
    {
        $categories = TeamCategory::withCount('members')->orderBy('name')->get();

        return view('admin.team-categories.index', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreTeamCategoryRequest $request): RedirectResponse
    {
        $this->categories->create($request->validated());

        return redirect()->route('admin.team-categories.index')->with('success', __('Category created successfully.'));
    }

    /**
     * Update the given category.
     */
    public function update(UpdateTeamCategoryRequest $request, TeamCategory $category): RedirectResponse
    {
        $this->categories->update($category, $request->validated());

        return redirect()->route('admin.team-categories.index')->with('success', __('Category updated successfully.'));
    }

    /**
     * Delete the given category.
     */
    public function destroy(TeamCategory $category): RedirectResponse
    {
        $this->categories->delete($category);

        return redirect()->route('admin.team-categories.index')->with('success', __('Category deleted successfully.'));
    }
}
