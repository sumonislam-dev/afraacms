<section class="bg-gray-50 py-20">
    <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
        @if ($section['image_url'])
            <img src="{{ $section['image_url'] }}" alt="" class="mx-auto mb-8 h-16 w-auto">
        @endif

        @if ($section['heading'])
            <h1 class="text-4xl font-bold text-gray-900">{{ $section['heading'] }}</h1>
        @endif

        @if ($section['subheading'])
            <p class="mt-4 text-lg text-gray-600">{{ $section['subheading'] }}</p>
        @endif

        @if ($section['body'])
            <div class="mt-4 text-gray-600">{!! nl2br(e($section['body'])) !!}</div>
        @endif

        @if ($section['button_text'] && $section['button_url'])
            <a href="{{ $section['button_url'] }}" class="mt-8 inline-block rounded-md bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-500">
                {{ $section['button_text'] }}
            </a>
        @endif
    </div>
</section>
