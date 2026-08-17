<?php

namespace Database\Seeders;

use App\CMS\Services\BannerService;
use App\CMS\Services\FeaturedVisitorService;
use App\CMS\Services\GalleryService;
use App\CMS\Services\NewsService;
use App\CMS\Services\PageService;
use App\CMS\Services\ProjectService;
use App\CMS\Services\StoryService;
use App\CMS\Services\TeamService;
use App\Models\Banner;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\Donation;
use App\Models\Enrollment;
use App\Models\FeaturedVisitor;
use App\Models\Gallery;
use App\Models\NewsCategory;
use App\Models\NewsPost;
use App\Models\Page;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Story;
use App\Models\Student;
use App\Models\TeamCategory;
use App\Models\TeamMember;
use App\Models\VisitorBookEntry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds a batch of fake sample rows across every content module, purely to
 * exercise each admin screen (list/create/edit/trash, every status a module
 * supports) during manual QA - this is NOT real client content (see
 * RsufDemoSeeder for that).
 *
 * Safe to run repeatedly: categories are matched by a fixed name via
 * firstOrCreate() rather than re-created each time, and every slug this
 * seeder generates gets a random suffix appended - fake()->unique() alone
 * only guarantees uniqueness within a single process, so a second `db:seed`
 * run (a fresh process) can otherwise collide with slugs the first run
 * already wrote to the database.
 *
 * Run standalone whenever a fresh batch of test data is needed:
 *   php artisan db:seed --class=TestDataSeeder
 */
class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $projectCategories = collect(['Test Project Category A', 'Test Project Category B', 'Test Project Category C'])
            ->map(fn (string $name) => ProjectCategory::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]));

        $newsCategories = collect(['Test News Category A', 'Test News Category B', 'Test News Category C'])
            ->map(fn (string $name) => NewsCategory::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]));

        $teamCategories = collect(['Test Team Category A', 'Test Team Category B'])
            ->map(fn (string $name) => TeamCategory::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]));

        $projects = Project::factory()
            ->published()
            ->count(6)
            ->state(fn () => ['slug' => self::uniqueSlug(), 'category_id' => $projectCategories->random()->id])
            ->create();
        Project::factory()->count(2)->state(fn () => ['slug' => self::uniqueSlug()])->create(); // drafts

        Story::factory()
            ->published()
            ->count(5)
            ->state(fn () => ['slug' => self::uniqueSlug(), 'project_id' => $projects->random()->id])
            ->create();
        Story::factory()->count(2)->state(fn () => ['slug' => self::uniqueSlug()])->create(); // drafts

        TeamMember::factory()
            ->count(6)
            ->state(fn () => ['category_id' => $teamCategories->random()->id])
            ->create();
        TeamMember::factory()->create(['is_active' => false, 'name' => 'Hidden Test Member']);

        NewsPost::factory()
            ->published()
            ->count(8)
            ->state(fn () => ['slug' => self::uniqueSlug(), 'category_id' => $newsCategories->random()->id])
            ->create();
        NewsPost::factory()->count(2)->state(fn () => ['slug' => self::uniqueSlug()])->create(); // drafts

        $galleries = Gallery::factory()->count(3)->state(fn () => ['slug' => self::uniqueSlug()])->create();

        foreach ($galleries as $gallery) {
            $gallery->items()->create(['type' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'caption' => fake()->sentence(3), 'sort_order' => 0]);
            $gallery->items()->createMany(
                collect(range(1, 3))->map(fn (int $i) => ['type' => 'image', 'caption' => fake()->sentence(3), 'sort_order' => $i])->all()
            );
        }

        Banner::factory()->create(['type' => 'homepage', 'title' => 'Test Homepage Banner']);
        Banner::factory()->create(['type' => 'page', 'title' => 'Test Page Banner']);
        Banner::factory()->create(['type' => 'cta', 'title' => 'Test CTA Banner']);
        Banner::factory()->create(['type' => 'popup', 'title' => 'Test Popup Banner']);

        ContactMessage::factory()->count(5)->create();
        ContactMessage::factory()->count(2)->create(['is_read' => true]);

        Certificate::factory()
            ->count(6)
            ->state(fn () => ['project_id' => $projects->random()->id])
            ->create();
        Certificate::factory()->revoked()->count(2)->create();

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

        $courses = Course::factory()->count(3)->create();
        Course::factory()->inactive()->create(['course_name' => 'Discontinued Test Course']);

        $students = Student::factory()->count(12)->create();

        Enrollment::factory()
            ->count(4)
            ->state(fn () => ['student_id' => $students->random()->id, 'course_id' => $courses->random()->id])
            ->create(); // pending

        Enrollment::factory()
            ->passed()
            ->count(3)
            ->state(fn () => ['student_id' => $students->random()->id, 'course_id' => $courses->random()->id])
            ->create(); // passed, certificate not yet issued

        Enrollment::factory()
            ->failed()
            ->count(2)
            ->state(fn () => ['student_id' => $students->random()->id, 'course_id' => $courses->random()->id])
            ->create();

        Enrollment::factory()
            ->certificateIssued()
            ->count(5)
            ->state(fn () => ['student_id' => $students->random()->id, 'course_id' => $courses->random()->id])
            ->create();

        Enrollment::factory()
            ->certificateRevoked()
            ->count(1)
            ->state(fn () => ['student_id' => $students->random()->id, 'course_id' => $courses->random()->id])
            ->create();

        // FeaturedVisitor rows alone are invisible on the public site - unlike
        // VisitorBookEntry (which auto-shows on its Project's page and on
        // /visitor-book with no Section needed), a "Featured Visitors" Section
        // must be attached to some Page before the frontend renders anything.
        $this->attachFeaturedVisitorsSection();

        // The Service layer's caches are only busted via each Service's own
        // write methods - this seeder writes through Eloquent factories
        // directly, so every cached frontend read needs clearing by hand
        // (same reasoning as RsufDemoSeeder).
        app(ProjectService::class)->forget();
        app(StoryService::class)->forget();
        app(TeamService::class)->forget();
        app(NewsService::class)->forget();
        app(GalleryService::class)->forget();
        app(BannerService::class)->forget();
        app(FeaturedVisitorService::class)->forget();
        app(PageService::class)->forget();

        $this->command?->info('Test data seeded across all modules.');
    }

    /**
     * Attach a "Featured Visitors" section to a real page - preferring the
     * "about" page (the real client site's own home for this content, per
     * the rsufbd.com About Us page this feature was modeled on), falling
     * back to any published page, or creating one if the database is
     * otherwise empty. Idempotent: won't add a second section on a re-run.
     */
    private function attachFeaturedVisitorsSection(): void
    {
        $page = Page::where('slug', 'about')->first()
            ?? Page::where('status', 'published')->first()
            ?? Page::factory()->create(['slug' => 'test-visitors-page', 'title' => 'Test Visitors Page', 'status' => 'published']);

        if ($page->sections()->where('type', 'featured_visitors')->exists()) {
            return;
        }

        $page->sections()->create([
            'type' => 'featured_visitors',
            'heading' => 'Our Visitors',
            'subheading' => 'Test Data',
            'sort_order' => ($page->sections()->max('sort_order') ?? -1) + 1,
        ]);
    }

    /**
     * A slug guaranteed unique across separate seeder runs, unlike
     * fake()->unique()->slug() (which only tracks uniqueness within the
     * current process and will collide with a prior run's rows).
     */
    private static function uniqueSlug(): string
    {
        return Str::slug(fake()->words(3, true)).'-'.Str::random(6);
    }
}
