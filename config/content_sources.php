<?php

/*
|--------------------------------------------------------------------------
| Content Sources
|--------------------------------------------------------------------------
|
| The single source of truth for the "content_list" (global) Section type:
| which content pool a section pulls from when its "source" field is set to
| this key. Adding a new source here (plus its pivot tables and Section
| relations, if it supports category/specific picking) is all it takes to
| make a new content pool selectable from that one Section type - no new
| Section type, admin screen, or frontend partial needed.
|
*/

return [
    'news' => [
        'label' => 'News',
        'index_label' => 'News',
        'service' => \App\CMS\Services\NewsService::class,
        'supports_category' => true,
        'category_model' => \App\Models\NewsCategory::class,
        'item_model' => \App\Models\NewsPost::class,
        'categories_relation' => 'newsCategories',
        'items_relation' => 'newsPosts',
        'card_component' => 'frontend.news-card',
        'card_prop' => 'post',
        'index_route' => 'news.index',
        'show_route' => 'news.show',
        'notices_only' => false,
    ],
    'notices' => [
        'label' => 'Notices (News with an attachment)',
        'index_label' => 'Notices',
        'service' => \App\CMS\Services\NewsService::class,
        'supports_category' => true,
        'category_model' => \App\Models\NewsCategory::class,
        'item_model' => \App\Models\NewsPost::class,
        'categories_relation' => 'newsCategories',
        'items_relation' => 'newsPosts',
        'card_component' => 'frontend.news-card',
        'card_prop' => 'post',
        'index_route' => 'news.index',
        'index_route_params' => ['format' => 'notice'],
        'show_route' => 'news.show',
        'notices_only' => true,
    ],
    'stories' => [
        'label' => 'Success Stories',
        'index_label' => 'Stories',
        'service' => \App\CMS\Services\StoryService::class,
        'supports_category' => true,
        'category_model' => \App\Models\StoryCategory::class,
        'item_model' => \App\Models\Story::class,
        'categories_relation' => 'storyCategories',
        'items_relation' => 'storyItems',
        'card_component' => 'frontend.story-card',
        'card_prop' => 'story',
        'index_route' => 'stories.index',
        'show_route' => 'stories.show',
        'notices_only' => false,
    ],
    'annual_reports' => [
        'label' => 'Annual Reports',
        'index_label' => 'Annual Reports',
        'service' => \App\CMS\Services\AnnualReportService::class,
        'supports_category' => false,
        'item_model' => \App\Models\AnnualReport::class,
        'items_relation' => 'annualReportItems',
        'card_component' => 'frontend.annual-report-card',
        'card_prop' => 'report',
        'index_route' => 'annual-reports.index',
        'show_route' => null,
        'notices_only' => false,
    ],
    'projects' => [
        'label' => 'Projects',
        'index_label' => 'Projects',
        'service' => \App\CMS\Services\ProjectService::class,
        'supports_category' => true,
        'category_model' => \App\Models\ProjectCategory::class,
        'item_model' => \App\Models\Project::class,
        'categories_relation' => 'projectCategories',
        'items_relation' => 'projectItems',
        'card_component' => 'frontend.project-card',
        'card_prop' => 'project',
        'index_route' => 'projects.index',
        'show_route' => 'projects.show',
        'notices_only' => false,
    ],
];
