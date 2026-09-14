<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Repairs a schema drift some dev databases picked up from the migration
 * squash: create_story_categories_and_link_to_stories was recorded as run
 * without section_story_category actually existing, breaking every page
 * (PageService eager-loads storyCategories on every section unconditionally).
 * Guarded so it's a no-op on a database where the table is already there.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('section_story_category')) {
            return;
        }

        Schema::create('section_story_category', function (Blueprint $table) {
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('story_category_id')->constrained('story_categories')->cascadeOnDelete();
            $table->primary(['section_id', 'story_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_story_category');
    }
};
