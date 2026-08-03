@props([
    'heading' => null,
    'subheading' => null,
    'items' => [],
])

<section {{ $attributes->merge(['class' => 'py-16']) }}>
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        @if ($heading)
            <h2 class="text-center text-2xl font-bold text-gray-900">{{ $heading }}</h2>
        @endif

        @if ($subheading)
            <p class="mt-2 text-center text-gray-600">{{ $subheading }}</p>
        @endif

        <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($items as $item)
                <div class="rounded-lg border border-gray-200 p-6">
                    @if ($item['image_url'] ?? null)
                        <img src="{{ $item['image_url'] }}" alt="" class="mb-4 h-12 w-12 object-contain" loading="lazy">
                    @endif

                    @if ($item['title'] ?? null)
                        <h3 class="text-lg font-semibold text-gray-900">{{ $item['title'] }}</h3>
                    @endif

                    @if ($item['body'] ?? null)
                        <p class="mt-2 text-sm text-gray-600">{{ $item['body'] }}</p>
                    @endif

                    @if ($item['url'] ?? null)
                        <a href="{{ $item['url'] }}" class="mt-4 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            {{ __('Learn more') }} &rarr;
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
