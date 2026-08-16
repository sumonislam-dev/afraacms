<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\VisitorBookEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorBookTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_visitor_can_submit_an_opinion_and_it_starts_pending(): void
    {
        $project = Project::factory()->published()->create(['slug' => 'clean-water-project']);

        $response = $this->post(route('projects.visitor-book.store', $project->slug), [
            'visitor_name' => 'Jane Doe',
            'visitor_email' => 'jane@example.com',
            'opinion' => 'This project changed my community for the better.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('visitor_book_entries', [
            'project_id' => $project->id,
            'visitor_name' => 'Jane Doe',
            'status' => 'pending',
        ]);
    }

    public function test_the_honeypot_field_silently_blocks_bot_submissions(): void
    {
        $project = Project::factory()->published()->create(['slug' => 'clean-water-project']);

        $response = $this->post(route('projects.visitor-book.store', $project->slug), [
            'visitor_name' => 'Bot',
            'opinion' => 'Spam opinion.',
            'website' => 'http://spam.example.com',
        ]);

        $response->assertSessionHasErrors('website');
        $this->assertDatabaseMissing('visitor_book_entries', ['visitor_name' => 'Bot']);
    }

    public function test_submitting_requires_name_and_opinion(): void
    {
        $project = Project::factory()->published()->create();

        $this->post(route('projects.visitor-book.store', $project->slug), [])
            ->assertSessionHasErrors(['visitor_name', 'opinion']);
    }

    public function test_a_pending_entry_does_not_show_on_the_project_page(): void
    {
        $project = Project::factory()->published()->create(['slug' => 'clean-water-project']);
        VisitorBookEntry::factory()->for($project)->create(['visitor_name' => 'Pending Visitor']);

        $response = $this->get(route('projects.show', $project->slug));

        $response->assertOk()->assertDontSee('Pending Visitor');
    }

    public function test_an_approved_entry_shows_on_the_project_page(): void
    {
        $project = Project::factory()->published()->create(['slug' => 'clean-water-project']);
        VisitorBookEntry::factory()->approved()->for($project)->create(['visitor_name' => 'Approved Visitor']);

        $response = $this->get(route('projects.show', $project->slug));

        $response->assertOk()->assertSee('Approved Visitor');
    }

    public function test_a_rejected_entry_does_not_show_on_the_project_page(): void
    {
        $project = Project::factory()->published()->create(['slug' => 'clean-water-project']);
        VisitorBookEntry::factory()->rejected()->for($project)->create(['visitor_name' => 'Rejected Visitor']);

        $response = $this->get(route('projects.show', $project->slug));

        $response->assertOk()->assertDontSee('Rejected Visitor');
    }

    public function test_the_site_wide_visitor_book_only_lists_approved_entries(): void
    {
        $project = Project::factory()->published()->create();
        VisitorBookEntry::factory()->approved()->for($project)->create(['visitor_name' => 'Shown Visitor']);
        VisitorBookEntry::factory()->for($project)->create(['visitor_name' => 'Hidden Pending Visitor']);
        VisitorBookEntry::factory()->rejected()->for($project)->create(['visitor_name' => 'Hidden Rejected Visitor']);

        $response = $this->get(route('visitor-book.index'));

        $response->assertOk()
            ->assertSee('Shown Visitor')
            ->assertDontSee('Hidden Pending Visitor')
            ->assertDontSee('Hidden Rejected Visitor');
    }
}
