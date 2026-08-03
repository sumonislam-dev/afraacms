<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->boolean('open_in_new_tab')->default(false)->after('url');
        });

        // Preserve existing behavior: external links used to always open in a
        // new tab (hardcoded in MenuItem::getTargetAttribute()), so carry
        // that forward as an explicit value for rows that already exist.
        DB::table('menu_items')->where('type', 'external')->update(['open_in_new_tab' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('open_in_new_tab');
        });
    }
};
