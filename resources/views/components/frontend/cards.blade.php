@props([
    'heading' => null,
    'subheading' => null,
    'items' => [],
])

<section {{ $attributes->merge(['class' => 'bg-ink-900 py-20 sm:py-24']) }}>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto mb-16 max-w-2xl text-center">
            @if ($subheading)
                <p class="mb-3 text-sm font-semibold uppercase tracking-widest text-brand-400">{{ $subheading }}</p>
            @endif

            @if ($heading)
                <h2 class="font-display text-3xl font-bold text-white sm:text-4xl">{{ $heading }}</h2>
            @endif
        </div>

        <div class="grid gap-8 md:grid-cols-3">
            @foreach ($items as $item)
                <div class="rounded-2xl bg-white/5 p-8 ring-1 ring-white/10 transition hover:ring-brand-500/60">
                    @if ($item['icon'] ?? null)
                        <div class="mb-6 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-500">
                            <x-icon :name="$item['icon']" class="h-6 w-6 text-white" />
                        </div>
                    @elseif ($item['image_url'] ?? null)
                        <img src="{{ $item['image_url'] }}" alt="" class="mb-6 h-12 w-12 object-contain" loading="lazy">
                    @endif

                    @if ($item['title'] ?? null)
                        <h3 class="mb-2 text-lg font-semibold text-white">{{ $item['title'] }}</h3>
                    @endif

                    @if ($item['body'] ?? null)
                        <p class="text-sm leading-relaxed text-white/60">{{ $item['body'] }}</p>
                    @endif

                    @if ($item['url'] ?? null)
                        <a href="{{ $item['url'] }}" class="mt-4 inline-block text-sm font-semibold text-brand-400 hover:text-brand-300">
                            {{ __('Learn more') }} &rarr;
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
