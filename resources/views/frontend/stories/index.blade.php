<x-frontend-layout
    :title="$cmsPage['seo']['title'] ?? __('Success Stories')"
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
        :page-title="$startsWithHero ? null : ($cmsPage['title'] ?? __('Success Stories'))"
    />

    <x-sections :sections="$cmsPage['sections'] ?? []" />

    <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        @if ($projects->isNotEmpty())
            <div class="flex flex-wrap justify-center gap-2">
                <a
                    href="{{ route('stories.index') }}"
                    class="rounded-full px-3 py-1 text-sm font-medium {{ request('project') ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-brand-500 text-white' }}"
                >
                    {{ __('All') }}
                </a>

                @foreach ($projects as $project)
                    <a
                        href="{{ route('stories.index', ['project' => $project['slug']]) }}"
                        class="rounded-full px-3 py-1 text-sm font-medium {{ request('project') === $project['slug'] ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                    >
                        {{ $project['title'] }}
                    </a>
                @endforeach
            </div>
        @endif

        @if (empty($stories))
            <p class="mt-10 text-center text-gray-500">{{ __('No success stories yet.') }}</p>
        @else
            <div class="mt-10 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($stories as $story)
                    <x-frontend.story-card :story="$story" />
                @endforeach
            </div>
        @endif
    </div>
</x-frontend-layout>
