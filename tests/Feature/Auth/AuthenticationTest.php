<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_an_inactive_user_cannot_authenticate(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    /**
     * EnsureUserIsActive (bootstrap/app.php's web middleware group) is what
     * makes deactivation take effect immediately instead of only on the
     * user's NEXT login - without it, an already-logged-in user keeps
     * working until their session naturally expires.
     */
    public function test_a_user_deactivated_mid_session_is_logged_out_on_their_next_request(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)->get(route('profile.edit'))->assertOk();

        $user->update(['is_active' => false]);

        $this->actingAs($user)->get(route('profile.edit'))->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
