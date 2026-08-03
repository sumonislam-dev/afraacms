@props([
    'heading' => null,
    'items' => [],
])

<section {{ $attributes->merge(['class' => 'py-16']) }}>
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        @if ($heading)
            <h2 class="text-center text-2xl font-bold text-gray-900">{{ $heading }}</h2>
        @endif

        <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($items as $item)
                @if ($item['image_url'] ?? null)
                    <figure>
                        <img src="{{ $item['image_url'] }}" alt="{{ $item['title'] ?? '' }}" class="aspect-square w-full rounded-lg object-cover" loading="lazy">

                        @if ($item['title'] ?? null)
                            <figcaption class="mt-2 text-center text-sm text-gray-500">{{ $item['title'] }}</figcaption>
                        @endif
                    </figure>
                @endif
            @endforeach
        </div>
    </div>
</section>
