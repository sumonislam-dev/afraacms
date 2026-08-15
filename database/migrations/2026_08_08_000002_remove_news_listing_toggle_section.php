<?php

use App\CMS\Services\PageService;
use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Undoes 2026_08_08_000001: the "news" Page's full post archive is no
     * longer gated on having a "Latest News" section attached (it always
     * shows now, like Gallery/Projects/Stories), so the placeholder section
     * that migration added purely as an on/off switch would now just render
     * as a redundant preview at the top of the very page it's on - remove it.
     */
    public function up(): void
    {
        $page = Page::where('slug', 'news')->first();

        if (! $page) {
            return;
        }

        $removed = $page->sections()->where('type', 'news')->delete();

        if ($removed) {
            app(PageService::class)->forget();
        }
    }

    public function down(): void
    {
        // Not reversible: see 2026_08_08_000001's own down() note.
    }
};
