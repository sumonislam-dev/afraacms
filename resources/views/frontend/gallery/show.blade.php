<x-frontend-layout
    :title="$album['seo']['title'] ?: $album['title']"
    :description="$album['seo']['description'] ?: $album['description']"
    :image="$album['seo']['image_url'] ?: $album['cover_image_url']"
    :canonical="$album['seo']['canonical_url'] ?? null"
    :robots="$album['seo']['robots'] ?? null"
>
    <x-banner type="page" :page-title="$album['title']" />

    <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <div class="text-center">
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
            <a href="{{ route('gallery.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-500">&larr; {{ __('Back to Gallery') }}</a>
        </div>
    </div>
</x-frontend-layout>
