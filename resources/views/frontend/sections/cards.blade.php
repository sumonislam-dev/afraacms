<section class="py-16">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        @if ($section['heading'])
            <h2 class="text-center text-2xl font-bold text-gray-900">{{ $section['heading'] }}</h2>
        @endif

        @if ($section['subheading'])
            <p class="mt-2 text-center text-gray-600">{{ $section['subheading'] }}</p>
        @endif

        <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($section['items'] as $item)
                <div class="rounded-lg border border-gray-200 p-6">
                    @if ($item['image_url'])
                        <img src="{{ $item['image_url'] }}" alt="" class="mb-4 h-12 w-12 object-contain">
                    @endif

                    @if ($item['title'])
                        <h3 class="text-lg font-semibold text-gray-900">{{ $item['title'] }}</h3>
                    @endif

                    @if ($item['body'])
                        <p class="mt-2 text-sm text-gray-600">{{ $item['body'] }}</p>
                    @endif

                    @if ($item['url'])
                        <a href="{{ $item['url'] }}" class="mt-4 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            {{ __('Learn more') }} &rarr;
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
