<?php

namespace Tests\Feature\Admin;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.contact.index'))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_the_inbox(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.contact.index'))->assertForbidden();
    }

    public function test_editor_can_view_the_inbox(): void
    {
        $editor = $this->editor();
        ContactMessage::factory()->create(['name' => 'Jane Visitor', 'subject' => 'A question']);

        $this->actingAs($editor)
            ->get(route('admin.contact.index'))
            ->assertOk()
            ->assertSee('Jane Visitor');
    }

    public function test_viewing_a_message_marks_it_as_read(): void
    {
        $editor = $this->editor();
        $message = ContactMessage::factory()->create(['is_read' => false]);

        $this->actingAs($editor)
            ->get(route('admin.contact.show', $message))
            ->assertOk();

        $this->assertTrue($message->fresh()->is_read);
    }

    public function test_a_user_without_permissions_cannot_view_a_message(): void
    {
        $user = $this->userWithoutPermissions();
        $message = ContactMessage::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.contact.show', $message))
            ->assertForbidden();
    }

    public function test_editor_can_delete_a_message(): void
    {
        $editor = $this->editor();
        $message = ContactMessage::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.contact.destroy', $message))
            ->assertRedirect(route('admin.contact.index'));

        $this->assertModelMissing($message);
    }

    public function test_a_user_without_permissions_cannot_delete_a_message(): void
    {
        $user = $this->userWithoutPermissions();
        $message = ContactMessage::factory()->create();

        $this->actingAs($user)
            ->delete(route('admin.contact.destroy', $message))
            ->assertForbidden();

        $this->assertModelExists($message);
    }

    public function test_search_filters_messages_by_name_email_or_subject(): void
    {
        $editor = $this->editor();
        ContactMessage::factory()->create(['name' => 'Alice Smith', 'subject' => 'Pricing question']);
        ContactMessage::factory()->create(['name' => 'Bob Jones', 'subject' => 'Support request']);

        $this->actingAs($editor)
            ->get(route('admin.contact.index', ['search' => 'Alice']))
            ->assertOk()
            ->assertSee('Alice Smith')
            ->assertDontSee('Bob Jones');
    }
}
