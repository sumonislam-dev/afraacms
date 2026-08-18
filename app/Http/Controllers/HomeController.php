<?php

namespace App\Http\Controllers;

use App\CMS\Services\PageService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly PageService $pages) {}

    /**
     * Display the site homepage: the admin-selected Page (settings.homepage_page_id)
     * if one is set and still published, otherwise a placeholder prompting setup.
     */
    public function index(): View
    {
        $homepage = $this->pages->homepage();

        if (! $homepage) {
            return view('frontend.home');
        }

        return view("frontend.templates.{$this->pages->templateFor($homepage)}", ['page' => $homepage, 'isHome' => true]);
    }
}
