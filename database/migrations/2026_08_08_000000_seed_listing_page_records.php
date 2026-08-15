<?php

use App\CMS\Services\PageService;
use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Gallery/Projects/Stories didn't have a Page record behind them (News
     * already did - see the down() note), since their /gallery, /projects,
     * /stories routes are handled by dedicated controllers, not PageController.
     * Creating one for each now lets those controllers look up a banner
     * image/eyebrow/SEO override for their listing page, the same way any
     * other Page already can - existing sites keep their current shared
     * banner until an admin sets one, since these start with no override.
     */
    public function up(): void
    {
        $created = false;

        foreach ([
            'gallery' => 'Gallery',
            'projects' => 'Projects',
            'stories' => 'Success Stories',
        ] as $slug => $title) {
            $page = Page::firstOrCreate(
                ['slug' => $slug],
                ['title' => $title, 'status' => 'published', 'template' => 'default']
            );

            $created = $created || $page->wasRecentlyCreated;
        }

        // Pages are cached forever (see PageService::allCached()) - on any
        // environment where that cache is already warm, inserting these
        // rows directly like this would otherwise silently not take effect
        // until something else happened to bust it.
        if ($created) {
            app(PageService::class)->forget();
        }
    }

    public function down(): void
    {
        Page::whereIn('slug', ['gallery', 'projects', 'stories'])->delete();
    }
};
