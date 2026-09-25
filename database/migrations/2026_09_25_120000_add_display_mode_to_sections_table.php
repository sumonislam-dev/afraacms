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
            // Only meaningful for the "content_list" type: "preview" caps the
            // section at item_limit items with a "View All" button (today's
            // look, and the default so nothing changes for existing
            // sections); "paginate" shows real prev/next pagination controls
            // instead, for a section that IS the full listing (no button).
            $table->string('display_mode', 20)->default('preview')->after('item_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('display_mode');
        });
    }
};
