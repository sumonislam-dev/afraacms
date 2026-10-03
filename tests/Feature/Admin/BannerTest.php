<?php

namespace Tests\Feature\Admin;

use App\Models\Banner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class BannerTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.banners.index'))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_banners(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.banners.index'))->assertForbidden();
    }

    public function test_editor_can_view_the_banner_list(): void
    {
        $editor = $this->editor();
        Banner::factory()->create(['title' => 'Summer Sale']);

        $this->actingAs($editor)
            ->get(route('admin.banners.index'))
            ->assertOk()
            ->assertSee('Summer Sale');
    }

    public function test_editor_can_create_a_banner(): void
    {
        $editor = $this->editor();

        $response = $this->actingAs($editor)->post(route('admin.banners.store'), [
            'type' => 'homepage',
            'title' => 'New Banner',
        ]);

        $response->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseHas('banners', ['title' => 'New Banner', 'type' => 'homepage']);
    }

    public function test_editor_can_create_a_popup_with_display_options(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)->post(route('admin.banners.store'), [
            'type' => 'popup',
            'title' => 'Webinar',
            'popup_frequency' => 'daily',
            'popup_pages' => 'home',
            'popup_delay' => 5,
        ])->assertRedirect(route('admin.banners.index'));

        $this->assertDatabaseHas('banners', [
            'title' => 'Webinar',
            'popup_frequency' => 'daily',
            'popup_pages' => 'home',
            'popup_delay' => 5,
        ]);
    }

    public function test_a_banner_created_without_popup_options_gets_the_defaults(): void
    {
        $this->actingAs($this->editor())->post(route('admin.banners.store'), ['type' => 'popup', 'title' => 'Plain']);

        $this->assertDatabaseHas('banners', [
            'title' => 'Plain',
            'popup_frequency' => 'session',
            'popup_pages' => 'all',
            'popup_delay' => 0,
        ]);
    }

    public function test_popup_options_must_be_known_values(): void
    {
        $this->actingAs($this->editor())
            ->post(route('admin.banners.store'), [
                'type' => 'popup',
                'popup_frequency' => 'hourly',
                'popup_pages' => 'contact',
                'popup_delay' => 61,
            ])
            ->assertSessionHasErrors(['popup_frequency', 'popup_pages', 'popup_delay']);
    }

    public function test_button_text_requires_a_button_url(): void
    {
        $this->actingAs($this->editor())
            ->post(route('admin.banners.store'), ['type' => 'cta', 'button_text' => 'Donate'])
            ->assertSessionHasErrors('button_url');
    }

    public function test_button_url_requires_button_text(): void
    {
        $this->actingAs($this->editor())
            ->post(route('admin.banners.store'), ['type' => 'cta', 'button_url' => '/donate'])
            ->assertSessionHasErrors('button_text');
    }

    public function test_a_half_filled_button_is_rejected_on_update_too(): void
    {
        $banner = Banner::factory()->create(['type' => 'cta']);

        $this->actingAs($this->editor())
            ->put(route('admin.banners.update', $banner), ['type' => 'cta', 'button_text' => 'Donate'])
            ->assertSessionHasErrors('button_url');
    }

    public function test_a_banner_without_any_button_is_still_valid(): void
    {
        $this->actingAs($this->editor())
            ->post(route('admin.banners.store'), ['type' => 'cta', 'title' => 'No button'])
            ->assertSessionHasNoErrors();
    }

    public function test_a_banner_type_must_be_a_known_placement(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.banners.store'), ['type' => 'not-a-real-placement'])
            ->assertSessionHasErrors('type');
    }

    public function test_end_date_must_not_be_before_start_date(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.banners.store'), [
                'type' => 'homepage',
                'starts_at' => '2026-06-10',
                'ends_at' => '2026-06-01',
            ])
            ->assertSessionHasErrors('ends_at');
    }

    public function test_a_user_without_permissions_cannot_create_a_banner(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->post(route('admin.banners.store'), ['type' => 'homepage'])
            ->assertForbidden();

        $this->assertDatabaseCount('banners', 0);
    }

    public function test_editor_can_update_a_banner(): void
    {
        $editor = $this->editor();
        $banner = Banner::factory()->create(['title' => 'Old Title']);

        $this->actingAs($editor)->put(route('admin.banners.update', $banner), [
            'type' => $banner->type,
            'title' => 'New Title',
            'is_active' => false,
        ])->assertRedirect(route('admin.banners.index'));

        $banner->refresh();
        $this->assertSame('New Title', $banner->title);
        $this->assertFalse($banner->is_active);
    }

    public function test_editor_can_delete_a_banner(): void
    {
        $editor = $this->editor();
        $banner = Banner::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.banners.destroy', $banner))
            ->assertRedirect(route('admin.banners.index'));

        $this->assertModelMissing($banner);
    }

    public function test_a_user_without_permissions_cannot_delete_a_banner(): void
    {
        $user = $this->userWithoutPermissions();
        $banner = Banner::factory()->create();

        $this->actingAs($user)
            ->delete(route('admin.banners.destroy', $banner))
            ->assertForbidden();

        $this->assertModelExists($banner);
    }
}
