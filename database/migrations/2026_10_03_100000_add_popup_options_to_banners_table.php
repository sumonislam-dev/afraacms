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
        Schema::table('banners', function (Blueprint $table) {
            // Only meaningful for the "popup" type (see config/banners.php
            // popup_frequencies/popup_pages). The defaults match how popups
            // behaved before these options existed, so nothing changes for
            // existing banners.
            $table->string('popup_frequency', 20)->default('session')->after('sort_order');
            $table->string('popup_pages', 20)->default('all')->after('popup_frequency');
            $table->unsignedTinyInteger('popup_delay')->default(0)->after('popup_pages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['popup_frequency', 'popup_pages', 'popup_delay']);
        });
    }
};
