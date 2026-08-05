<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\StoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStoryRequest;
use App\Http\Requests\Admin\UpdateStoryRequest;
use App\Models\Story;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StoryController extends Controller
{
    public function __construct(private readonly StoryService $stories)
    {
        $this->authorizeResource(Story::class, 'story');
    }

    /**
     * Display a listing of the stories.
     */
    public function index(): View
    {
        $stories = Story::with('project')
            ->when(request('search'), fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->latest('published_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.stories.index', compact('stories'));
    }

    /**
     * Show the form for creating a new story.
     */
    public function create(): View
    {
        return view('admin.stories.create');
    }

    /**
     * Store a newly created story.
     */
    public function store(StoreStoryRequest $request): RedirectResponse
    {
        $this->stories->create($request->validated());

        return redirect()->route('admin.stories.index')->with('success', __('Story created successfully.'));
    }

    /**
     * Show the form for editing the given story.
     */
    public function edit(Story $story): View
    {
        return view('admin.stories.edit', compact('story'));
    }

    /**
     * Update the given story.
     */
    public function update(UpdateStoryRequest $request, Story $story): RedirectResponse
    {
        $this->stories->update($story, $request->validated());

        return redirect()->route('admin.stories.index')->with('success', __('Story updated successfully.'));
    }

    /**
     * Delete the given story.
     */
    public function destroy(Story $story): RedirectResponse
    {
        $this->stories->delete($story);

        return redirect()->route('admin.stories.index')->with('success', __('Story deleted successfully.'));
    }

    /**
     * Display the trashed (soft-deleted) stories.
     */
    public function trash(): View
    {
        $this->authorize('viewAny', Story::class);

        $stories = Story::onlyTrashed()
            ->with('project')
            ->when(request('search'), fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.stories.trash', compact('stories'));
    }

    /**
     * Restore a trashed story.
     */
    public function restore(Story $story): RedirectResponse
    {
        $this->authorize('restore', $story);

        $this->stories->restore($story);

        return redirect()->route('admin.stories.trash')->with('success', __('Story restored successfully.'));
    }

    /**
     * Permanently delete a trashed story.
     */
    public function forceDelete(Story $story): RedirectResponse
    {
        $this->authorize('forceDelete', $story);

        $this->stories->forceDelete($story);

        return redirect()->route('admin.stories.trash')->with('success', __('Story permanently deleted.'));
    }
}
