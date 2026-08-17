<?php

namespace Database\Seeders;

use App\CMS\Services\PageService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder
{
    /**
     * Seed the standard inner pages as empty, published, admin-editable
     * scaffolds: a Hero section using the page title, ready to be filled
     * in through the Section Engine. No fabricated body content.
     */
    public function run(): void
    {
        $pages = [
            'About' => 'about',
            'History' => 'history',
            'Registration' => 'registration',
            'Contact' => 'contact',
        ];

        foreach ($pages as $title => $slug) {
            $page = Page::firstOrCreate(
                ['slug' => $slug],
                ['title' => $title, 'status' => 'published', 'template' => 'default']
            );

            if ($page->sections()->doesntExist()) {
                $page->sections()->create([
                    'type' => 'hero',
                    'heading' => $title,
                    'sort_order' => 0,
                ]);
            }
        }

        $this->seedListingPageRecords();
    }

    /**
     * Gallery/Projects/Stories don't own a URL through PageController - their
     * /gallery, /projects, /stories routes are handled by dedicated
     * controllers - so they get no Hero section, unlike the pages above.
     * They still need a bare Page row so those controllers can look up a
     * banner image/eyebrow/SEO override for their listing page, the same
     * way any other Page already can. Existing sites keep their current
     * shared banner until an admin sets one, since these start with no
     * override.
     */
    private function seedListingPageRecords(): void
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
        // environment where that cache is already warm (e.g. re-running this
        // seeder to backfill on an existing install), inserting rows
        // directly like this would otherwise silently not take effect until
        // something else happened to bust it.
        if ($created) {
            app(PageService::class)->forget();
        }
    }
}
