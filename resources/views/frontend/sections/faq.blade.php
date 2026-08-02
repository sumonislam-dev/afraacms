<section class="py-16">
    <div class="mx-auto max-w-3xl px-4 sm:px-6">
        @if ($section['heading'])
            <h2 class="text-center text-2xl font-bold text-gray-900">{{ $section['heading'] }}</h2>
        @endif

        <div class="mt-10 space-y-6">
            @foreach ($section['items'] as $item)
                <div>
                    @if ($item['title'])
                        <h3 class="font-semibold text-gray-900">{{ $item['title'] }}</h3>
                    @endif

                    @if ($item['body'])
                        <p class="mt-2 text-sm text-gray-600">{{ $item['body'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
