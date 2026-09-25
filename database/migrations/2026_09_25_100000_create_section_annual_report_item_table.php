<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pivot table for the "content_list" (global) Section type's "annual_reports"
 * source - which specific reports a section has hand-picked. See
 * config/content_sources.php and 2026_09_13_091000_create_content_list_pivot_tables
 * for the identical pattern the other sources use. Annual reports have no
 * category concept, so (unlike news/projects/stories) there's no
 * section_annual_report_category table to go with this one.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_annual_report_item', function (Blueprint $table) {
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('annual_report_id')->constrained()->cascadeOnDelete();
            $table->primary(['section_id', 'annual_report_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_annual_report_item');
    }
};
