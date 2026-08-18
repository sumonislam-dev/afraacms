<?php

namespace Database\Seeders;

use App\CMS\Services\BannerService;
use App\CMS\Services\GalleryService;
use App\CMS\Services\MenuService;
use App\CMS\Services\PageService;
use App\CMS\Services\ProjectService;
use App\CMS\Services\SettingService;
use App\CMS\Services\StoryService;
use App\CMS\Services\TeamService;
use App\Models\Banner;
use App\Models\Gallery;
use App\Models\MediaItem;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Story;
use App\Models\TeamCategory;
use App\Models\TeamMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Seeds real content for the RSUF client demo (rsuf_frontend/), replacing
 * PagesSeeder's bare placeholder pages with the actual site structure:
 * Home (hero + sections), About (anchor sub-sections), Get Involved, News,
 * Contact, a Gallery album, six Projects, and the header/footer menus.
 *
 * Run standalone once, whenever the demo content needs (re)seeding:
 *   php artisan db:seed --class=RsufDemoSeeder
 *
 * Safe to re-run: media/pages/menus/galleries/projects are all matched by
 * a stable key (title or slug) and updated in place rather than duplicated.
 */
class RsufDemoSeeder extends Seeder
{
    /** @var array<string, MediaItem> */
    private array $media = [];

    private ?Gallery $fieldWorkGallery = null;

    private ?Gallery $heroGallery = null;

    private ?Gallery $scholarshipGallery = null;

    private ?Gallery $workshopGallery = null;

    private ?Page $homePage = null;

    private ?TeamCategory $executiveCommittee = null;

    public function run(): void
    {
        $this->call(SettingsSeeder::class);

        $this->importMedia();
        $this->seedSettings();
        $this->seedBanners();
        $this->seedGalleries();
        $this->seedProjects();
        $this->seedStories();
        $this->seedTeam();
        $this->seedMenus();
        $this->seedHomePage();
        $this->seedAboutPage();
        $this->seedGetInvolvedPage();
        $this->seedNewsPage();
        $this->seedContactPage();
        $this->removePlaceholderPages();
        $this->hideUnrelatedDemoContent();

        Setting::where('key', 'homepage_page_id')->update(['value' => $this->homePage->id]);

        // The Service layer's caches are only busted via each Service's own
        // write methods - this seeder writes through Eloquent directly, so
        // every cached frontend read needs clearing by hand.
        app(PageService::class)->forget();
        app(MenuService::class)->forget();
        app(GalleryService::class)->forget();
        app(ProjectService::class)->forget();
        app(BannerService::class)->forget();
        app(SettingService::class)->forget();
        app(TeamService::class)->forget();
        app(StoryService::class)->forget();

        $this->command?->info('RSUF demo content seeded.');
    }

    /**
     * Page::updateOrCreate() would miss a page that was soft-deleted (e.g.
     * trashed from the admin panel) and try to insert a duplicate slug,
     * violating the unique constraint - so look it up including trashed
     * rows and un-trash it in the same save.
     */
    private function upsertPage(array $attributes, array $values): Page
    {
        $page = Page::withTrashed()->firstOrNew($attributes);
        $page->fill($values);
        $page->deleted_at = null;
        $page->save();

        return $page;
    }

    /**
     * Same reasoning as upsertPage(): Project also uses SoftDeletes.
     */
    private function upsertProject(array $attributes, array $values): Project
    {
        $project = Project::withTrashed()->firstOrNew($attributes);
        $project->fill($values);
        $project->deleted_at = null;
        $project->save();

        return $project;
    }

    /**
     * Same reasoning as upsertPage(): TeamMember also uses SoftDeletes.
     */
    private function upsertTeamMember(array $attributes, array $values): TeamMember
    {
        $member = TeamMember::withTrashed()->firstOrNew($attributes);
        $member->fill($values);
        $member->deleted_at = null;
        $member->save();

        return $member;
    }

    /**
     * Same reasoning as upsertPage(): Story also uses SoftDeletes.
     */
    private function upsertStory(array $attributes, array $values): Story
    {
        $story = Story::withTrashed()->firstOrNew($attributes);
        $story->fill($values);
        $story->deleted_at = null;
        $story->save();

        return $story;
    }

    /**
     * Import rsuf_frontend/images/* into the Media Library, keyed by a
     * short handle used throughout this seeder.
     */
    private function importMedia(): void
    {
        $files = [
            'logo' => 'rsuf_ogo.jpg',
            'campus' => 'campus.jpg',
            'hospital' => 'hospital.jpg',
            'old_care' => 'old.jpg',
            'relief' => 'relief.jpg',
            'safe_water' => 'safe-drinking-water.jpg',
            'scholarship' => 'schlorship-rogram.JPG',
            'slider_1' => 'slider-1680411159.jpg',
            'slider_2' => 'slider-1685853822.jpg',
            'slider_3' => 'slider-1685857545.jpg',
            'slider_4' => 'slider-1691034006.jpeg',
            'slider_5' => 'slider-1716965073.jpg',
        ];

        $basePath = base_path('rsuf_frontend/images');

        foreach ($files as $key => $filename) {
            $path = $basePath.'/'.$filename;

            if (! is_file($path)) {
                $this->command?->warn("RsufDemoSeeder: skipping missing image {$filename}");

                continue;
            }

            $title = Str::headline(pathinfo($filename, PATHINFO_FILENAME));
            $item = MediaItem::firstOrCreate(['title' => $title]);

            if (! $item->getFirstMedia('file')) {
                $image = ImageManager::usingDriver(Driver::class)->decodePath($path);

                $item->addMedia($path)
                    ->preservingOriginal()
                    ->withCustomProperties(['width' => $image->width(), 'height' => $image->height()])
                    ->toMediaCollection('file');
            }

            $this->media[$key] = $item;
        }
    }

    private function seedSettings(): void
    {
        $values = [
            'site_name' => 'RSUF',
            'tagline' => 'Rahmantunnessa Shikkha Unnayan Foundation',
            'logo' => $this->media['logo']->id ?? null,
            'contact_email' => 'rsufbd@gmail.com',
            'contact_phone' => '+880 1786-360453',
            'contact_address' => 'Village: Paturia, Post Office: Gee-Comla, Upazilla: Kalukhali, Dist.: Rajbari, Bangladesh',
            'facebook' => 'https://www.facebook.com/rsuf.rsuf.94',
            'copyright' => '© '.date('Y').' RSUF. All rights reserved.',
            'gallery_display_mode' => 'flat',
        ];

        foreach ($values as $key => $value) {
            if ($value !== null) {
                Setting::where('key', $key)->update(['value' => $value]);
            }
        }
    }

    /**
     * A fallback "page" banner background for routes with no per-page
     * override (the dedicated Gallery/Projects index pages).
     */
    private function seedBanners(): void
    {
        Banner::updateOrCreate(
            ['type' => 'page'],
            ['image' => $this->media['slider_2']->id ?? null, 'is_active' => true, 'sort_order' => 0]
        );
    }

    private function seedGalleries(): void
    {
        $this->fieldWorkGallery = Gallery::updateOrCreate(
            ['slug' => 'field-work'],
            [
                'title' => 'Field Work',
                'description' => "Moments from RSUF's work across Bangladesh.",
                'cover_image' => $this->media['slider_1']->id ?? null,
                'is_active' => true,
                'sort_order' => 0,
            ]
        );
        $this->fieldWorkGallery->items()->delete();

        $this->heroGallery = Gallery::updateOrCreate(
            ['slug' => 'homepage-hero'],
            ['title' => 'Homepage Hero Slides', 'is_active' => true, 'is_public' => false, 'sort_order' => 1]
        );
        $this->heroGallery->items()->delete();

        $fieldWorkPhotos = ['slider_1', 'slider_2', 'slider_3', 'slider_4', 'slider_5', 'campus', 'hospital', 'old_care'];

        foreach ($fieldWorkPhotos as $i => $key) {
            if (! isset($this->media[$key])) {
                continue;
            }

            $this->fieldWorkGallery->items()->create([
                'type' => 'image', 'image' => $this->media[$key]->id, 'sort_order' => $i,
            ]);
        }

        $heroPhotos = ['slider_4', 'slider_3', 'slider_2', 'slider_1'];

        foreach ($heroPhotos as $i => $key) {
            if (! isset($this->media[$key])) {
                continue;
            }

            $this->heroGallery->items()->create([
                'type' => 'image', 'image' => $this->media[$key]->id, 'sort_order' => $i,
            ]);
        }

        // The live site's two homepage carousels ("Small Project Scholarship
        // Program", "RSUF Family Development Workshop") each showed 10+ distinct
        // photos we don't have locally - these reuse the same imported demo
        // images as a stand-in until the client supplies the real photo sets.
        $this->scholarshipGallery = Gallery::updateOrCreate(
            ['slug' => 'small-project-scholarship-program'],
            ['title' => 'Small Project Scholarship Program', 'is_active' => true, 'is_public' => false, 'sort_order' => 2]
        );
        $this->scholarshipGallery->items()->delete();

        $this->workshopGallery = Gallery::updateOrCreate(
            ['slug' => 'family-development-workshop'],
            ['title' => 'RSUF Family Development Workshop', 'is_active' => true, 'is_public' => false, 'sort_order' => 3]
        );
        $this->workshopGallery->items()->delete();

        $scholarshipPhotos = ['scholarship', 'campus', 'slider_2', 'slider_3'];

        foreach ($scholarshipPhotos as $i => $key) {
            if (! isset($this->media[$key])) {
                continue;
            }

            $this->scholarshipGallery->items()->create([
                'type' => 'image', 'image' => $this->media[$key]->id, 'sort_order' => $i,
            ]);
        }

        $workshopPhotos = ['old_care', 'relief', 'hospital', 'safe_water'];

        foreach ($workshopPhotos as $i => $key) {
            if (! isset($this->media[$key])) {
                continue;
            }

            $this->workshopGallery->items()->create([
                'type' => 'image', 'image' => $this->media[$key]->id, 'sort_order' => $i,
            ]);
        }
    }

    private function seedProjects(): void
    {
        $projects = [
            ['title' => 'Rahmatunnesa Skill Training Project (RSTP)', 'excerpt' => 'Vocational and skills-based training that helps young people become self-reliant income earners.', 'image' => 'campus'],
            ['title' => 'RSUF Scholarship Program', 'excerpt' => 'Financial support and scholarships that keep promising students from low-income families in school.', 'image' => 'scholarship'],
            ['title' => 'Late Mazad Mollah Eye Hospital', 'excerpt' => 'Accessible eye care and treatment for communities that would otherwise go without.', 'image' => 'hospital'],
            ['title' => 'Safe Drinking Water Project', 'excerpt' => 'Clean, safe drinking water infrastructure for households that lack reliable access.', 'image' => 'safe_water'],
            ['title' => 'RSUF Old Care Project', 'excerpt' => 'Dedicated care and support services for elderly members of the community.', 'image' => 'old_care'],
            ['title' => 'RSUF Emergency Relief Activities', 'excerpt' => 'Rapid relief distribution to families affected by disasters and emergencies.', 'image' => 'relief'],
        ];

        foreach ($projects as $i => $p) {
            $this->upsertProject(
                ['slug' => Str::slug($p['title'])],
                [
                    'title' => $p['title'],
                    'excerpt' => $p['excerpt'],
                    'content' => $p['excerpt'],
                    'cover_image' => $this->media[$p['image']]->id ?? null,
                    'status' => 'published',
                    'is_featured' => $i === 0,
                ]
            );
        }
    }

    /**
     * Real success stories from the live site, migrated from the About
     * page's old hardcoded "Success" text into their own dedicated Story
     * records, linked to the training project they both came from.
     */
    private function seedStories(): void
    {
        $trainingProject = Project::where('slug', 'rahmatunnesa-skill-training-project-rstp')->first();

        $stories = [
            [
                'slug' => 'from-daily-laborer-to-technician',
                'title' => 'From Daily Laborer to Technician — Mohammad Sabbir Hossain',
                'excerpt' => 'A day laborer from a poor farming family completes RSUF\'s residential technical course and secures a stable job.',
                'content' => "<p>Sabbir came from a poor farming family in a backward village. After completing his SSC, he could not continue his education due to financial constraints, and worked as a day laborer in agricultural fields alongside his father. Despite dreaming of stable employment and social dignity, he saw no path forward — until a friend told him about RSUF's technical training program in 2019.</p><p>Sabbir enrolled in January 2020 and completed a 2-year residential course covering:</p><ul><li>Electrical Installation and House Wiring</li><li>Computer Operation</li><li>Solar Systems</li></ul><p>\"The teachers' support helped me gradually master all subjects despite lacking a technical background,\" he says. The program also included a month each of rural electricity and industrial training.</p><p>On completion, Sabbir received a free certificate and toolbox, and secured employment at Super Star Limited, a renowned Bangladeshi company — later earning a promotion. He now supports his family financially, and his success has inspired other villagers to pursue similar training.</p>",
                'days_ago' => 400,
            ],
            [
                'slug' => 'from-neglected-girl-to-independent-woman',
                'title' => 'From Neglected Girl to Independent Woman — Fatema',
                'excerpt' => 'Rejecting early marriage, Fatema completes RSUF\'s electrical training course and becomes financially independent.',
                'content' => '<p>Fatema, from Ojparagaon Paturiya village, rejected the typical path of early marriage. Lacking access to education due to poverty, she felt trapped — until she discovered RSUF in 2019.</p><p>She completed the same 2-year residential electrical course. "The teaching system was excellent — I understood all subjects easily despite having no prior technical knowledge," she says.</p><p>After receiving her free certificate and toolbox, Fatema secured employment and later a promotion, and now earns an income to support her family. She has since recommended fellow trainees for positions at her company, and continues working alongside her RSUF classmates.</p><p>Her success has motivated other girls in her village to pursue vocational training instead of accepting early marriage — transforming educational prospects in the area.</p>',
                'days_ago' => 380,
            ],
        ];

        foreach ($stories as $i => $s) {
            $this->upsertStory(
                ['slug' => $s['slug']],
                [
                    'project_id' => $trainingProject->id ?? null,
                    'title' => $s['title'],
                    'excerpt' => $s['excerpt'],
                    'content' => $s['content'],
                    'published_at' => now()->subDays($s['days_ago']),
                    'is_featured' => $i === 0,
                    'status' => 'published',
                ]
            );
        }
    }

    /**
     * The site's own About Us page names 5 of its 9 Executive Committee
     * seats; the remaining 4 aren't disclosed anywhere public, so only the
     * named 5 are seeded here rather than inventing placeholder names.
     */
    private function seedTeam(): void
    {
        $this->executiveCommittee = TeamCategory::firstOrCreate(
            ['slug' => 'executive-committee'],
            ['name' => 'Executive Committee']
        );

        $members = [
            ['name' => 'Md. Jahidul Islam', 'role' => 'Founder & Chairman / President', 'bio' => 'Contact: +88 01708515958, rsufbd@gmail.com', 'sort_order' => 0],
            ['name' => 'Md. Sohidul Islam', 'role' => 'Director', 'bio' => 'Contact: +88 01711231830', 'sort_order' => 1],
            ['name' => 'Md. Abdullah Ibrahim', 'role' => 'Vice-President', 'bio' => null, 'sort_order' => 2],
            ['name' => 'Rokeya Razzak', 'role' => 'General Secretary', 'bio' => null, 'sort_order' => 3],
            ['name' => 'M A Bari', 'role' => 'Treasurer', 'bio' => null, 'sort_order' => 4],
        ];

        foreach ($members as $m) {
            $this->upsertTeamMember(
                ['name' => $m['name'], 'category_id' => $this->executiveCommittee->id],
                [
                    'role' => $m['role'],
                    'bio' => $m['bio'],
                    'is_active' => true,
                    'sort_order' => $m['sort_order'],
                ]
            );
        }
    }

    private function seedMenus(): void
    {
        $header = Menu::firstOrCreate(['slug' => 'header'], ['name' => 'Header Menu']);
        $header->items()->delete();

        $header->items()->create(['label' => 'Home', 'type' => 'internal', 'url' => '/', 'is_active' => true, 'sort_order' => 0]);
        $header->items()->create(['label' => 'Get Involved', 'type' => 'internal', 'url' => '/get-involved', 'is_active' => true, 'sort_order' => 1]);
        $header->items()->create(['label' => 'News', 'type' => 'internal', 'url' => '/news', 'is_active' => true, 'sort_order' => 2]);
        $header->items()->create(['label' => 'Gallery', 'type' => 'internal', 'url' => '/gallery', 'is_active' => true, 'sort_order' => 3]);

        $about = $header->items()->create(['label' => 'About', 'type' => 'internal', 'url' => '/about', 'is_active' => true, 'sort_order' => 4]);

        $aboutChildren = [
            ['label' => 'About Us', 'url' => '/about'],
            ['label' => 'History', 'url' => '/about#history'],
            ['label' => 'Registration', 'url' => '/about#registration'],
            ['label' => 'Vision & Mission', 'url' => '/about#vision-mission'],
            ['label' => 'Areas of Operation', 'url' => '/about#areas'],
            ['label' => 'What We Do', 'url' => '/about#what-we-do'],
            ['label' => 'Success', 'url' => '/about#success'],
        ];

        foreach ($aboutChildren as $i => $child) {
            $header->items()->create([
                'label' => $child['label'], 'type' => 'internal', 'url' => $child['url'],
                'parent_id' => $about->id, 'is_active' => true, 'sort_order' => $i,
            ]);
        }

        $header->items()->create(['label' => 'Contact Us', 'type' => 'internal', 'url' => '/contact', 'is_active' => true, 'sort_order' => 5]);

        $footer = Menu::firstOrCreate(['slug' => 'footer'], ['name' => 'Footer Menu']);
        $footer->items()->delete();

        $footerLinks = [
            ['label' => 'About Us', 'url' => '/about'],
            ['label' => 'Get Involved', 'url' => '/get-involved'],
            ['label' => 'Photo Gallery', 'url' => '/gallery'],
            ['label' => 'News', 'url' => '/news'],
            ['label' => 'Contact Us', 'url' => '/contact'],
        ];

        foreach ($footerLinks as $i => $link) {
            $footer->items()->create(['label' => $link['label'], 'type' => 'internal', 'url' => $link['url'], 'is_active' => true, 'sort_order' => $i]);
        }
    }

    private function seedHomePage(): void
    {
        $page = $this->upsertPage(
            ['slug' => 'home'],
            ['title' => 'Home', 'status' => 'published', 'template' => 'default']
        );
        $page->sections()->delete();

        $hero = $page->sections()->create([
            'type' => 'hero', 'sort_order' => 0,
            'heading' => 'Building a Poverty-Free, Educated & Peaceful Society',
            'subheading' => 'Rahmantunnessa Shikkha Unnayan Foundation',
            'body' => 'A non-government, non-political, non-profit organization working with the poorest of the poor across Bangladesh — toward self-reliance, dignity and equal opportunity for all.',
            'button_text' => 'Get Involved',
            'button_url' => '/get-involved',
        ]);

        if ($this->heroGallery) {
            $hero->galleries()->sync([$this->heroGallery->id]);
        }

        $scholarshipSlider = $page->sections()->create([
            'type' => 'photo_slider', 'sort_order' => 1,
            'subheading' => 'Scholarships',
            'heading' => 'Small Project Scholarship Program',
        ]);

        if ($this->scholarshipGallery) {
            $scholarshipSlider->galleries()->sync([$this->scholarshipGallery->id]);
        }

        $workshopSlider = $page->sections()->create([
            'type' => 'photo_slider', 'sort_order' => 2,
            'subheading' => 'Community Development',
            'heading' => 'RSUF Family Development Workshop',
        ]);

        if ($this->workshopGallery) {
            $workshopSlider->galleries()->sync([$this->workshopGallery->id]);
        }

        $whatWeDo = $page->sections()->create([
            'type' => 'cards', 'sort_order' => 3,
            'subheading' => 'What We Do',
            'heading' => 'Focus Areas That Drive Our Work',
        ]);

        $whatWeDo->items()->createMany([
            ['title' => 'Education & Skills Training', 'body' => 'Scholarships and vocational training that equip young people with the skills to build independent livelihoods.', 'icon' => 'document-text', 'sort_order' => 0],
            ['title' => 'Healthcare Access', 'body' => 'Affordable eye care and safe drinking water programs that protect the health of underserved communities.', 'icon' => 'check-circle', 'sort_order' => 1],
            ['title' => 'Relief & Elderly Care', 'body' => 'Emergency relief distribution and dedicated old-age care for the most vulnerable members of society.', 'icon' => 'users', 'sort_order' => 2],
        ]);

        $page->sections()->create([
            'type' => 'image_text', 'sort_order' => 4,
            'subheading' => 'Welcome to RSUF',
            'heading' => 'Socio-Economic Development Among the Poorest of the Poor',
            'body' => "Rahmantunnessa Shikkha Unnayan Foundation (RSUF) is a Bangladeshi organization engaged in socio-economic development activities among the poorest of the poor. It is a non-government, non-political and non-profitable organization working with the poor of all levels irrespective of caste or creed.\n\nThe main aim of RSUF is to bring about self-reliance of the people through participation of grass-root communities in every development effort. We remain grateful to our well-wishers, partners, donors and the Government for their continuous support of our initiatives.",
            'image' => $this->media['campus']->id ?? null,
            'layout' => 'image-left',
        ]);

        $page->sections()->create([
            'type' => 'projects', 'sort_order' => 5,
            'subheading' => 'Our Projects',
            'heading' => 'Where Your Support Makes a Difference',
            'button_text' => 'View All Projects',
        ]);

        $galleryPreview = $page->sections()->create([
            'type' => 'gallery_albums', 'sort_order' => 6,
            'subheading' => 'Gallery',
            'heading' => 'Moments From the Field',
            'button_text' => 'View Full Gallery',
        ]);

        if ($this->fieldWorkGallery) {
            $galleryPreview->galleries()->sync([$this->fieldWorkGallery->id]);
        }

        $page->sections()->create([
            'type' => 'contact', 'sort_order' => 7,
            'subheading' => 'Be a Volunteer',
            'heading' => 'Doing Nothing Is Not an Option',
        ]);

        $this->homePage = $page;
    }

    private function seedAboutPage(): void
    {
        $page = $this->upsertPage(
            ['slug' => 'about'],
            [
                'title' => 'About Us', 'status' => 'published', 'template' => 'default',
                'banner_eyebrow' => 'About RSUF',
                'banner_image' => $this->media['campus']->id ?? null,
            ]
        );
        $page->sections()->delete();

        $sections = [
            [null, 'About Us', '<p>Founded on 10 January 2002, Rahmantunnessa Shikkha Unnayan Foundation (RSUF) is a Bangladeshi organization engaged in socio-economic development activities among the poorest of the poor. It is a non-government, non-political and non-profitable organization working with the poor of all levels irrespective of caste or creed.</p><p>RSUF is a people-based learning organization — participation of grass-root communities in every development effort is one of our key principles.</p>'],
            ['history', 'History', 'Full history details coming soon.'],
            ['registration', 'Registration', "<p>Rahmantunnessa Shikkha Unnayan Foundation (RSUF) is registered with the Government of the People's Republic of Bangladesh under the following departments:</p><ul><li><strong>Department of Social Welfare</strong> — under the Joint Stock Companies and Firms, Bangladesh Registration and Control Ordinance No. (XXI) of 1860. Registration No: S-5460(574)/06, dated 23 February 2006.</li><li><strong>NGO Affairs Bureau</strong> — Registration No: 3142/18.</li><li><strong>National Skill Development Authority (NSDA)</strong> — Registration No: STP-RAJ-000375, dated 2022.</li></ul>"],
            ['vision-mission', 'Vision & Mission', "Vision: A poverty free, educated and peaceful society.\n\nMission: Reducing poverty, establishing equal opportunities, promoting peace and justice, and ensuring quality education, agriculture support, hygiene, sanitation, skills development and income generation for marginalized communities — grounded in human dignity, ethical values, participation, teamwork, mutual respect and inter-religious harmony."],
            ['areas', 'Areas of Operation', 'Details on our areas of operation coming soon.'],
            ['what-we-do', 'What We Do', 'Education & skills training, healthcare access, safe drinking water, elderly care and emergency relief — see our Projects on the homepage.'],
            ['success', 'Success', '<p>Real stories of transformation from the people RSUF has trained and supported.</p>'],
        ];

        foreach ($sections as $i => [$anchor, $heading, $body]) {
            $page->sections()->create([
                'type' => 'rich_text', 'sort_order' => $i,
                'anchor' => $anchor, 'heading' => $heading, 'body' => $body,
            ]);
        }

        $page->sections()->create([
            'type' => 'stories', 'sort_order' => count($sections),
            'subheading' => 'Success Stories',
            'heading' => 'Lives Changed Through RSUF',
        ]);

        $leadership = $page->sections()->create([
            'type' => 'team_members', 'sort_order' => count($sections) + 1,
            'anchor' => 'leadership',
            'subheading' => 'Leadership',
            'heading' => 'Our Executive Committee',
        ]);

        if ($this->executiveCommittee) {
            $leadership->teamCategories()->sync([$this->executiveCommittee->id]);
        }
    }

    private function seedGetInvolvedPage(): void
    {
        $page = $this->upsertPage(
            ['slug' => 'get-involved'],
            [
                'title' => 'Get Involved', 'status' => 'published', 'template' => 'default',
                'banner_eyebrow' => 'Join Us',
                'banner_image' => $this->media['relief']->id ?? null,
            ]
        );
        $page->sections()->delete();

        $ways = $page->sections()->create([
            'type' => 'cards', 'sort_order' => 0,
            'subheading' => 'Get Involved',
            'heading' => 'Ways to Support RSUF',
        ]);

        $ways->items()->createMany([
            ['title' => 'Membership', 'body' => 'RSUF welcomes all adults with good social reputation and character who support our core principles and philosophy.', 'icon' => 'user-circle', 'sort_order' => 0],
            ['title' => 'Volunteers', 'body' => 'We welcome unpaid volunteers, both domestic and international, to exchange experience and assistance on development and cultural issues.', 'icon' => 'users', 'sort_order' => 1],
            ['title' => 'Collaboration', 'body' => 'We pursue joint ventures with other NGOs and Government institutions, and engage consultants. RSUF holds membership with the National Skill Development Authority (NSDA).', 'icon' => 'link', 'sort_order' => 2],
            ['title' => 'Partners & Donors', 'body' => 'Shanti (Switzerland), SHETU (Germany), and the Swiss Agency for Development Cooperation (SDC) support our work.', 'icon' => 'globe-alt', 'sort_order' => 3],
        ]);

        $page->sections()->create([
            'type' => 'cta', 'sort_order' => 1,
            'subheading' => "Whether you can give your time, your skills or your support, RSUF welcomes anyone who wants to help build self-reliant communities across Bangladesh. Reach out to us and we'll help you find the right way to contribute.",
            'button_text' => 'Contact Us',
            'button_url' => '/contact',
        ]);
    }

    private function seedNewsPage(): void
    {
        $page = $this->upsertPage(
            ['slug' => 'news'],
            [
                'title' => 'News & Visitors', 'status' => 'published', 'template' => 'default',
                'banner_eyebrow' => 'Stay Updated',
                'banner_image' => $this->media['slider_5']->id ?? null,
            ]
        );
        $page->sections()->delete();

        $page->sections()->create([
            'type' => 'rich_text', 'sort_order' => 0,
            'body' => 'Latest news and updates from RSUF will be posted here soon.',
        ]);
    }

    private function seedContactPage(): void
    {
        $page = $this->upsertPage(
            ['slug' => 'contact'],
            [
                'title' => 'Contact Us', 'status' => 'published', 'template' => 'default',
                'banner_eyebrow' => 'Reach Us',
                'banner_image' => $this->media['slider_1']->id ?? null,
            ]
        );
        $page->sections()->delete();

        $page->sections()->create([
            'type' => 'contact', 'sort_order' => 0,
            'heading' => 'Get In Touch',
        ]);
    }

    /**
     * PagesSeeder's "history" and "registration" scaffold pages are
     * superseded by the anchored sections on the About page above.
     */
    private function removePlaceholderPages(): void
    {
        Page::whereIn('slug', ['history', 'registration'])->delete();
    }

    /**
     * Non-destructively hide older, unrelated dev/test records so the
     * public site reflects RSUF's content only: deactivating a Gallery
     * (rather than deleting it) drops it out of the "flat" gallery view,
     * and drafting a Project drops it out of the published listing - both
     * fully reversible from the admin panel.
     */
    private function hideUnrelatedDemoContent(): void
    {
        Gallery::whereNotIn('slug', [
            'field-work', 'homepage-hero', 'small-project-scholarship-program', 'family-development-workshop',
        ])->update(['is_active' => false]);

        Project::whereNotIn('slug', [
            'rahmatunnesa-skill-training-project-rstp',
            'rsuf-scholarship-program',
            'late-mazad-mollah-eye-hospital',
            'safe-drinking-water-project',
            'rsuf-old-care-project',
            'rsuf-emergency-relief-activities',
        ])->update(['status' => 'draft']);
    }
}
