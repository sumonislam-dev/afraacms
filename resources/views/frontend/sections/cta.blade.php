<section class="bg-indigo-600 py-16">
    <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
        @if ($section['heading'])
            <h2 class="text-3xl font-bold text-white">{{ $section['heading'] }}</h2>
        @endif

        @if ($section['subheading'])
            <p class="mt-4 text-indigo-100">{{ $section['subheading'] }}</p>
        @endif

        @if ($section['button_text'] && $section['button_url'])
            <a href="{{ $section['button_url'] }}" class="mt-8 inline-block rounded-md bg-white px-6 py-3 text-sm font-semibold text-indigo-600 hover:bg-indigo-50">
                {{ $section['button_text'] }}
            </a>
        @endif
    </div>
</section>
