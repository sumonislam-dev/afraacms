<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.settings.edit'))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_settings(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.settings.edit'))->assertForbidden();
    }

    public function test_editor_can_view_settings_but_not_edit_them(): void
    {
        $editor = $this->editor();
        $this->seed(SettingsSeeder::class);

        $this->actingAs($editor)->get(route('admin.settings.edit'))->assertOk();

        $this->actingAs($editor)
            ->put(route('admin.settings.update'), ['site_name' => 'Hacked Name'])
            ->assertForbidden();
    }

    public function test_super_admin_can_update_settings(): void
    {
        $superAdmin = $this->superAdmin();
        $this->seed(SettingsSeeder::class);

        $response = $this->actingAs($superAdmin)->put(route('admin.settings.update'), [
            'site_name' => 'My Updated Site',
            'tagline' => 'A new tagline',
        ]);

        $response->assertRedirect(route('admin.settings.edit'));
        $this->assertSame('My Updated Site', Setting::where('key', 'site_name')->value('value'));
    }

    public function test_updating_settings_busts_the_settings_cache(): void
    {
        $superAdmin = $this->superAdmin();
        $this->seed(SettingsSeeder::class);

        $settings = app(\App\CMS\Services\SettingService::class);
        $this->assertSame('AfraaCMS', $settings->get('site_name'));

        $this->actingAs($superAdmin)->put(route('admin.settings.update'), ['site_name' => 'Fresh Name']);

        $this->assertSame('Fresh Name', $settings->get('site_name'));
    }
}
