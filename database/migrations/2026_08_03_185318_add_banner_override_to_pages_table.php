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
        Schema::table('pages', function (Blueprint $table) {
            $table->foreignId('banner_image')->nullable()->after('template')->constrained('media_items')->nullOnDelete();
            $table->string('banner_eyebrow')->nullable()->after('banner_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * The foreign key drop is deliberately separate from the column drop
     * and wrapped in a try/catch - see the identical situation and full
     * explanation in create_story_categories_and_link_to_stories's down().
     */
    public function down(): void
    {
        if (Schema::hasColumn('pages', 'banner_image')) {
            try {
                Schema::table('pages', function (Blueprint $table) {
                    $table->dropForeign(['banner_image']);
                });
            } catch (Throwable) {
                // Constraint didn't exist under this name; nothing to drop.
            }

            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('banner_image');
            });
        }

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('banner_eyebrow');
        });
    }
};
