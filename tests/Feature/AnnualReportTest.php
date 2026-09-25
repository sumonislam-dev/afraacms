<?php

namespace Tests\Feature;

use App\Models\AnnualReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AnnualReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_the_annual_reports_index_lists_only_active_reports(): void
    {
        $active = AnnualReport::factory()->create(['title' => 'Active Report', 'year' => '2025-2026']);
        $active->addMedia(UploadedFile::fake()->create('active.pdf', 500))->toMediaCollection('attachment');

        $inactive = AnnualReport::factory()->create(['title' => 'Inactive Report', 'year' => '2024-2025', 'is_active' => false]);
        $inactive->addMedia(UploadedFile::fake()->create('inactive.pdf', 500))->toMediaCollection('attachment');

        $response = $this->get(route('annual-reports.index'));

        $response->assertOk()->assertSee('Active Report')->assertDontSee('Inactive Report');
    }

    public function test_the_annual_reports_index_orders_newest_year_first(): void
    {
        $older = AnnualReport::factory()->create(['title' => 'Older Report', 'year' => '2022-2023']);
        $older->addMedia(UploadedFile::fake()->create('older.pdf', 500))->toMediaCollection('attachment');

        $newer = AnnualReport::factory()->create(['title' => 'Newer Report', 'year' => '2025-2026']);
        $newer->addMedia(UploadedFile::fake()->create('newer.pdf', 500))->toMediaCollection('attachment');

        $response = $this->get(route('annual-reports.index'));

        $content = $response->getContent();
        $this->assertLessThan(strpos($content, 'Older Report'), strpos($content, 'Newer Report'));
    }
}
