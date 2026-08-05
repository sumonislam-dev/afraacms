<?php

namespace App\Http\Controllers;

use App\CMS\Services\ProjectService;
use App\CMS\Services\StoryService;
use Illuminate\View\View;

class StoryController extends Controller
{
    public function __construct(
        private readonly StoryService $stories,
        private readonly ProjectService $projects,
    ) {
    }

    /**
     * Display every published story, optionally filtered by project.
     */
    public function index(): View
    {
        $stories = $this->stories->all();
        $projects = collect($this->projects->all())->filter(
            fn (array $project) => collect($stories)->contains(fn (array $story) => ($story['project']['slug'] ?? null) === $project['slug'])
        )->values();

        if ($project = request('project')) {
            $stories = array_values(array_filter(
                $stories,
                fn (array $story) => ($story['project']['slug'] ?? null) === $project
            ));
        }

        return view('frontend.stories.index', compact('stories', 'projects'));
    }

    /**
     * Display a single story.
     */
    public function show(string $slug): View
    {
        $story = $this->stories->find($slug);

        abort_unless($story, 404);

        return view('frontend.stories.show', compact('story'));
    }
}
