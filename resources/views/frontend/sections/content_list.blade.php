@php
    $sourceKey = $section['source'] ?? null;
    $meta = $sourceKey ? config("content_sources.{$sourceKey}") : null;
@endphp

@if ($meta)
    @php
        $all = collect(app($meta['service'])->all());

        if ($meta['notices_only'] ?? false) {
            $all = $all->filter(fn ($item) => filled($item['attachment_url'] ?? null))->values();
        }

        $categoryIds = $section['content_category_ids'] ?? [];
        $itemIds = $section['content_item_ids'] ?? [];

        // Only the "news" source has its own site-wide default count
        // (Settings -> News -> "Latest News Count"); every other source
        // falls back to a plain 6, matching each one's old dedicated
        // section type.
        $defaultLimit = $sourceKey === 'news' ? (int) setting('news_section_count', 8) : 6;
        $perPage = max(1, (int) ($section['item_limit'] ?? $defaultLimit));

        $baseFiltered = match (true) {
            ! empty($itemIds) => $all->whereIn('id', $itemIds)->values(),
            ! empty($categoryIds) => $all->whereIn('category_id', $categoryIds)->values(),
            default => $all->values(),
        };

        // Scoped per section instance (not a single global "page"/"q" param)
        // so multiple content_list sections on the same page paginate and
        // search independently without clobbering each other's query string.
        $pageName = 'content_page_'.($section['id'] ?? 'x');
        $qName = 'content_q_'.($section['id'] ?? 'x');
        $searchQuery = trim((string) request($qName, ''));

        $filtered = $searchQuery === '' ? $baseFiltered : $baseFiltered->filter(function ($item) use ($searchQuery) {
            $needle = mb_strtolower($searchQuery);

            foreach (['title', 'excerpt'] as $field) {
                if (str_contains(mb_strtolower((string) ($item[$field] ?? '')), $needle)) {
                    return true;
                }
            }

            return false;
        })->values();

        // A section with nothing configured stays hidden - but once a
        // search is actually in progress, keep it (and its search box)
        // visible even with zero matches, so the user can see "no results"
        // and try again instead of the whole section vanishing.
        $showSection = $baseFiltered->isNotEmpty() || $searchQuery !== '';

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage($pageName);

        $paginator = (new \Illuminate\Pagination\LengthAwarePaginator(
            $filtered->forPage($currentPage, $perPage)->values(),
            $filtered->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(), 'pageName' => $pageName]
        ))->appends(request()->except($pageName));

        $items = collect($paginator->items());

        // Every field here is read defensively (??): not every source's
        // cached item array carries the same optional keys (e.g.
        // annual_reports has no excerpt/content/cover_image_url/slug), and a
        // stale rememberForever() cache from before a source's array shape
        // changed could still be serving the old, narrower shape.
        $isTable = ($section['layout'] ?? 'cards') === 'table';
        $rows = $isTable ? $items->map(fn ($item) => [
            'date' => $item['published_at'] ?? null,
            'title' => $item['title'],
            'description' => ($item['excerpt'] ?? null) ?: str($item['content'] ?? '')->stripTags()->limit(160)->value(),
            'image_url' => $item['cover_image_url'] ?? null,
            'url' => ($item['attachment_url'] ?? null) ?: ($meta['show_route'] ? route($meta['show_route'], $item['slug'] ?? null) : '#'),
            'new_tab' => (bool) ($item['attachment_url'] ?? null),
        ]) : collect();
    @endphp

    @if ($showSection)
        <section class="bg-white py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto mb-16 max-w-2xl text-center">
                    @if ($section['subheading'])
                        <p class="mb-3 text-sm font-semibold uppercase tracking-widest text-brand-600">{{ $section['subheading'] }}</p>
                    @endif

                    @if ($section['heading'])
                        <h2 class="font-display text-3xl font-bold text-ink-900 sm:text-4xl">{{ $section['heading'] }}</h2>
                    @endif
                </div>

                <div id="content-list-{{ $section['id'] }}" data-content-list>
                    @if ($section['show_search'] ?? false)
                        <x-frontend.section-search-box
                            action="{{ url()->current() }}"
                            :params="request()->except([$pageName, $qName])"
                            :placeholder="__('Search :label...', ['label' => $meta['label']])"
                            :name="$qName"
                            :value="$searchQuery"
                            data-content-search
                        />
                    @endif

                    @if ($filtered->isEmpty())
                        <p class="text-center text-gray-500">{{ __('No :label found for ":query".', ['label' => $meta['label'], 'query' => $searchQuery]) }}</p>
                    @elseif ($isTable)
                        <x-frontend.content-table :items="$rows" />
                    @else
                        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($items as $item)
                                @switch($sourceKey)
                                    @case('news')
                                    @case('notices')
                                        <x-frontend.news-card :post="$item" />
                                        @break
                                    @case('stories')
                                        <x-frontend.story-card :story="$item" />
                                        @break
                                    @case('projects')
                                        <x-frontend.project-card :project="$item" />
                                        @break
                                    @case('annual_reports')
                                        <x-frontend.annual-report-card :report="$item" />
                                        @break
                                @endswitch
                            @endforeach
                        </div>
                    @endif

                    <x-frontend.pagination :paginator="$paginator" />
                </div>

                <div class="mt-10 text-center">
                    <a
                        href="{{ $section['button_url'] ?: route($meta['index_route'], $meta['index_route_params'] ?? []) }}"
                        class="inline-block rounded-full border border-brand-500 px-6 py-3 text-sm font-semibold text-brand-600 transition hover:bg-brand-500 hover:text-white"
                    >
                        {{ $section['button_text'] ?: __('View All :label', ['label' => $meta['index_label']]) }}
                    </a>
                </div>
            </div>
        </section>
    @endif
@endif
