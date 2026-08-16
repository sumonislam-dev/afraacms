@props([
    'heading' => null,
    'subheading' => null,
    'items' => [],
])

<section {{ $attributes->merge(['class' => 'bg-white py-20 sm:py-24']) }}>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto mb-16 max-w-2xl text-center">
            @if ($subheading)
                <p class="mb-3 text-sm font-semibold uppercase tracking-widest text-brand-600">{{ $subheading }}</p>
            @endif

            @if ($heading)
                <h2 class="font-display text-3xl font-bold text-gray-900 sm:text-4xl">{{ $heading }}</h2>
            @endif
        </div>

        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($items as $item)
                <div class="text-center">
                    @if ($item['photo_url'] ?? null)
                        <img src="{{ $item['photo_url'] }}" alt="{{ $item['name'] ?? '' }}" class="mx-auto h-32 w-32 rounded-full object-cover shadow-md" loading="lazy">
                    @else
                        <div class="mx-auto flex h-32 w-32 items-center justify-center rounded-full bg-gray-100">
                            <x-icon name="user-circle" class="h-14 w-14 text-gray-300" />
                        </div>
                    @endif

                    @if ($item['name'] ?? null)
                        <h3 class="mt-4 text-base font-semibold text-gray-900">{{ $item['name'] }}</h3>
                    @endif

                    @if ($item['organization'] ?? null)
                        <p class="text-sm font-medium text-brand-600">{{ $item['organization'] }}</p>
                    @endif

                    @if (($item['country'] ?? null) || ($item['visited_at'] ?? null))
                        <p class="mt-1 text-xs text-gray-500">
                            {{ implode(' · ', array_filter([
                                $item['country'] ?? null,
                                isset($item['visited_at']) ? \Illuminate\Support\Carbon::parse($item['visited_at'])->format('M Y') : null,
                            ])) }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
