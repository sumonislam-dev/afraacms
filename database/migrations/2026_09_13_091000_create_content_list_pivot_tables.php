<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pivot tables for the "content_list" (global) Section type and the
 * "notices" Section type: which categories/items a section has picked from
 * each content_sources pool. See config/content_sources.php.
 *
 * The stories pool's own category pivot (section_story_category) isn't
 * created here - it depends on story_categories, which doesn't exist yet at
 * this point in the schema. See create_story_categories_and_link_to_stories.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('section_news_category', function (Blueprint $table) {
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('news_category_id')->constrained('news_categories')->cascadeOnDelete();
            $table->primary(['section_id', 'news_category_id']);
        });

        Schema::create('section_news_post', function (Blueprint $table) {
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('news_post_id')->constrained('news_posts')->cascadeOnDelete();
            $table->primary(['section_id', 'news_post_id']);
        });

        Schema::create('section_project_category', function (Blueprint $table) {
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_category_id')->constrained('project_categories')->cascadeOnDelete();
            $table->primary(['section_id', 'project_category_id']);
        });

        Schema::create('section_project_item', function (Blueprint $table) {
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->primary(['section_id', 'project_id']);
        });

        Schema::create('section_story_item', function (Blueprint $table) {
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('story_id')->constrained('stories')->cascadeOnDelete();
            $table->primary(['section_id', 'story_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_story_item');
        Schema::dropIfExists('section_project_item');
        Schema::dropIfExists('section_project_category');
        Schema::dropIfExists('section_news_post');
        Schema::dropIfExists('section_news_category');
    }
};
