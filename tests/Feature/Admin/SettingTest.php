<?php

namespace Tests\Feature\Admin;

use App\CMS\Services\SettingService;
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

    public function test_editor_can_view_and_edit_general_settings(): void
    {
        $editor = $this->editor();
        $this->seed(SettingsSeeder::class);

        $this->actingAs($editor)->get(route('admin.settings.edit'))->assertOk();

        $this->actingAs($editor)
            ->put(route('admin.settings.update'), ['site_name' => 'Client Renamed Site'])
            ->assertRedirect(route('admin.settings.edit'));

        $this->assertSame('Client Renamed Site', Setting::where('key', 'site_name')->value('value'));
    }

    public function test_editor_cannot_change_the_locked_developer_credit_even_by_forging_the_field(): void
    {
        $editor = $this->editor();
        $this->seed(SettingsSeeder::class);
        $original = Setting::where('key', 'developer_credit_text')->value('value');

        $this->actingAs($editor)
            ->put(route('admin.settings.update'), [
                'site_name' => 'Client Renamed Site',
                'developer_credit_text' => 'Hacked Credit',
                'developer_credit_url' => 'https://evil.example',
            ])
            ->assertRedirect(route('admin.settings.edit'));

        $this->assertSame($original, Setting::where('key', 'developer_credit_text')->value('value'));
    }

    public function test_super_admin_can_change_the_developer_credit(): void
    {
        $superAdmin = $this->superAdmin();
        $this->seed(SettingsSeeder::class);

        $this->actingAs($superAdmin)
            ->put(route('admin.settings.update'), [
                'developer_credit_text' => 'Developed by NewAgency',
                'developer_credit_url' => 'https://newagency.example',
            ])
            ->assertRedirect(route('admin.settings.edit'));

        $this->assertSame('Developed by NewAgency', Setting::where('key', 'developer_credit_text')->value('value'));
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

        $settings = app(SettingService::class);
        $this->assertSame('AfraaCMS', $settings->get('site_name'));

        $this->actingAs($superAdmin)->put(route('admin.settings.update'), ['site_name' => 'Fresh Name']);

        $this->assertSame('Fresh Name', $settings->get('site_name'));
    }
}
