@php
    $imageRight = ($section['layout'] ?? 'image-left') === 'image-right';
@endphp

<section class="py-16">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2">
            <div class="{{ $imageRight ? 'lg:order-2' : '' }}">
                @if ($section['image_url'])
                    <img src="{{ $section['image_url'] }}" alt="" class="w-full rounded-lg object-cover">
                @endif
            </div>

            <div class="{{ $imageRight ? 'lg:order-1' : '' }}">
                @if ($section['heading'])
                    <h2 class="text-2xl font-bold text-gray-900">{{ $section['heading'] }}</h2>
                @endif

                @if ($section['subheading'])
                    <p class="mt-2 text-lg text-gray-600">{{ $section['subheading'] }}</p>
                @endif

                @if ($section['body'])
                    <div class="mt-4 space-y-4 text-gray-700">{!! nl2br(e($section['body'])) !!}</div>
                @endif
            </div>
        </div>
    </div>
</section>
