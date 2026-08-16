<x-frontend-layout
    :title="$cmsPage['seo']['title'] ?? __('Visitor Book')"
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
        :page-title="$startsWithHero ? null : ($cmsPage['title'] ?? __('Visitor Book'))"
    />

    <x-sections :sections="$cmsPage['sections'] ?? []" />

    <div class="mx-auto max-w-3xl px-4 py-20 sm:px-6 lg:px-8">
        @if ($entries->isEmpty())
            <p class="text-center text-gray-500">{{ __('No visitor book entries yet.') }}</p>
        @else
            <div class="space-y-6">
                @foreach ($entries as $entry)
                    <div class="rounded-lg bg-gray-50 p-5">
                        <p class="text-gray-700">{{ $entry->opinion }}</p>
                        <p class="mt-3 text-sm font-medium text-gray-500">
                            {{ $entry->visitor_name }} &middot; {{ $entry->created_at->format('M j, Y') }}
                            @if ($entry->project)
                                &middot; <a href="{{ route('projects.show', $entry->project->slug) }}" class="text-brand-600 hover:text-brand-500">{{ $entry->project->title }}</a>
                            @endif
                        </p>
                    </div>
                @endforeach
            </div>

            <div class="mt-10">
                <x-frontend.pagination :paginator="$entries" />
            </div>
        @endif
    </div>
</x-frontend-layout>
