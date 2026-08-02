<section class="py-16">
    <div class="mx-auto max-w-3xl px-4 sm:px-6">
        @if ($section['heading'])
            <h2 class="text-2xl font-bold text-gray-900">{{ $section['heading'] }}</h2>
        @endif

        @if ($section['body'])
            <div class="mt-4 space-y-4 text-gray-700">
                {!! $section['body'] !!}
            </div>
        @endif
    </div>
</section>
