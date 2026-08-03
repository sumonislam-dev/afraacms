<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder
{
    /**
     * Seed the standard inner pages as empty, published, admin-editable
     * scaffolds: a Hero section using the page title, ready to be filled
     * in through the Section Engine. No fabricated body content.
     */
    public function run(): void
    {
        $pages = [
            'About' => 'about',
            'History' => 'history',
            'Registration' => 'registration',
            'Contact' => 'contact',
            'Gallery' => 'gallery',
            'Projects' => 'projects',
        ];

        foreach ($pages as $title => $slug) {
            $page = Page::firstOrCreate(
                ['slug' => $slug],
                ['title' => $title, 'status' => 'published', 'template' => 'default']
            );

            if ($page->sections()->doesntExist()) {
                $page->sections()->create([
                    'type' => 'hero',
                    'heading' => $title,
                    'sort_order' => 0,
                ]);
            }
        }
    }
}
