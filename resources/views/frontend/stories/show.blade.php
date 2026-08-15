<x-frontend-layout
    :title="$story['seo']['title'] ?: $story['title']"
    :description="$story['seo']['description'] ?: $story['excerpt']"
    :image="$story['seo']['image_url'] ?: $story['cover_image_url']"
    :canonical="$story['seo']['canonical_url'] ?? null"
    :robots="$story['seo']['robots'] ?? null"
>
    <x-banner type="page" :override="['image_url' => $story['cover_image_url'] ?? null]" :page-title="$story['title']" />

    <div class="mx-auto max-w-4xl px-4 py-16 sm:px-6">
        @if ($story['cover_image_url'])
            <img src="{{ $story['cover_image_url'] }}" alt="{{ $story['title'] }}" class="aspect-video w-full rounded-lg object-cover">
        @endif

        <div class="mt-8 text-center">
            <div class="flex items-center justify-center gap-2 text-xs font-medium uppercase tracking-wide text-brand-600">
                @if ($story['project'])
                    <a href="{{ route('stories.index', ['project' => $story['project']['slug']]) }}" class="hover:text-brand-500">
                        {{ $story['project']['title'] }}
                    </a>
                    <span>&middot;</span>
                @endif

                @if ($story['published_at'])
                    <span class="text-ink-900/50">{{ \Illuminate\Support\Carbon::parse($story['published_at'])->format('F j, Y') }}</span>
                @endif
            </div>

            @if ($story['excerpt'])
                <p class="mt-4 text-lg text-gray-600">{{ $story['excerpt'] }}</p>
            @endif
        </div>

        @if ($story['content'])
            <div class="prose prose-neutral mt-8 max-w-none text-gray-700">
                {!! str_starts_with(ltrim($story['content']), '<') ? $story['content'] : nl2br(e($story['content'])) !!}
            </div>
        @endif

        <div class="mt-10 text-center">
            <a href="{{ route('stories.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-500">&larr; {{ __('Back to Success Stories') }}</a>
        </div>
    </div>
</x-frontend-layout>
