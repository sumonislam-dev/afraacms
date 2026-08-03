<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\ProjectService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectRequest;
use App\Http\Requests\Admin\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(private readonly ProjectService $projects)
    {
        $this->authorizeResource(Project::class, 'project');
    }

    /**
     * Display a listing of the projects.
     */
    public function index(): View
    {
        $projects = Project::with('category')
            ->when(request('search'), fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(): View
    {
        return view('admin.projects.create');
    }

    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $this->projects->create($request->validated());

        return redirect()->route('admin.projects.index')->with('success', __('Project created successfully.'));
    }

    /**
     * Show the form for editing the given project.
     */
    public function edit(Project $project): View
    {
        return view('admin.projects.edit', compact('project'));
    }

    /**
     * Update the given project.
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->projects->update($project, $request->validated());

        return redirect()->route('admin.projects.index')->with('success', __('Project updated successfully.'));
    }

    /**
     * Delete the given project.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $this->projects->delete($project);

        return redirect()->route('admin.projects.index')->with('success', __('Project deleted successfully.'));
    }

    /**
     * Display the trashed (soft-deleted) projects.
     */
    public function trash(): View
    {
        $this->authorize('viewAny', Project::class);

        $projects = Project::onlyTrashed()
            ->with('category')
            ->when(request('search'), fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.projects.trash', compact('projects'));
    }

    /**
     * Restore a trashed project.
     */
    public function restore(Project $project): RedirectResponse
    {
        $this->authorize('restore', $project);

        $this->projects->restore($project);

        return redirect()->route('admin.projects.trash')->with('success', __('Project restored successfully.'));
    }

    /**
     * Permanently delete a trashed project.
     */
    public function forceDelete(Project $project): RedirectResponse
    {
        $this->authorize('forceDelete', $project);

        $this->projects->forceDelete($project);

        return redirect()->route('admin.projects.trash')->with('success', __('Project permanently deleted.'));
    }
}
