<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.seo.edit'))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_seo_settings(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.seo.edit'))->assertForbidden();
    }

    public function test_editor_can_view_and_update_seo_settings(): void
    {
        $editor = $this->editor();
        $this->seed(SettingsSeeder::class);

        $this->actingAs($editor)->get(route('admin.seo.edit'))->assertOk();

        $response = $this->actingAs($editor)->put(route('admin.seo.update'), [
            'default_robots' => 'noindex, nofollow',
            'sitemap_include_projects' => false,
            'sitemap_include_galleries' => true,
            'robots_txt' => "User-agent: *\nDisallow: /admin",
        ]);

        $response->assertRedirect(route('admin.seo.edit'));
        $this->assertSame('noindex, nofollow', Setting::where('key', 'default_robots')->value('value'));
    }

    public function test_default_robots_must_be_a_known_value(): void
    {
        $editor = $this->editor();
        $this->seed(SettingsSeeder::class);

        $this->actingAs($editor)
            ->put(route('admin.seo.update'), ['default_robots' => 'not-a-real-directive'])
            ->assertSessionHasErrors('default_robots');
    }
}
