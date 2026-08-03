<x-frontend-layout :title="__('Gallery')">
    <x-banner type="page" />

    <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <h1 class="text-center text-3xl font-bold text-gray-900">{{ __('Gallery') }}</h1>

        @if (empty($albums))
            <p class="mt-6 text-center text-gray-500">{{ __('No albums yet.') }}</p>
        @else
            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($albums as $album)
                    <x-frontend.album-card :album="$album" />
                @endforeach
            </div>
        @endif
    </div>
</x-frontend-layout>
