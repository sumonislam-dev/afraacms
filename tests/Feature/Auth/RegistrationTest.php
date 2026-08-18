<?php

namespace Tests\Feature\Auth;

use App\CMS\Services\SettingService;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password1234',
            'password_confirmation' => 'Password1234',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_registration_is_blocked_when_disabled_in_settings(): void
    {
        Setting::create([
            'group' => 'system',
            'key' => 'registration_enabled',
            'value' => '0',
            'type' => 'boolean',
            'autoload' => true,
            'sort_order' => 0,
        ]);
        app(SettingService::class)->forget();

        $this->get('/register')->assertNotFound();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password1234',
            'password_confirmation' => 'Password1234',
        ]);

        $response->assertNotFound();
        $this->assertGuest();
    }
}
