<x-frontend-layout
    :title="$cmsPage['seo']['title'] ?? __('Projects')"
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
        :page-title="$startsWithHero ? null : ($cmsPage['title'] ?? __('Projects'))"
    />

    <x-sections :sections="$cmsPage['sections'] ?? []" />

    <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('projects.index') }}" class="mx-auto mb-8 max-w-md">
            @if (request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="flex gap-2">
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="{{ __('Search projects...') }}"
                    class="block w-full rounded-full border-gray-300 shadow-xs focus:border-brand-500 focus:ring-brand-500"
                >
                <button type="submit" class="shrink-0 rounded-full bg-brand-500 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-600">
                    {{ __('Search') }}
                </button>
            </div>
        </form>

        @if ($categories->isNotEmpty())
            <div class="flex flex-wrap justify-center gap-2">
                <a
                    href="{{ route('projects.index') }}"
                    class="rounded-full px-3 py-1 text-sm font-medium {{ request('category') ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-brand-500 text-white' }}"
                >
                    {{ __('All') }}
                </a>

                @foreach ($categories as $category)
                    <a
                        href="{{ route('projects.index', ['category' => $category->slug]) }}"
                        class="rounded-full px-3 py-1 text-sm font-medium {{ request('category') === $category->slug ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                    >
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        @endif

        @if (empty($projects))
            <p class="mt-10 text-center text-gray-500">{{ __('No projects found.') }}</p>
        @else
            <div class="mt-10 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <x-frontend.project-card :project="$project" />
                @endforeach
            </div>

            <x-frontend.pagination :paginator="$paginator" />
        @endif
    </div>
</x-frontend-layout>
