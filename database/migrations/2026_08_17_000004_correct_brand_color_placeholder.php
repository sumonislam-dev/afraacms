<?php

use App\CMS\Services\SettingService;
use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Corrects brand_color's existing value: it was seeded with a
     * placeholder ('#4f46e5') back when this field was inert and did
     * nothing. Now that it's wired to the runtime theming layer
     * (app/CMS/Helpers/theme.php), leaving that value in place would
     * silently flip the live site's color scheme the moment this ships -
     * so it's corrected here to '#f96d00', the color already hardcoded
     * (and actually in use) in resources/css/app.css, making this migration
     * visually a no-op until an admin deliberately picks a new color. Only
     * applied if still exactly that untouched placeholder - a real admin
     * customization is left alone.
     *
     * heading_font/body_font don't need seeding here - they're declared in
     * config/settings.php's "branding" group like every other setting, so
     * SettingsSeeder already creates them from that single source. This
     * migration exists only for the one thing a seeder's firstOrCreate can
     * never do: correcting an already-existing row's value.
     */
    public function up(): void
    {
        Setting::where('key', 'brand_color')->where('value', '#4f46e5')->update(['value' => '#f96d00']);

        app(SettingService::class)->forget();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('key', 'brand_color')->where('value', '#f96d00')->update(['value' => '#4f46e5']);

        app(SettingService::class)->forget();
    }
};
