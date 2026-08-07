@php
    $slides = collect(app(\App\CMS\Services\GalleryService::class)->all())
        ->whereIn('id', $section['gallery_ids'] ?? [])
        ->flatMap(fn ($gallery) => collect($gallery['items'])->where('type', 'image')->map(fn ($item) => [
            'image_url' => $item['image_url'],
            'title' => $item['caption'],
        ]))
        ->filter(fn ($slide) => $slide['image_url'])
        ->values()
        ->all();
@endphp

@if (count($slides))
    <section class="bg-white py-20 sm:py-28">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto mb-12 max-w-2xl text-center">
                @if ($section['subheading'])
                    <p class="mb-3 text-sm font-semibold uppercase tracking-widest text-brand-600">{{ $section['subheading'] }}</p>
                @endif

                @if ($section['heading'])
                    <h2 class="font-display text-3xl font-bold text-ink-900 sm:text-4xl">{{ $section['heading'] }}</h2>
                @endif
            </div>

            <x-frontend.slider :items="$slides" />
        </div>
    </section>
@endif
