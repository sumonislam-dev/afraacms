<?php

namespace Database\Seeders;

use App\CMS\Services\BannerService;
use App\CMS\Services\GalleryService;
use App\CMS\Services\MenuService;
use App\CMS\Services\PageService;
use App\CMS\Services\ProjectService;
use App\CMS\Services\SettingService;
use App\Models\Banner;
use App\Models\Gallery;
use App\Models\MediaItem;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Project;
use App\Models\Setting;
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

    private ?Page $homePage = null;

    public function run(): void
    {
        $this->call(SettingsSeeder::class);

        $this->importMedia();
        $this->seedSettings();
        $this->seedBanners();
        $this->seedGalleries();
        $this->seedProjects();
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
            ['title' => 'Homepage Hero Slides', 'is_active' => true, 'sort_order' => 1]
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

        $whatWeDo = $page->sections()->create([
            'type' => 'cards', 'sort_order' => 1,
            'subheading' => 'What We Do',
            'heading' => 'Focus Areas That Drive Our Work',
        ]);

        $whatWeDo->items()->createMany([
            ['title' => 'Education & Skills Training', 'body' => 'Scholarships and vocational training that equip young people with the skills to build independent livelihoods.', 'icon' => 'document-text', 'sort_order' => 0],
            ['title' => 'Healthcare Access', 'body' => 'Affordable eye care and safe drinking water programs that protect the health of underserved communities.', 'icon' => 'check-circle', 'sort_order' => 1],
            ['title' => 'Relief & Elderly Care', 'body' => 'Emergency relief distribution and dedicated old-age care for the most vulnerable members of society.', 'icon' => 'users', 'sort_order' => 2],
        ]);

        $page->sections()->create([
            'type' => 'image_text', 'sort_order' => 2,
            'subheading' => 'Welcome to RSUF',
            'heading' => 'Socio-Economic Development Among the Poorest of the Poor',
            'body' => "Rahmantunnessa Shikkha Unnayan Foundation (RSUF) is a Bangladeshi organization engaged in socio-economic development activities among the poorest of the poor. It is a non-government, non-political and non-profitable organization working with the poor of all levels irrespective of caste or creed.\n\nThe main aim of RSUF is to bring about self-reliance of the people through participation of grass-root communities in every development effort. We remain grateful to our well-wishers, partners, donors and the Government for their continuous support of our initiatives.",
            'image' => $this->media['campus']->id ?? null,
            'layout' => 'image-left',
        ]);

        $page->sections()->create([
            'type' => 'projects', 'sort_order' => 3,
            'subheading' => 'Our Projects',
            'heading' => 'Where Your Support Makes a Difference',
            'button_text' => 'View All Projects',
        ]);

        $galleryPreview = $page->sections()->create([
            'type' => 'gallery_albums', 'sort_order' => 4,
            'subheading' => 'Gallery',
            'heading' => 'Moments From the Field',
            'button_text' => 'View Full Gallery',
        ]);

        if ($this->fieldWorkGallery) {
            $galleryPreview->galleries()->sync([$this->fieldWorkGallery->id]);
        }

        $page->sections()->create([
            'type' => 'contact', 'sort_order' => 5,
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
            [null, 'About Us', 'Rahmantunnessa Shikkha Unnayan Foundation (RSUF) is a Bangladeshi organization engaged in socio-economic development activities among the poorest of the poor. It is a non-government, non-political and non-profitable organization working with the poor of all levels irrespective of caste or creed. RSUF is a people-based learning organization — participation of grass-root communities in every development effort is one of our key principles.'],
            ['history', 'History', 'Full history details coming soon.'],
            ['registration', 'Registration', 'Registration details coming soon.'],
            ['vision-mission', 'Vision & Mission', "Vision: A poverty free, educated and peaceful society.\n\nMission: Reducing poverty, establishing equal opportunities, promoting peace and justice, and ensuring quality education, agriculture support, hygiene, sanitation, skills development and income generation for marginalized communities — grounded in human dignity, ethical values, participation, teamwork, mutual respect and inter-religious harmony."],
            ['areas', 'Areas of Operation', 'Details on our areas of operation coming soon.'],
            ['what-we-do', 'What We Do', 'Education & skills training, healthcare access, safe drinking water, elderly care and emergency relief — see our Projects on the homepage.'],
            ['success', 'Success', 'Success stories coming soon.'],
        ];

        foreach ($sections as $i => [$anchor, $heading, $body]) {
            $page->sections()->create([
                'type' => 'rich_text', 'sort_order' => $i,
                'anchor' => $anchor, 'heading' => $heading, 'body' => $body,
            ]);
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

        $page->sections()->create([
            'type' => 'cta', 'sort_order' => 0,
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
        Gallery::whereNotIn('slug', ['field-work', 'homepage-hero'])->update(['is_active' => false]);

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
