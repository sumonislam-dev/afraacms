<?php

namespace App\Http\Controllers;

use App\CMS\Services\NewsService;
use App\Models\NewsCategory;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function __construct(private readonly NewsService $news)
    {
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

        return view('frontend.news.index', compact('posts', 'categories'));
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
