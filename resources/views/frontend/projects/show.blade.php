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

        <div class="mt-10 text-center">
            <a href="{{ route('projects.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-500">&larr; {{ __('Back to Projects') }}</a>
        </div>
    </div>
</x-frontend-layout>
