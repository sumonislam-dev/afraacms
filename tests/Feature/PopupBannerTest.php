<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class PopupBannerTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    private function visitPage(): TestResponse
    {
        $page = Page::factory()->create(['slug' => 'about', 'status' => 'published']);

        return $this->get('/'.$page->slug)->assertOk();
    }

    public function test_an_active_popup_banner_renders_as_a_dialog(): void
    {
        Banner::factory()->create(['type' => 'popup', 'title' => 'Join our webinar']);

        $this->visitPage()
            ->assertSee('Join our webinar')
            ->assertSee('role="dialog"', false)
            ->assertSee('sessionStorage', false);
    }

    public function test_the_popup_carries_its_frequency_and_delay_to_the_page(): void
    {
        Banner::factory()->create(['type' => 'popup', 'title' => 'Hello', 'popup_frequency' => 'daily', 'popup_delay' => 7]);

        $this->visitPage()
            ->assertSee("frequency: 'daily'", false)
            ->assertSee('delay: 7', false);
    }

    public function test_editing_a_popup_changes_its_dismissal_key(): void
    {
        $this->travelTo(now()->startOfMinute());
        $banner = Banner::factory()->create(['type' => 'popup', 'title' => 'Hello']);
        $before = 'banner-popup-dismissed-'.$banner->id.'-'.$banner->updated_at->getTimestamp();

        $this->visitPage()->assertSee($before, false);

        $this->travel(5)->minutes();
        $this->actingAs($this->editor())
            ->put(route('admin.banners.update', $banner), ['type' => 'popup', 'title' => 'Hello again']);

        $this->get('/about')->assertSee('Hello again')->assertDontSee($before, false);
    }

    public function test_a_homepage_only_popup_is_left_out_of_other_pages(): void
    {
        Banner::factory()->create(['type' => 'popup', 'title' => 'Home only', 'popup_pages' => 'home']);

        $this->visitPage()->assertDontSee('Home only');
        $this->get('/')->assertOk()->assertSee('Home only');
    }

    public function test_no_popup_renders_without_an_active_popup_banner(): void
    {
        Banner::factory()->create(['type' => 'popup', 'title' => 'Hidden popup', 'is_active' => false]);

        $this->visitPage()
            ->assertDontSee('Hidden popup')
            ->assertDontSee('role="dialog"', false);
    }

    public function test_a_scheduled_popup_appears_once_its_start_time_passes_without_an_admin_edit(): void
    {
        $this->travelTo(now()->startOfMinute());
        Banner::factory()->create(['type' => 'popup', 'title' => 'Starts soon', 'starts_at' => now()->addHour()]);

        $this->visitPage()->assertDontSee('Starts soon');

        $this->travel(61)->minutes();

        $this->get('/about')->assertOk()->assertSee('Starts soon');
    }

    public function test_a_popup_disappears_once_its_end_time_passes_without_an_admin_edit(): void
    {
        $this->travelTo(now()->startOfMinute());
        Banner::factory()->create(['type' => 'popup', 'title' => 'Ends soon', 'ends_at' => now()->addHour()]);

        $this->visitPage()->assertSee('Ends soon');

        $this->travel(61)->minutes();

        $this->get('/about')->assertOk()->assertDontSee('Ends soon');
    }
}
