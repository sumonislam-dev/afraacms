<x-frontend-layout
    :title="$page['seo']['title'] ?: $page['title']"
    :description="$page['seo']['description'] ?? null"
    :image="$page['seo']['image_url'] ?? null"
    :canonical="$page['seo']['canonical_url'] ?? null"
    :robots="$page['seo']['robots'] ?? null"
    :is-home="$isHome ?? false"
>
    @php
        // A page whose first section is a Hero already gets its own big
        // title treatment there - showing the small page-banner's title too
        // would just duplicate it, so skip the banner's title in that case.
        $startsWithHero = ($page['sections'][0]['type'] ?? null) === 'hero';
    @endphp

    @unless ($isHome ?? false)
        <x-banner
            type="page"
            :override="['title' => $page['banner_eyebrow'] ?? null, 'image_url' => $page['banner_image_url'] ?? null]"
            :page-title="$startsWithHero ? null : $page['title']"
        />
    @endunless

    @if (! empty($page['sections']))
        <x-sections :sections="$page['sections']" />
    @else
        <div class="mx-auto max-w-3xl px-4 py-16 sm:px-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $page['title'] }}</h1>

            @if ($page['content'])
                <div class="mt-6 space-y-4 text-gray-700">
                    {!! nl2br(e($page['content'])) !!}
                </div>
            @endif
        </div>
    @endif
</x-frontend-layout>
