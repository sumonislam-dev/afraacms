<?php

namespace Tests\Feature;

use App\Models\FeaturedVisitor;
use App\Models\Page;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeaturedVisitorsSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_only_active_visitors(): void
    {
        FeaturedVisitor::factory()->create(['name' => 'Jane Diplomat', 'is_active' => true]);
        FeaturedVisitor::factory()->create(['name' => 'Hidden Visitor', 'is_active' => false]);

        $page = Page::factory()->create(['slug' => 'about', 'status' => 'published']);
        Section::factory()->for($page)->create(['type' => 'featured_visitors', 'heading' => 'Our Visitors']);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()->assertSee('Jane Diplomat')->assertDontSee('Hidden Visitor');
    }

    public function test_it_shows_the_visitors_organization_and_country(): void
    {
        FeaturedVisitor::factory()->create([
            'name' => 'Jane Diplomat',
            'organization' => 'UNICEF Bangladesh',
            'country' => 'Bangladesh',
        ]);

        $page = Page::factory()->create(['slug' => 'about', 'status' => 'published']);
        Section::factory()->for($page)->create(['type' => 'featured_visitors', 'heading' => 'Our Visitors']);

        $response = $this->get('/'.$page->slug);

        $response->assertOk()
            ->assertSee('Jane Diplomat')
            ->assertSee('UNICEF Bangladesh')
            ->assertSee('Bangladesh');
    }
}
