@props(['story'])

<a href="{{ route('stories.show', $story['slug']) }}" class="group block overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-black/5 transition hover:shadow-xl">
    <div class="relative h-52 overflow-hidden">
        @if ($story['cover_image_url'])
            <img src="{{ $story['cover_image_url'] }}" alt="" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gray-100 text-gray-300">
                <x-icon name="photo" class="h-10 w-10" />
            </div>
        @endif

        @if ($story['is_featured'])
            <span class="absolute left-2 top-2 rounded-full bg-brand-500 px-2 py-0.5 text-xs font-semibold text-white">{{ __('Featured') }}</span>
        @endif
    </div>

    <div class="p-6">
        <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-brand-600">
            @if ($story['project'])
                <span>{{ $story['project']['title'] }}</span>
                <span>&middot;</span>
            @endif

            @if ($story['published_at'])
                <span class="text-ink-900/50">{{ \Illuminate\Support\Carbon::parse($story['published_at'])->format('M j, Y') }}</span>
            @endif
        </div>

        <h3 class="mt-2 text-lg font-semibold text-ink-900">{{ $story['title'] }}</h3>

        @if ($story['excerpt'])
            <p class="mt-2 text-sm text-ink-900/60">{{ $story['excerpt'] }}</p>
        @endif
    </div>
</a>
