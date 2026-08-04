@props(['project'])

<a href="{{ route('projects.show', $project['slug']) }}" class="group block overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-black/5 transition hover:shadow-xl">
    <div class="relative h-52 overflow-hidden">
        @if ($project['cover_image_url'])
            <img src="{{ $project['cover_image_url'] }}" alt="" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gray-100 text-gray-300">
                <x-icon name="photo" class="h-10 w-10" />
            </div>
        @endif

        @if ($project['is_featured'])
            <span class="absolute left-2 top-2 rounded-full bg-brand-500 px-2 py-0.5 text-xs font-semibold text-white">{{ __('Featured') }}</span>
        @endif
    </div>

    <div class="p-6">
        @if ($project['category'])
            <p class="text-xs font-medium uppercase tracking-wide text-brand-600">{{ $project['category']['name'] }}</p>
        @endif

        <h3 class="mt-1 text-lg font-semibold text-ink-900">{{ $project['title'] }}</h3>

        @if ($project['excerpt'])
            <p class="mt-2 text-sm text-ink-900/60">{{ $project['excerpt'] }}</p>
        @endif
    </div>
</a>
