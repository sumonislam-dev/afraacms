<?php

namespace App\Http\Controllers;

use App\CMS\Services\PageService;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(private readonly PageService $pages)
    {
    }

    /**
     * Display a published page by its slug.
     */
    public function show(string $slug): View
    {
        $page = $this->pages->findPublished($slug);

        abort_unless($page, 404);

        return view("frontend.templates.{$this->pages->templateFor($page)}", ['page' => $page]);
    }
}
