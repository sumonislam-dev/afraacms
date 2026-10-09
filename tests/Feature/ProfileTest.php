<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    /**
     * This self-service flow bypasses UserPolicy entirely (it's not an
     * admin-panel action), so the "can't delete the last active Super
     * Admin" protection has to be re-checked directly in the controller -
     * otherwise the sole Super Admin could lock everyone out via their own
     * profile page.
     */
    public function test_the_last_active_super_admin_cannot_delete_their_own_account(): void
    {
        // CreatesAdminUsers::superAdmin() creates an ADDITIONAL Super Admin
        // on top of the seeded default account, so it's never actually the
        // last one - seed directly and use the one-and-only seeded account
        // to genuinely exercise that scenario.
        $this->seed(RolesAndPermissionsSeeder::class);
        $superAdmin = User::where('email', config('admin.super_admin.email'))->firstOrFail();

        $response = $this
            ->actingAs($superAdmin)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response->assertRedirect('/profile');
        $this->assertAuthenticated();
        $this->assertNotNull($superAdmin->fresh());
    }

    public function test_a_super_admin_can_delete_their_own_account_if_another_one_exists(): void
    {
        $superAdmin = $this->superAdmin();
        $otherSuperAdmin = User::factory()->create();
        $otherSuperAdmin->assignRole('Super Admin');

        $response = $this
            ->actingAs($superAdmin)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response->assertRedirect('/');
        $this->assertGuest();
        $this->assertNull($superAdmin->fresh());
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
