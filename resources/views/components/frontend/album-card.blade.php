@props(['album'])

<a href="{{ route('gallery.show', $album['slug']) }}" class="group block overflow-hidden rounded-lg border border-gray-200">
    @if ($album['cover_image_url'])
        <img src="{{ $album['cover_image_url'] }}" alt="" class="aspect-video w-full object-cover transition group-hover:opacity-90" loading="lazy">
    @else
        <div class="flex aspect-video w-full items-center justify-center bg-gray-100 text-gray-300">
            <x-icon name="photo" class="h-10 w-10" />
        </div>
    @endif

    <div class="p-4">
        <h2 class="font-semibold text-gray-900">{{ $album['title'] }}</h2>

        @if ($album['description'])
            <p class="mt-1 truncate text-sm text-gray-500">{{ $album['description'] }}</p>
        @endif
    </div>
</a>
