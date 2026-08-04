@php
    $backgroundImages = collect(app(\App\CMS\Services\GalleryService::class)->all())
        ->whereIn('id', $section['gallery_ids'] ?? [])
        ->flatMap(fn ($gallery) => collect($gallery['items'])->where('type', 'image')->pluck('image_url'))
        ->filter()
        ->values()
        ->all();
@endphp

<x-frontend.hero
    :heading="$section['heading']"
    :subheading="$section['subheading']"
    :body="$section['body']"
    :image-url="$section['image_url']"
    :background-images="$backgroundImages"
    :button-text="$section['button_text']"
    :button-url="$section['button_url']"
/>
