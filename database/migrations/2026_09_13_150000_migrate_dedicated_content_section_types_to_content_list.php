<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Folds the old dedicated "news"/"stories"/"projects"/"notices" Section
 * types into the generic "content_list" type (source = the old type name).
 * content_list already has full capability parity with all four (added in
 * the previous migration's app-layer changes: button_text/button_url
 * rendering, category/specific picking, item_limit, layout, show_search) -
 * see config/content_sources.php.
 *
 * No pivot data needs to move: "notices" already read/wrote the same
 * section_news_category/section_news_post tables content_list uses for
 * source=news/notices, and "news"/"stories"/"projects" never had picker
 * data of their own to carry over. This is a pure relabel.
 */
return new class extends Migration
{
    private array $types = ['news', 'stories', 'projects', 'notices'];

    public function up(): void
    {
        foreach ($this->types as $type) {
            DB::table('sections')->where('type', $type)->update([
                'type' => 'content_list',
                'source' => $type,
            ]);
        }
    }

    /**
     * Best-effort only: a content_list section created fresh after this
     * migration ran (rather than relabeled by it) is indistinguishable from
     * a migrated one by row data alone, and would be wrongly reverted too.
     */
    public function down(): void
    {
        foreach ($this->types as $type) {
            DB::table('sections')
                ->where('type', 'content_list')
                ->where('source', $type)
                ->update(['type' => $type, 'source' => null]);
        }
    }
};
