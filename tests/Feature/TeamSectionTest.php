<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_team_section_renders_its_members(): void
    {
        $page = Page::factory()->create(['slug' => 'about', 'status' => 'published']);
        $section = Section::factory()->for($page)->create([
            'type' => 'team',
            'heading' => 'Our Team',
        ]);
        $section->items()->create([
            'title' => 'Jane Doe',
            'subtitle' => 'Executive Director',
            'body' => 'Leads the organisation.',
        ]);

        $this->get('/'.$page->slug)
            ->assertOk()
            ->assertSee('Our Team')
            ->assertSee('Jane Doe')
            ->assertSee('Executive Director');
    }
}
