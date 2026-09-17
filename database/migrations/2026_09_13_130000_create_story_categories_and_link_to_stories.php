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
        Schema::create('story_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::table('stories', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('project_id')->constrained('story_categories')->nullOnDelete();
        });

        Schema::create('section_story_category', function (Blueprint $table) {
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('story_category_id')->constrained('story_categories')->cascadeOnDelete();
            $table->primary(['section_id', 'story_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * The foreign key drop is deliberately separate from the column drop
     * and wrapped in a try/catch: this database's tables were originally
     * created under MyISAM (which silently discards FOREIGN KEY clauses),
     * so a later repair migration had to add this constraint back under
     * its own name - which can drift out of sync with what this migration
     * expects depending on rollback order. dropConstrainedForeignId() does
     * both in one call and aborts the column drop too if the FK-drop half
     * fails, so a missing/differently-named constraint would otherwise
     * block this migration from ever reversing.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_story_category');

        if (Schema::hasColumn('stories', 'category_id')) {
            try {
                Schema::table('stories', function (Blueprint $table) {
                    $table->dropForeign(['category_id']);
                });
            } catch (\Throwable) {
                // Constraint didn't exist under this name; nothing to drop.
            }

            Schema::table('stories', function (Blueprint $table) {
                $table->dropColumn('category_id');
            });
        }

        Schema::dropIfExists('story_categories');
    }
};
