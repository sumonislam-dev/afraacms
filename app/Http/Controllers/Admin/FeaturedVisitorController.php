<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\FeaturedVisitorService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFeaturedVisitorRequest;
use App\Http\Requests\Admin\UpdateFeaturedVisitorRequest;
use App\Models\FeaturedVisitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeaturedVisitorController extends Controller
{
    public function __construct(private readonly FeaturedVisitorService $visitors)
    {
        $this->authorizeResource(FeaturedVisitor::class, 'featuredVisitor');
    }

    /**
     * Display a listing of the featured visitors.
     */
    public function index(): View
    {
        $visitors = FeaturedVisitor::query()
            ->when(request('search'), fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->orderByDesc('visited_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.featured-visitors.index', compact('visitors'));
    }

    /**
     * Show the form for creating a new featured visitor.
     */
    public function create(): View
    {
        return view('admin.featured-visitors.create');
    }

    /**
     * Store a newly created featured visitor.
     */
    public function store(StoreFeaturedVisitorRequest $request): RedirectResponse
    {
        $this->visitors->create($request->validated());

        return redirect()->route('admin.featured-visitors.index')->with('success', __('Featured visitor added successfully.'));
    }

    /**
     * Show the form for editing the given featured visitor.
     */
    public function edit(FeaturedVisitor $featuredVisitor): View
    {
        return view('admin.featured-visitors.edit', ['visitor' => $featuredVisitor]);
    }

    /**
     * Update the given featured visitor.
     */
    public function update(UpdateFeaturedVisitorRequest $request, FeaturedVisitor $featuredVisitor): RedirectResponse
    {
        $this->visitors->update($featuredVisitor, $request->validated());

        return redirect()->route('admin.featured-visitors.index')->with('success', __('Featured visitor updated successfully.'));
    }

    /**
     * Delete the given featured visitor.
     */
    public function destroy(FeaturedVisitor $featuredVisitor): RedirectResponse
    {
        $this->visitors->delete($featuredVisitor);

        return redirect()->route('admin.featured-visitors.index')->with('success', __('Featured visitor deleted successfully.'));
    }

    /**
     * Display the trashed (soft-deleted) featured visitors.
     */
    public function trash(): View
    {
        $this->authorize('viewAny', FeaturedVisitor::class);

        $visitors = FeaturedVisitor::onlyTrashed()
            ->when(request('search'), fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.featured-visitors.trash', compact('visitors'));
    }

    /**
     * Restore a trashed featured visitor.
     */
    public function restore(FeaturedVisitor $featuredVisitor): RedirectResponse
    {
        $this->authorize('restore', $featuredVisitor);

        $this->visitors->restore($featuredVisitor);

        return redirect()->route('admin.featured-visitors.trash')->with('success', __('Featured visitor restored successfully.'));
    }

    /**
     * Permanently delete a trashed featured visitor.
     */
    public function forceDelete(FeaturedVisitor $featuredVisitor): RedirectResponse
    {
        $this->authorize('forceDelete', $featuredVisitor);

        $this->visitors->forceDelete($featuredVisitor);

        return redirect()->route('admin.featured-visitors.trash')->with('success', __('Featured visitor permanently deleted.'));
    }
}
