<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\NewsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNewsPostRequest;
use App\Http\Requests\Admin\UpdateNewsPostRequest;
use App\Models\NewsPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NewsPostController extends Controller
{
    public function __construct(private readonly NewsService $news)
    {
        $this->authorizeResource(NewsPost::class, 'post');
    }

    /**
     * Display a listing of the posts.
     */
    public function index(): View
    {
        $posts = NewsPost::with('category')
            ->when(request('search'), fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->latest('published_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.news.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create(): View
    {
        return view('admin.news.create');
    }

    /**
     * Store a newly created post.
     */
    public function store(StoreNewsPostRequest $request): RedirectResponse
    {
        $this->news->create($request->validated());

        return redirect()->route('admin.news.index')->with('success', __('Post created successfully.'));
    }

    /**
     * Show the form for editing the given post.
     */
    public function edit(NewsPost $post): View
    {
        return view('admin.news.edit', compact('post'));
    }

    /**
     * Update the given post.
     */
    public function update(UpdateNewsPostRequest $request, NewsPost $post): RedirectResponse
    {
        $this->news->update($post, $request->validated());

        return redirect()->route('admin.news.index')->with('success', __('Post updated successfully.'));
    }

    /**
     * Delete the given post.
     */
    public function destroy(NewsPost $post): RedirectResponse
    {
        $this->news->delete($post);

        return redirect()->route('admin.news.index')->with('success', __('Post deleted successfully.'));
    }

    /**
     * Display the trashed (soft-deleted) posts.
     */
    public function trash(): View
    {
        $this->authorize('viewAny', NewsPost::class);

        $posts = NewsPost::onlyTrashed()
            ->with('category')
            ->when(request('search'), fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.news.trash', compact('posts'));
    }

    /**
     * Restore a trashed post.
     */
    public function restore(NewsPost $post): RedirectResponse
    {
        $this->authorize('restore', $post);

        $this->news->restore($post);

        return redirect()->route('admin.news.trash')->with('success', __('Post restored successfully.'));
    }

    /**
     * Permanently delete a trashed post.
     */
    public function forceDelete(NewsPost $post): RedirectResponse
    {
        $this->authorize('forceDelete', $post);

        $this->news->forceDelete($post);

        return redirect()->route('admin.news.trash')->with('success', __('Post permanently deleted.'));
    }
}
