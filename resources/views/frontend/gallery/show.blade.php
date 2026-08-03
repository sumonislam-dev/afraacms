<x-frontend-layout :title="$album['title']">
    <x-banner type="page" />

    <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900">{{ $album['title'] }}</h1>

            @if ($album['description'])
                <p class="mt-4 text-gray-600">{{ $album['description'] }}</p>
            @endif
        </div>

        <div class="mt-10">
            @if (empty($album['items']))
                <p class="text-center text-gray-500">{{ __('No photos or videos yet.') }}</p>
            @else
                <x-frontend.lightbox-gallery :items="$album['items']" />
            @endif
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('gallery.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">&larr; {{ __('Back to Gallery') }}</a>
        </div>
    </div>
</x-frontend-layout>
