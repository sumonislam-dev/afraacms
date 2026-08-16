<?php

namespace Tests\Feature\Admin;

use App\Models\VisitorBookEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class VisitorBookEntryTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.visitor-book.index'))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_the_visitor_book(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.visitor-book.index'))->assertForbidden();
    }

    public function test_editor_sees_pending_entries_by_default(): void
    {
        $editor = $this->editor();
        VisitorBookEntry::factory()->create(['visitor_name' => 'Pending Person']);
        VisitorBookEntry::factory()->approved()->create(['visitor_name' => 'Approved Person']);

        $this->actingAs($editor)
            ->get(route('admin.visitor-book.index'))
            ->assertOk()
            ->assertSee('Pending Person')
            ->assertDontSee('Approved Person');
    }

    public function test_editor_can_filter_by_approved_status(): void
    {
        $editor = $this->editor();
        VisitorBookEntry::factory()->create(['visitor_name' => 'Pending Person']);
        VisitorBookEntry::factory()->approved()->create(['visitor_name' => 'Approved Person']);

        $this->actingAs($editor)
            ->get(route('admin.visitor-book.index', ['status' => 'approved']))
            ->assertOk()
            ->assertSee('Approved Person')
            ->assertDontSee('Pending Person');
    }

    public function test_editor_can_approve_a_pending_entry(): void
    {
        $editor = $this->editor();
        $entry = VisitorBookEntry::factory()->create();

        $this->actingAs($editor)
            ->post(route('admin.visitor-book.approve', $entry))
            ->assertRedirect();

        $this->assertSame('approved', $entry->fresh()->status);
    }

    public function test_editor_can_reject_a_pending_entry(): void
    {
        $editor = $this->editor();
        $entry = VisitorBookEntry::factory()->create();

        $this->actingAs($editor)
            ->post(route('admin.visitor-book.reject', $entry))
            ->assertRedirect();

        $this->assertSame('rejected', $entry->fresh()->status);
    }

    public function test_a_user_without_permissions_cannot_approve_an_entry(): void
    {
        $user = $this->userWithoutPermissions();
        $entry = VisitorBookEntry::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.visitor-book.approve', $entry))
            ->assertForbidden();

        $this->assertSame('pending', $entry->fresh()->status);
    }

    public function test_editor_can_delete_an_entry(): void
    {
        $editor = $this->editor();
        $entry = VisitorBookEntry::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.visitor-book.destroy', $entry))
            ->assertRedirect(route('admin.visitor-book.index'));

        $this->assertDatabaseMissing('visitor_book_entries', ['id' => $entry->id]);
    }

    public function test_editor_can_view_a_single_entry(): void
    {
        $editor = $this->editor();
        $entry = VisitorBookEntry::factory()->create(['opinion' => 'A truly wonderful project.']);

        $this->actingAs($editor)
            ->get(route('admin.visitor-book.show', $entry))
            ->assertOk()
            ->assertSee('A truly wonderful project.');
    }
}
