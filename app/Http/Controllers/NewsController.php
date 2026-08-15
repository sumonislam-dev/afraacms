<?php

namespace App\Http\Controllers;

use App\CMS\Services\NewsService;
use App\CMS\Services\PageService;
use App\Models\NewsCategory;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function __construct(
        private readonly NewsService $news,
        private readonly PageService $pages,
    ) {
    }

    /**
     * Display every published post, optionally filtered by category.
     */
    public function index(): View
    {
        $posts = $this->news->all();
        $categories = NewsCategory::orderBy('name')->get(['name', 'slug']);

        if ($category = request('category')) {
            $posts = array_values(array_filter(
                $posts,
                fn (array $post) => ($post['category']['slug'] ?? null) === $category
            ));
        }

        // The "news" slug's Page record supplies this listing's banner
        // image/eyebrow/SEO override, if an admin has set one - the posts
        // themselves are still rendered by NewsService, not Page sections.
        $cmsPage = $this->pages->findPublished('news');

        return view('frontend.news.index', compact('posts', 'categories', 'cmsPage'));
    }

    /**
     * Display a single post.
     */
    public function show(string $slug): View
    {
        $post = $this->news->find($slug);

        abort_unless($post, 404);

        return view('frontend.news.show', compact('post'));
    }
}
