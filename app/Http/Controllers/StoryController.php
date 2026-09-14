<?php

namespace App\Http\Controllers;

use App\CMS\Services\PageService;
use App\CMS\Services\ProjectService;
use App\CMS\Services\StoryService;
use App\Http\Controllers\Concerns\PaginatesArrays;
use App\Models\StoryCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StoryController extends Controller
{
    use PaginatesArrays;

    public function __construct(
        private readonly StoryService $stories,
        private readonly ProjectService $projects,
        private readonly PageService $pages,
    ) {}

    /**
     * Display every published story, optionally filtered by project and a
     * search query, one page at a time.
     */
    public function index(): View
    {
        $stories = $this->stories->all();
        $projects = collect($this->projects->all())->filter(
            fn (array $project) => collect($stories)->contains(fn (array $story) => ($story['project']['slug'] ?? null) === $project['slug'])
        )->values();
        $categories = StoryCategory::orderBy('name')->get(['name', 'slug']);

        if ($project = request('project')) {
            $stories = array_values(array_filter(
                $stories,
                fn (array $story) => ($story['project']['slug'] ?? null) === $project
            ));
        }

        if ($category = request('category')) {
            $stories = array_values(array_filter(
                $stories,
                fn (array $story) => ($story['category']['slug'] ?? null) === $category
            ));
        }

        $stories = $this->searchArray($stories, request('q'));

        $paginator = $this->paginateArray($stories, 'stories_items_per_page', 9);
        $stories = $paginator->items();

        // The "stories" slug's Page record supplies this listing's banner
        // image/eyebrow/SEO override, if an admin has set one.
        $cmsPage = $this->pages->findPublished('stories');

        return view('frontend.stories.index', compact('stories', 'projects', 'categories', 'cmsPage', 'paginator'));
    }

    /**
     * Display a single story.
     */
    public function show(string $slug): View|RedirectResponse
    {
        $story = $this->stories->find($slug);

        abort_unless($story, 404);

        // A story with an attachment links straight to it from its card
        // (see story-card), so a direct/indexed visit to this URL should
        // still land somewhere useful rather than a near-empty page.
        if ($story['attachment_url']) {
            return redirect($story['attachment_url']);
        }

        return view('frontend.stories.show', compact('story'));
    }
}
