<?php

namespace App\Http\Controllers;

use App\CMS\Services\ProjectService;
use App\Models\ProjectCategory;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(private readonly ProjectService $projects)
    {
    }

    /**
     * Display every published project, optionally filtered by category.
     */
    public function index(): View
    {
        $projects = $this->projects->all();
        $categories = ProjectCategory::orderBy('name')->get(['name', 'slug']);

        if ($category = request('category')) {
            $projects = array_values(array_filter(
                $projects,
                fn (array $project) => ($project['category']['slug'] ?? null) === $category
            ));
        }

        return view('frontend.projects.index', compact('projects', 'categories'));
    }

    /**
     * Display a single project.
     */
    public function show(string $slug): View
    {
        $project = $this->projects->find($slug);

        abort_unless($project, 404);

        return view('frontend.projects.show', compact('project'));
    }
}
