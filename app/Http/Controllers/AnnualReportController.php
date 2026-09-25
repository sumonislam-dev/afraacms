<?php

namespace App\Http\Controllers;

use App\CMS\Services\AnnualReportService;
use App\CMS\Services\PageService;
use Illuminate\View\View;

class AnnualReportController extends Controller
{
    public function __construct(
        private readonly AnnualReportService $reports,
        private readonly PageService $pages,
    ) {}

    /**
     * Display every active annual report, newest year first. Each one links
     * straight to its PDF - same "click to download" pattern as a Notice.
     */
    public function index(): View
    {
        // The "annual-reports" slug's Page record supplies this listing's
        // banner image/eyebrow/SEO override, if an admin has set one - same
        // pattern as GalleryController's public index.
        $cmsPage = $this->pages->findPublished('annual-reports');

        return view('frontend.annual-reports.index', [
            'reports' => $this->reports->all(),
            'cmsPage' => $cmsPage,
        ]);
    }
}
