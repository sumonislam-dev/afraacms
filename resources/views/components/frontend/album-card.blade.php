@props(['album'])

<a href="{{ route('gallery.show', $album['slug']) }}" class="group block overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-black/5 transition hover:shadow-xl">
    @if ($album['cover_image_url'])
        <img src="{{ $album['cover_image_url'] }}" alt="" class="aspect-video w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
    @else
        <div class="flex aspect-video w-full items-center justify-center bg-gray-100 text-gray-300">
            <x-icon name="photo" class="h-10 w-10" />
        </div>
    @endif

    <div class="p-6">
        <h3 class="font-semibold text-ink-900">{{ $album['title'] }}</h3>

        @if ($album['description'])
            <p class="mt-1 truncate text-sm text-ink-900/60">{{ $album['description'] }}</p>
        @endif
    </div>
</a>
