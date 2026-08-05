<?php

namespace Database\Seeders;

use App\Models\NewsCategory;
use App\Models\NewsPost;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Seed a starter set of news categories and published posts so the
     * News module has real demo content out of the box.
     */
    public function run(): void
    {
        $categories = [
            'Announcements' => 'announcements',
            'Events' => 'events',
            'Press Releases' => 'press-releases',
        ];

        $categoryIds = [];

        foreach ($categories as $name => $slug) {
            $category = NewsCategory::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );

            $categoryIds[$slug] = $category->id;
        }

        $posts = [
            [
                'title' => 'RSUF Launches New Scholarship Round for 2026',
                'slug' => 'rsuf-launches-new-scholarship-round-2026',
                'category' => 'announcements',
                'excerpt' => 'Applications are now open for this year\'s scholarship program, supporting underprivileged students across the region.',
                'content' => "We are pleased to announce that applications for the 2026 scholarship round are now open.\n\nThe program continues our commitment to supporting underprivileged students with access to quality education, covering tuition fees, books, and essential supplies for the academic year.\n\nInterested applicants can visit our Registration page to learn more about eligibility criteria and submit their application before the deadline.",
                'is_featured' => true,
                'days_ago' => 2,
            ],
            [
                'title' => 'Annual General Meeting Scheduled for Next Month',
                'slug' => 'annual-general-meeting-scheduled',
                'category' => 'events',
                'excerpt' => 'Members and stakeholders are invited to attend our upcoming Annual General Meeting to review this year\'s progress.',
                'content' => "Our Annual General Meeting will bring together members, staff, and stakeholders to review the past year's achievements and outline priorities for the year ahead.\n\nThe agenda includes a financial report, program updates, and an open discussion session. Further details on the venue and schedule will be shared soon.",
                'is_featured' => false,
                'days_ago' => 6,
            ],
            [
                'title' => 'New Partnership to Expand Community Health Outreach',
                'slug' => 'new-partnership-community-health-outreach',
                'category' => 'press-releases',
                'excerpt' => 'A new partnership will help extend free health checkups and awareness campaigns to more rural communities.',
                'content' => "We are excited to announce a new partnership aimed at expanding our community health outreach program.\n\nThrough this collaboration, we will be able to reach additional rural communities with free health checkups, maternal health awareness sessions, and basic medical supplies.\n\nThis initiative builds on our long-standing commitment to improving healthcare access for underserved populations.",
                'is_featured' => true,
                'days_ago' => 10,
            ],
            [
                'title' => 'Volunteer Training Workshop Held Successfully',
                'slug' => 'volunteer-training-workshop-held',
                'category' => 'events',
                'excerpt' => 'Over fifty new volunteers completed our latest training workshop, preparing them for upcoming field programs.',
                'content' => "Our latest volunteer training workshop concluded successfully, with over fifty participants completing sessions covering fieldwork safety, community engagement, and reporting procedures.\n\nThese newly trained volunteers will support our upcoming programs across education, health, and disaster relief.",
                'is_featured' => false,
                'days_ago' => 18,
            ],
            [
                'title' => 'Statement on Recent Flood Relief Efforts',
                'slug' => 'statement-on-recent-flood-relief-efforts',
                'category' => 'press-releases',
                'excerpt' => 'An update on our emergency response and distribution of relief supplies to flood-affected families.',
                'content' => "In response to recent flooding in the region, our emergency response teams have been distributing food, clean water, and essential supplies to affected families.\n\nWe remain committed to supporting recovery efforts and are coordinating with local authorities to identify the communities most in need of continued assistance.",
                'is_featured' => false,
                'days_ago' => 25,
            ],
            [
                'title' => 'Office Closed for Public Holiday',
                'slug' => 'office-closed-for-public-holiday',
                'category' => 'announcements',
                'excerpt' => 'Our offices will be closed in observance of the upcoming public holiday. Normal operations resume the following day.',
                'content' => "Please note that our offices will be closed in observance of the upcoming public holiday.\n\nNormal operations, including our helpline and registration services, will resume the following business day. We appreciate your understanding.",
                'is_featured' => false,
                'days_ago' => 30,
            ],
        ];

        foreach ($posts as $post) {
            NewsPost::firstOrCreate(
                ['slug' => $post['slug']],
                [
                    'category_id' => $categoryIds[$post['category']],
                    'title' => $post['title'],
                    'excerpt' => $post['excerpt'],
                    'content' => $post['content'],
                    'published_at' => now()->subDays($post['days_ago']),
                    'is_featured' => $post['is_featured'],
                    'status' => 'published',
                ]
            );
        }
    }
}
