<x-frontend-layout
    :title="$cmsPage['seo']['title'] ?? __('Gallery')"
    :description="$cmsPage['seo']['description'] ?? null"
    :canonical="$cmsPage['seo']['canonical_url'] ?? null"
    :robots="$cmsPage['seo']['robots'] ?? null"
>
    @php
        // Same rule as the generic Page template: a Hero section already
        // carries its own big title, so the page-header banner skips its
        // title in that case to avoid showing it twice.
        $startsWithHero = ($cmsPage['sections'][0]['type'] ?? null) === 'hero';
    @endphp

    <x-banner
        type="page"
        :override="['title' => $cmsPage['banner_eyebrow'] ?? null, 'image_url' => $cmsPage['banner_image_url'] ?? null]"
        :page-title="$startsWithHero ? null : ($cmsPage['title'] ?? __('Gallery'))"
    />

    <x-sections :sections="$cmsPage['sections'] ?? []" />

    <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        @if (isset($items))
            @if (empty($items))
                <p class="mt-6 text-center text-gray-500">{{ __('No photos yet.') }}</p>
            @else
                <div class="mt-10">
                    <x-frontend.lightbox-gallery :items="$items" :show-captions="setting('gallery_show_captions', true)" />
                </div>

                <x-frontend.pagination :paginator="$paginator" />
            @endif
        @elseif (empty($albums))
            <p class="mt-6 text-center text-gray-500">{{ __('No albums yet.') }}</p>
        @else
            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($albums as $album)
                    <x-frontend.album-card :album="$album" />
                @endforeach
            </div>
        @endif
    </div>
</x-frontend-layout>
