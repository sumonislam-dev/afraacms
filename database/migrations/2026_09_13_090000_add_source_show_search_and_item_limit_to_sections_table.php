<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            // Only meaningful for the "content_list" type: which content
            // pool (news, notices, stories, projects - see
            // config/content_sources.php) this section pulls from.
            $table->string('source', 20)->nullable()->after('layout');

            // Only meaningful for listing-style types (news, notices,
            // stories, content_list): shows a search box that links out to
            // that content's full listing page with the typed query applied.
            $table->boolean('show_search')->default(false)->after('source');

            // Only meaningful for listing-style types (news, notices,
            // stories, projects, content_list): how many items to show.
            // Null falls back to that type's own default (see the frontend
            // partials) rather than a single site-wide count.
            $table->unsignedSmallInteger('item_limit')->nullable()->after('show_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['source', 'show_search', 'item_limit']);
        });
    }
};
