<x-frontend-layout
    :title="$project['seo']['title'] ?: $project['title']"
    :description="$project['seo']['description'] ?: $project['excerpt']"
    :image="$project['seo']['image_url'] ?: $project['cover_image_url']"
    :canonical="$project['seo']['canonical_url'] ?? null"
    :robots="$project['seo']['robots'] ?? null"
>
    <x-banner type="page" />

    <div class="mx-auto max-w-4xl px-4 py-16 sm:px-6">
        @if ($project['cover_image_url'])
            <img src="{{ $project['cover_image_url'] }}" alt="" class="aspect-video w-full rounded-lg object-cover">
        @endif

        <div class="mt-8 text-center">
            @if ($project['category'])
                <a href="{{ route('projects.index', ['category' => $project['category']['slug']]) }}" class="text-xs font-medium uppercase tracking-wide text-indigo-600 hover:text-indigo-500">
                    {{ $project['category']['name'] }}
                </a>
            @endif

            <h1 class="mt-1 text-3xl font-bold text-gray-900">{{ $project['title'] }}</h1>

            @if ($project['excerpt'])
                <p class="mt-4 text-lg text-gray-600">{{ $project['excerpt'] }}</p>
            @endif
        </div>

        @if ($project['content'])
            <div class="mt-8 space-y-4 text-gray-700">
                {!! nl2br(e($project['content'])) !!}
            </div>
        @endif

        @if (! empty($project['gallery_items']))
            <div class="mt-10">
                <x-frontend.lightbox-gallery :items="$project['gallery_items']" />
            </div>
        @endif

        <div class="mt-10 text-center">
            <a href="{{ route('projects.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">&larr; {{ __('Back to Projects') }}</a>
        </div>
    </div>
</x-frontend-layout>
