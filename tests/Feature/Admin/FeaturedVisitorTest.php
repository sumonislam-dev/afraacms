<?php

namespace Tests\Feature\Admin;

use App\Models\FeaturedVisitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class FeaturedVisitorTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $visitor = FeaturedVisitor::factory()->create();

        $this->get(route('admin.featured-visitors.index'))->assertRedirect(route('login'));
        $this->get(route('admin.featured-visitors.edit', $visitor))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_featured_visitors(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.featured-visitors.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.featured-visitors.create'))->assertForbidden();
    }

    public function test_editor_can_view_the_featured_visitors_list(): void
    {
        $editor = $this->editor();
        FeaturedVisitor::factory()->create(['name' => 'Jane Diplomat']);

        $this->actingAs($editor)
            ->get(route('admin.featured-visitors.index'))
            ->assertOk()
            ->assertSee('Jane Diplomat');
    }

    public function test_editor_can_add_a_featured_visitor(): void
    {
        $editor = $this->editor();

        $response = $this->actingAs($editor)->post(route('admin.featured-visitors.store'), [
            'name' => 'Jane Diplomat',
            'organization' => 'UNICEF',
            'country' => 'Bangladesh',
            'visited_at' => '2026-02-10',
        ]);

        $response->assertRedirect(route('admin.featured-visitors.index'));
        $this->assertDatabaseHas('featured_visitors', [
            'name' => 'Jane Diplomat',
            'organization' => 'UNICEF',
            'country' => 'Bangladesh',
        ]);
    }

    public function test_a_user_without_permissions_cannot_add_a_featured_visitor(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->post(route('admin.featured-visitors.store'), [
            'name' => 'Sneaky Visitor',
            'country' => 'Nowhere',
            'visited_at' => '2026-02-10',
        ])->assertForbidden();

        $this->assertDatabaseMissing('featured_visitors', ['name' => 'Sneaky Visitor']);
    }

    public function test_adding_a_featured_visitor_requires_name_country_and_visit_date(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.featured-visitors.store'), [])
            ->assertSessionHasErrors(['name', 'country', 'visited_at']);
    }

    public function test_editor_can_update_a_featured_visitor(): void
    {
        $editor = $this->editor();
        $visitor = FeaturedVisitor::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($editor)->put(route('admin.featured-visitors.update', $visitor), [
            'name' => 'New Name',
            'country' => $visitor->country,
            'visited_at' => $visitor->visited_at->format('Y-m-d'),
            'is_active' => false,
        ]);

        $response->assertRedirect(route('admin.featured-visitors.index'));
        $visitor->refresh();
        $this->assertSame('New Name', $visitor->name);
        $this->assertFalse($visitor->is_active);
    }

    public function test_editor_can_delete_a_featured_visitor(): void
    {
        $editor = $this->editor();
        $visitor = FeaturedVisitor::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.featured-visitors.destroy', $visitor))
            ->assertRedirect(route('admin.featured-visitors.index'));

        $this->assertNull(FeaturedVisitor::find($visitor->id));
        $this->assertSoftDeleted($visitor);
    }

    public function test_editor_can_restore_a_trashed_featured_visitor(): void
    {
        $editor = $this->editor();
        $visitor = FeaturedVisitor::factory()->create();
        $visitor->delete();

        $this->actingAs($editor)
            ->post(route('admin.featured-visitors.restore', $visitor))
            ->assertRedirect(route('admin.featured-visitors.trash'));

        $this->assertNotSoftDeleted($visitor);
    }
}
