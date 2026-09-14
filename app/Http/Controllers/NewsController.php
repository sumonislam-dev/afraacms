<?php

namespace App\Http\Controllers;

use App\CMS\Services\NewsService;
use App\CMS\Services\PageService;
use App\Http\Controllers\Concerns\PaginatesArrays;
use App\Models\NewsCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NewsController extends Controller
{
    use PaginatesArrays;

    public function __construct(
        private readonly NewsService $news,
        private readonly PageService $pages,
    ) {}

    /**
     * Display every published post, optionally filtered by category/format
     * and a search query, one page at a time.
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

        if (request('format') === 'notice') {
            $posts = array_values(array_filter(
                $posts,
                fn (array $post) => filled($post['attachment_url'] ?? null)
            ));
        }

        $posts = $this->searchArray($posts, request('q'));

        $paginator = $this->paginateArray($posts, 'news_items_per_page', 9);
        $posts = $paginator->items();

        // The "news" slug's Page record supplies this listing's banner
        // image/eyebrow/SEO override, if an admin has set one - the posts
        // themselves are still rendered by NewsService, not Page sections.
        $cmsPage = $this->pages->findPublished('news');

        return view('frontend.news.index', compact('posts', 'categories', 'cmsPage', 'paginator'));
    }

    /**
     * Display a single post.
     */
    public function show(string $slug): View|RedirectResponse
    {
        $post = $this->news->find($slug);

        abort_unless($post, 404);

        // A post with an attachment is treated as a Notice: nothing in the
        // app links here for one (news-card links straight to the
        // attachment), but a direct/indexed visit to this URL should still
        // land somewhere useful rather than a near-empty page.
        if ($post['attachment_url']) {
            return redirect($post['attachment_url']);
        }

        return view('frontend.news.show', compact('post'));
    }
}
