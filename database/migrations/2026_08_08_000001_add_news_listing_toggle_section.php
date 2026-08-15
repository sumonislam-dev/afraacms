<?php

use App\CMS\Services\PageService;
use App\Models\Page;
use App\Models\Section;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * The "news" Page's full post archive is now opt-in, gated on it having
     * a "Latest News" (type "news") section attached - so any site that
     * already had this page (and was relying on the archive always showing)
     * gets one added here, preserving current behavior with no visible
     * change. New installs get this from RsufDemoSeeder/an admin adding one.
     */
    public function up(): void
    {
        $page = Page::where('slug', 'news')->first();

        if (! $page) {
            return;
        }

        $hasNewsSection = $page->sections()->where('type', 'news')->exists();

        if (! $hasNewsSection) {
            Section::create([
                'page_id' => $page->id,
                'type' => 'news',
                'is_active' => true,
                'sort_order' => $page->sections()->max('sort_order') + 1,
            ]);

            // Pages are cached forever (see PageService::allCached()) - on
            // any environment where that cache is already warm, inserting
            // the row directly like this would otherwise silently not take
            // effect until something else happened to bust it.
            app(PageService::class)->forget();
        }
    }

    public function down(): void
    {
        // Not reversible: we can't tell an admin-added "news" section apart
        // from one this migration created.
    }
};
