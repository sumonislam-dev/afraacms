@php
    $allAlbums = collect(app(\App\CMS\Services\GalleryService::class)->all());
    $selectedIds = $section['gallery_ids'] ?? [];

    $albums = empty($selectedIds)
        ? $allAlbums->take(6)
        : $allAlbums->whereIn('id', $selectedIds)->values();
@endphp

@if ($albums->isNotEmpty())
    <section class="py-20 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto mb-14 max-w-2xl text-center">
                @if ($section['subheading'])
                    <p class="mb-3 text-sm font-semibold uppercase tracking-widest text-brand-600">{{ $section['subheading'] }}</p>
                @endif

                @if ($section['heading'])
                    <h2 class="font-display text-3xl font-bold text-ink-900 sm:text-4xl">{{ $section['heading'] }}</h2>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($albums as $album)
                    <x-frontend.album-card :album="$album" />
                @endforeach
            </div>

            <div class="mt-10 text-center">
                <a
                    href="{{ $section['button_url'] ?: route('gallery.index') }}"
                    class="inline-block rounded-full border border-brand-500 px-6 py-3 text-sm font-semibold text-brand-600 transition hover:bg-brand-500 hover:text-white"
                >
                    {{ $section['button_text'] ?: __('View Full Gallery') }}
                </a>
            </div>
        </div>
    </section>
@endif
