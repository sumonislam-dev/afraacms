@props(['project'])

<a href="{{ route('projects.show', $project['slug']) }}" class="group block overflow-hidden rounded-lg border border-gray-200">
    <div class="relative">
        @if ($project['cover_image_url'])
            <img src="{{ $project['cover_image_url'] }}" alt="" class="aspect-video w-full object-cover transition group-hover:opacity-90">
        @else
            <div class="flex aspect-video w-full items-center justify-center bg-gray-100 text-gray-300">
                <x-icon name="photo" class="h-10 w-10" />
            </div>
        @endif

        @if ($project['is_featured'])
            <span class="absolute left-2 top-2 rounded-full bg-amber-500 px-2 py-0.5 text-xs font-semibold text-white">{{ __('Featured') }}</span>
        @endif
    </div>

    <div class="p-4">
        @if ($project['category'])
            <p class="text-xs font-medium uppercase tracking-wide text-indigo-600">{{ $project['category']['name'] }}</p>
        @endif

        <h2 class="mt-1 font-semibold text-gray-900">{{ $project['title'] }}</h2>

        @if ($project['excerpt'])
            <p class="mt-1 truncate text-sm text-gray-500">{{ $project['excerpt'] }}</p>
        @endif
    </div>
</a>
