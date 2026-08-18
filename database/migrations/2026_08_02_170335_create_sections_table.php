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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->string('type');
            $table->string('heading')->nullable();
            // text(), not string(): most section types use this as a short
            // eyebrow label, but CTA sections use it as a full descriptive
            // paragraph (see resources/views/components/frontend/cta.blade.php),
            // so it can't be capped at the app's default indexed-string length.
            $table->text('subheading')->nullable();
            $table->longText('body')->nullable();
            $table->foreignId('image')->nullable()->constrained('media_items')->nullOnDelete();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('layout')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['page_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
