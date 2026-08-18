<?php

namespace App\Http\Controllers;

use App\CMS\Services\PageService;
use App\CMS\Services\VisitorBookService;
use App\Http\Requests\StoreVisitorBookEntryRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VisitorBookController extends Controller
{
    public function __construct(
        private readonly VisitorBookService $visitorBook,
        private readonly PageService $pages,
    ) {}

    /**
     * Display every approved visitor book entry, across all projects.
     */
    public function index(): View
    {
        $entries = $this->visitorBook->allApproved();

        // The "visitor-book" slug's Page record supplies this listing's
        // banner image/eyebrow/SEO override, if an admin has set one - same
        // mechanism as the news/gallery/projects/stories listing pages.
        $cmsPage = $this->pages->findPublished('visitor-book');

        return view('frontend.visitor-book.index', compact('entries', 'cmsPage'));
    }

    /**
     * Store a newly submitted visitor book entry for a project.
     */
    public function store(StoreVisitorBookEntryRequest $request, string $slug): RedirectResponse
    {
        $project = Project::published()->where('slug', $slug)->firstOrFail();

        $this->visitorBook->submit($project, $request->safe()->except('website'), $request->ip());

        return back()->with('success', __('Thank you for sharing your opinion! It will appear once reviewed.'));
    }
}
