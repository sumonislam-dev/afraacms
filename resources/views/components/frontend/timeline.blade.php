@props([
    'heading' => null,
    'items' => [],
])

<section {{ $attributes->merge(['class' => 'py-16']) }}>
    <div class="mx-auto max-w-3xl px-4 sm:px-6">
        @if ($heading)
            <h2 class="text-center text-2xl font-bold text-gray-900">{{ $heading }}</h2>
        @endif

        <div class="mt-10 space-y-8 border-l border-gray-200 pl-6">
            @foreach ($items as $item)
                <div>
                    @if ($item['subtitle'] ?? null)
                        <p class="text-sm font-medium text-indigo-600">{{ $item['subtitle'] }}</p>
                    @endif

                    @if ($item['title'] ?? null)
                        <h3 class="font-semibold text-gray-900">{{ $item['title'] }}</h3>
                    @endif

                    @if ($item['body'] ?? null)
                        <p class="mt-1 text-sm text-gray-600">{{ $item['body'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
