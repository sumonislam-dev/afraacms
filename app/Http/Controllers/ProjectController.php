<?php

namespace App\Http\Controllers;

use App\CMS\Services\PageService;
use App\CMS\Services\ProjectService;
use App\CMS\Services\StoryService;
use App\CMS\Services\VisitorBookService;
use App\Models\ProjectCategory;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projects,
        private readonly StoryService $stories,
        private readonly PageService $pages,
        private readonly VisitorBookService $visitorBook,
    ) {
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

        // The "projects" slug's Page record supplies this listing's banner
        // image/eyebrow/SEO override, if an admin has set one.
        $cmsPage = $this->pages->findPublished('projects');

        return view('frontend.projects.index', compact('projects', 'categories', 'cmsPage'));
    }

    /**
     * Display a single project.
     */
    public function show(string $slug): View
    {
        $project = $this->projects->find($slug);

        abort_unless($project, 404);

        $stories = collect($this->stories->all())
            ->filter(fn (array $story) => ($story['project']['slug'] ?? null) === $slug)
            ->values()
            ->all();

        $visitorBookEntries = $this->visitorBook->approvedForProjectSlug($slug);

        return view('frontend.projects.show', compact('project', 'stories', 'visitorBookEntries'));
    }
}
