<?php

namespace Tests\Feature\Admin;

use App\Models\ContactMessage;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_super_admin_sees_every_stat_card_and_recent_activity(): void
    {
        $admin = $this->superAdmin();
        Page::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Pages');
        $response->assertSee('Projects');
        $response->assertSee('Users');
        $response->assertSee('Roles');
        $response->assertSee('Recent Activity');
    }

    public function test_editor_only_sees_content_cards_and_no_activity_feed(): void
    {
        $editor = $this->editor();

        $response = $this->actingAs($editor)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Pages');
        $response->assertSee('Quick Actions');
        $response->assertDontSee('Users');
        $response->assertDontSee('Roles');
        $response->assertDontSee('Recent Activity');
    }

    public function test_unread_contact_messages_are_flagged_on_the_dashboard(): void
    {
        $admin = $this->superAdmin();
        ContactMessage::factory()->create(['is_read' => false]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('1 unread');
    }
}
