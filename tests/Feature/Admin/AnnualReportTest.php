<?php

namespace Tests\Feature\Admin;

use App\Models\AnnualReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class AnnualReportTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $report = AnnualReport::factory()->create();

        $this->get(route('admin.annual-reports.index'))->assertRedirect(route('login'));
        $this->get(route('admin.annual-reports.edit', $report))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_annual_reports(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.annual-reports.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.annual-reports.create'))->assertForbidden();
    }

    public function test_editor_can_view_the_annual_report_list(): void
    {
        $editor = $this->editor();
        AnnualReport::factory()->create(['title' => 'Annual Report 2024-2025']);

        $this->actingAs($editor)
            ->get(route('admin.annual-reports.index'))
            ->assertOk()
            ->assertSee('Annual Report 2024-2025');
    }

    public function test_editor_can_create_a_report_with_a_pdf(): void
    {
        $editor = $this->editor();

        $response = $this->actingAs($editor)->post(route('admin.annual-reports.store'), [
            'title' => 'Annual Report 2025-2026',
            'year' => '2025-2026',
            'attachment' => UploadedFile::fake()->create('report.pdf', 500, 'application/pdf'),
        ]);

        $response->assertRedirect(route('admin.annual-reports.index'));

        $report = AnnualReport::where('year', '2025-2026')->firstOrFail();
        $this->assertNotNull($report->attachment_url);
        $this->assertSame('report.pdf', $report->attachment_file_name);
    }

    public function test_a_report_requires_a_pdf_on_create(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.annual-reports.store'), [
                'title' => 'Annual Report 2025-2026',
                'year' => '2025-2026',
            ])
            ->assertSessionHasErrors('attachment');
    }

    public function test_the_attachment_must_be_a_pdf(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.annual-reports.store'), [
                'title' => 'Annual Report 2025-2026',
                'year' => '2025-2026',
                'attachment' => UploadedFile::fake()->create('report.docx', 500),
            ])
            ->assertSessionHasErrors('attachment');
    }

    public function test_editor_can_update_a_report_without_replacing_the_pdf(): void
    {
        $editor = $this->editor();
        $report = AnnualReport::factory()->create(['title' => 'Old Title', 'year' => '2024-2025']);
        $report->addMedia(UploadedFile::fake()->create('old.pdf', 500))->toMediaCollection('attachment');

        $this->actingAs($editor)->put(route('admin.annual-reports.update', $report), [
            'title' => 'New Title',
            'year' => '2024-2025',
        ])->assertRedirect(route('admin.annual-reports.index'));

        $report->refresh();
        $this->assertSame('New Title', $report->title);
        $this->assertSame('old.pdf', $report->attachment_file_name);
    }

    public function test_uploading_a_new_pdf_replaces_the_old_one(): void
    {
        $editor = $this->editor();
        $report = AnnualReport::factory()->create(['year' => '2024-2025']);
        $report->addMedia(UploadedFile::fake()->create('old.pdf', 500))->toMediaCollection('attachment');

        $this->actingAs($editor)->put(route('admin.annual-reports.update', $report), [
            'title' => $report->title,
            'year' => '2024-2025',
            'attachment' => UploadedFile::fake()->create('new.pdf', 500),
        ]);

        $report->refresh();
        $this->assertSame('new.pdf', $report->attachment_file_name);
        $this->assertSame(1, $report->getMedia('attachment')->count());
    }

    public function test_editor_can_delete_a_report(): void
    {
        $editor = $this->editor();
        $report = AnnualReport::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.annual-reports.destroy', $report))
            ->assertRedirect(route('admin.annual-reports.index'));

        $this->assertModelMissing($report);
    }

    public function test_viewer_cannot_create_or_delete_a_report(): void
    {
        $viewer = $this->viewer();
        $report = AnnualReport::factory()->create();

        $this->actingAs($viewer)->get(route('admin.annual-reports.create'))->assertForbidden();
        $this->actingAs($viewer)->delete(route('admin.annual-reports.destroy', $report))->assertForbidden();
    }
}
