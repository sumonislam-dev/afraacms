<x-frontend-layout
    :title="$project['seo']['title'] ?: $project['title']"
    :description="$project['seo']['description'] ?: $project['excerpt']"
    :image="$project['seo']['image_url'] ?: $project['cover_image_url']"
    :canonical="$project['seo']['canonical_url'] ?? null"
    :robots="$project['seo']['robots'] ?? null"
>
    <x-banner type="page" :override="['image_url' => $project['cover_image_url'] ?? null]" :page-title="$project['title']" />

    <div class="mx-auto max-w-4xl px-4 py-16 sm:px-6">
        @if ($project['cover_image_url'])
            <img src="{{ $project['cover_image_url'] }}" alt="{{ $project['title'] }}" class="aspect-video w-full rounded-lg object-cover">
        @endif

        <div class="mt-8 text-center">
            @if ($project['category'])
                <a href="{{ route('projects.index', ['category' => $project['category']['slug']]) }}" class="text-xs font-medium uppercase tracking-wide text-brand-600 hover:text-brand-500">
                    {{ $project['category']['name'] }}
                </a>
            @endif

            @if ($project['excerpt'])
                <p class="mt-4 text-lg text-gray-600">{{ $project['excerpt'] }}</p>
            @endif
        </div>

        @if ($project['content'])
            <div class="prose prose-neutral mt-8 max-w-none text-gray-700">
                {!! str_starts_with(ltrim($project['content']), '<') ? $project['content'] : nl2br(e($project['content'])) !!}
            </div>
        @endif

        @if (! empty($project['gallery_items']))
            <div class="mt-10">
                <x-frontend.lightbox-gallery :items="$project['gallery_items']" />
            </div>
        @endif

        @if (! empty($stories))
            <div class="mt-16">
                <h2 class="text-center font-display text-2xl font-bold text-ink-900">{{ __('Success Stories From This Project') }}</h2>

                <div class="mt-8 grid grid-cols-1 gap-8 sm:grid-cols-2">
                    @foreach ($stories as $story)
                        <x-frontend.story-card :story="$story" />
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-16 border-t border-gray-100 pt-12">
            <h2 class="text-center font-display text-2xl font-bold text-ink-900">{{ __('Visitor Book') }}</h2>
            <p class="mt-2 text-center text-sm text-gray-500">{{ __('Visited this project? Share your opinion below.') }}</p>

            @if (! empty($visitorBookEntries))
                <div class="mx-auto mt-8 max-w-xl space-y-4">
                    @foreach ($visitorBookEntries as $entry)
                        <div class="rounded-lg bg-gray-50 p-4">
                            <p class="text-sm text-gray-700">{{ $entry->opinion }}</p>
                            <p class="mt-2 text-xs font-medium text-gray-500">{{ $entry->visitor_name }} &middot; {{ $entry->created_at->format('M j, Y') }}</p>
                        </div>
                    @endforeach
                </div>

                <p class="mt-4 text-center text-sm">
                    <a href="{{ route('visitor-book.index') }}" class="font-medium text-brand-600 hover:text-brand-500">{{ __('View the full Visitor Book') }} &rarr;</a>
                </p>
            @endif

            <div class="mt-8">
                <x-frontend.visitor-book-form :project-slug="$project['slug']" />
            </div>
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('projects.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-500">&larr; {{ __('Back to Projects') }}</a>
        </div>
    </div>
</x-frontend-layout>
