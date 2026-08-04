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
                    @if ($item['image_url'] ?? null)
                        <img src="{{ $item['image_url'] }}" alt="{{ $item['title'] ?? '' }}" class="mx-auto h-40 w-40 rounded-full object-cover shadow-md" loading="lazy">
                    @else
                        <div class="mx-auto flex h-40 w-40 items-center justify-center rounded-full bg-gray-100">
                            <x-icon name="user-circle" class="h-16 w-16 text-gray-300" />
                        </div>
                    @endif

                    @if ($item['title'] ?? null)
                        <h3 class="mt-5 text-lg font-semibold text-gray-900">{{ $item['title'] }}</h3>
                    @endif

                    @if ($item['subtitle'] ?? null)
                        <p class="text-sm font-medium text-brand-600">{{ $item['subtitle'] }}</p>
                    @endif

                    @if ($item['body'] ?? null)
                        <p class="mt-2 text-sm leading-relaxed text-gray-600">{{ $item['body'] }}</p>
                    @endif

                    @if ($item['url'] ?? null)
                        <a href="{{ $item['url'] }}" target="_blank" rel="noopener" class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-500">
                            <x-icon name="link" class="h-4 w-4" />
                            {{ __('Profile') }}
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
