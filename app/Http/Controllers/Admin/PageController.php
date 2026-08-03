<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\PageService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePageRequest;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(private readonly PageService $pages)
    {
        $this->authorizeResource(Page::class, 'page');
    }

    /**
     * Display a listing of the pages.
     */
    public function index(): View
    {
        $pages = Page::query()
            ->when(request('search'), fn ($query, $search) => $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create(): View
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created page.
     */
    public function store(StorePageRequest $request): RedirectResponse
    {
        $this->pages->create($request->validated());

        return redirect()->route('admin.pages.index')->with('success', __('Page created successfully.'));
    }

    /**
     * Show the form for editing the given page.
     */
    public function edit(Page $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the given page.
     */
    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $this->pages->update($page, $request->validated());

        return redirect()->route('admin.pages.index')->with('success', __('Page updated successfully.'));
    }

    /**
     * Delete the given page.
     */
    public function destroy(Page $page): RedirectResponse
    {
        $this->pages->delete($page);

        return redirect()->route('admin.pages.index')->with('success', __('Page deleted successfully.'));
    }

    /**
     * Display the trashed (soft-deleted) pages.
     */
    public function trash(): View
    {
        $this->authorize('viewAny', Page::class);

        $pages = Page::onlyTrashed()
            ->when(request('search'), fn ($query, $search) => $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%");
            }))
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.trash', compact('pages'));
    }

    /**
     * Restore a trashed page.
     */
    public function restore(Page $page): RedirectResponse
    {
        $this->authorize('restore', $page);

        $this->pages->restore($page);

        return redirect()->route('admin.pages.trash')->with('success', __('Page restored successfully.'));
    }

    /**
     * Permanently delete a trashed page.
     */
    public function forceDelete(Page $page): RedirectResponse
    {
        $this->authorize('forceDelete', $page);

        $this->pages->forceDelete($page);

        return redirect()->route('admin.pages.trash')->with('success', __('Page permanently deleted.'));
    }
}
