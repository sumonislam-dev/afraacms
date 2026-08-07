<?php

namespace App\Http\Controllers;

use App\CMS\Services\GalleryService;
use App\CMS\Services\NewsService;
use App\CMS\Services\PageService;
use App\CMS\Services\ProjectService;
use App\CMS\Services\StoryService;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __construct(
        private readonly PageService $pages,
        private readonly ProjectService $projects,
        private readonly GalleryService $galleries,
        private readonly NewsService $news,
        private readonly StoryService $stories,
    ) {
    }

    /**
     * Generate the public sitemap.xml, listing the homepage, every
     * published page (other than whichever one currently IS the homepage,
     * to avoid listing its content at two URLs), and - unless disabled on
     * the dedicated SEO screen - every published project and album.
     */
    public function index(): Response
    {
        $homepage = $this->pages->homepage();

        $urls = collect([
            ['loc' => url('/'), 'lastmod' => $homepage['updated_at'] ?? null],
        ]);

        foreach ($this->pages->all() as $page) {
            if ($homepage && $page['slug'] === $homepage['slug']) {
                continue;
            }

            $urls->push(['loc' => url($page['slug']), 'lastmod' => $page['updated_at']]);
        }

        if (setting('sitemap_include_projects', true)) {
            $urls->push(['loc' => route('projects.index'), 'lastmod' => null]);

            foreach ($this->projects->all() as $project) {
                $urls->push(['loc' => route('projects.show', $project['slug']), 'lastmod' => $project['updated_at']]);
            }
        }

        if (setting('sitemap_include_galleries', true)) {
            $urls->push(['loc' => route('gallery.index'), 'lastmod' => null]);

            foreach ($this->galleries->allPublic() as $gallery) {
                $urls->push(['loc' => route('gallery.show', $gallery['slug']), 'lastmod' => $gallery['updated_at']]);
            }
        }

        if (setting('sitemap_include_news', true)) {
            $urls->push(['loc' => route('news.index'), 'lastmod' => null]);

            foreach ($this->news->all() as $post) {
                $urls->push(['loc' => route('news.show', $post['slug']), 'lastmod' => $post['updated_at']]);
            }
        }

        if (setting('sitemap_include_stories', true)) {
            $urls->push(['loc' => route('stories.index'), 'lastmod' => null]);

            foreach ($this->stories->all() as $story) {
                $urls->push(['loc' => route('stories.show', $story['slug']), 'lastmod' => $story['updated_at']]);
            }
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'text/xml');
    }
}
