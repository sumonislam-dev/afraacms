<?php

namespace Tests\Feature;

use App\CMS\Services\SettingService;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function seedThemeSetting(string $key, string $value): void
    {
        // updateOrCreate, not create(): RefreshDatabase runs migrations but
        // not seeders, and this test doesn't call SettingsSeeder, so these
        // rows don't already exist - updateOrCreate both creates them here
        // and stays correct if a future test setup does seed them first.
        Setting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => 'branding']);
    }

    public function test_the_default_brand_color_and_fonts_are_rendered_in_the_style_override(): void
    {
        $this->seedThemeSetting('brand_color', '#f96d00');
        $this->seedThemeSetting('heading_font', 'merriweather');
        $this->seedThemeSetting('body_font', 'inter');

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('--color-brand-500: #f96d00', false);
        $response->assertSee("--font-display: 'Merriweather'", false);
        $response->assertSee("--font-body: 'Inter'", false);
    }

    public function test_changing_the_brand_color_changes_the_rendered_style_override(): void
    {
        $this->seedThemeSetting('brand_color', '#00b894');
        $this->seedThemeSetting('heading_font', 'poppins');
        $this->seedThemeSetting('body_font', 'open-sans');

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('--color-brand-500: #00b894', false);
        $response->assertSee("--font-display: 'Poppins'", false);
        $response->assertSee("--font-body: 'Open Sans'", false);
        $response->assertDontSee("--font-display: 'Merriweather'", false);
    }

    public function test_updating_settings_busts_the_cache_so_the_new_theme_shows_immediately(): void
    {
        $this->seedThemeSetting('brand_color', '#f96d00');

        $this->get('/')->assertSee('--color-brand-500: #f96d00', false);

        app(SettingService::class)->updateMany(['brand_color' => '#3366ff']);

        $this->get('/')->assertSee('--color-brand-500: #3366ff', false);
    }
}
