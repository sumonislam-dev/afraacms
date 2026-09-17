<?php

namespace Database\Seeders;

use App\CMS\Services\FeaturedVisitorService;
use App\Models\Donation;
use App\Models\FeaturedVisitor;
use App\Models\Page;
use App\Models\Project;
use App\Models\VisitorBookEntry;
use Illuminate\Database\Seeder;

/**
 * Seeds just the three modules that neither RsufDemoSeeder nor
 * TrainingCertificateSeeder cover: donations, the visitor book, and featured
 * visitors. Split out of TestDataSeeder so it can be re-run after a
 * migrate:fresh without also duplicating TestDataSeeder's projects/stories/
 * news/etc on top of the real RsufDemoSeeder content.
 *
 * Not idempotent (uses factories) - safe to run on an empty dev database,
 * but re-running it adds a second batch rather than restoring the first.
 */
class DemoDonationsAndVisitorsSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();

        if ($projects->isEmpty()) {
            $this->command?->warn('No projects found - run RsufDemoSeeder (or TestDataSeeder) first.');

            return;
        }

        Donation::factory()
            ->count(8)
            ->state(fn () => ['project_id' => $projects->random()->id])
            ->create();
        Donation::factory()->refunded()->count(2)->create();
        Donation::factory()->withoutEmail()->count(2)->create();

        VisitorBookEntry::factory()
            ->approved()
            ->count(5)
            ->state(fn () => ['project_id' => $projects->random()->id])
            ->create();
        VisitorBookEntry::factory()
            ->count(3)
            ->state(fn () => ['project_id' => $projects->random()->id])
            ->create(); // pending
        VisitorBookEntry::factory()
            ->rejected()
            ->count(2)
            ->state(fn () => ['project_id' => $projects->random()->id])
            ->create();

        FeaturedVisitor::factory()->count(8)->create();
        FeaturedVisitor::factory()->create(['is_active' => false, 'name' => 'Hidden Test Visitor']);

        $this->attachFeaturedVisitorsSection();

        app(FeaturedVisitorService::class)->forget();

        $this->command?->info('Donations, visitor book entries, and featured visitors seeded.');
    }

    private function attachFeaturedVisitorsSection(): void
    {
        $page = Page::where('slug', 'about')->first()
            ?? Page::where('status', 'published')->first();

        if (! $page || $page->sections()->where('type', 'featured_visitors')->exists()) {
            return;
        }

        $page->sections()->create([
            'type' => 'featured_visitors',
            'heading' => 'Our Visitors',
            'subheading' => 'Demo Data',
            'sort_order' => ($page->sections()->max('sort_order') ?? -1) + 1,
        ]);
    }
}
