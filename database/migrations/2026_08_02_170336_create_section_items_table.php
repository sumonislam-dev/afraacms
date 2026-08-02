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
        Schema::create('section_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('body')->nullable();
            $table->foreignId('image')->nullable()->constrained('media_items')->nullOnDelete();
            $table->string('value')->nullable();
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['section_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_items');
    }
};
