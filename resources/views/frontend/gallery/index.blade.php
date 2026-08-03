<x-frontend-layout :title="__('Gallery')">
    <x-banner type="page" />

    <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <h1 class="text-center text-3xl font-bold text-gray-900">{{ __('Gallery') }}</h1>

        @if (empty($albums))
            <p class="mt-6 text-center text-gray-500">{{ __('No albums yet.') }}</p>
        @else
            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($albums as $album)
                    <a href="{{ route('gallery.show', $album['slug']) }}" class="group block overflow-hidden rounded-lg border border-gray-200">
                        @if ($album['cover_image_url'])
                            <img src="{{ $album['cover_image_url'] }}" alt="" class="aspect-video w-full object-cover transition group-hover:opacity-90">
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
                @endforeach
            </div>
        @endif
    </div>
</x-frontend-layout>
