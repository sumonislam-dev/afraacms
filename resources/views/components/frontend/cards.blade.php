@props([
    'heading' => null,
    'subheading' => null,
    'items' => [],
    'layout' => 'dark',
])

@php
    $isLight = $layout === 'light';
@endphp

<section {{ $attributes->merge(['class' => ($isLight ? 'bg-white' : 'bg-ink-900').' py-20 sm:py-24']) }}>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto mb-16 max-w-2xl text-center">
            @if ($subheading)
                <p class="mb-3 text-sm font-semibold uppercase tracking-widest {{ $isLight ? 'text-brand-600' : 'text-brand-400' }}">{{ $subheading }}</p>
            @endif

            @if ($heading)
                <h2 class="font-display text-3xl font-bold sm:text-4xl {{ $isLight ? 'text-ink-900' : 'text-white' }}">{{ $heading }}</h2>
            @endif
        </div>

        <div class="grid gap-8 md:grid-cols-3">
            @foreach ($items as $item)
                <div class="rounded-2xl p-8 transition {{ $isLight ? 'bg-white shadow-md ring-1 ring-black/5 hover:shadow-xl' : 'bg-white/5 ring-1 ring-white/10 hover:ring-brand-500/60' }}">
                    @if ($item['icon'] ?? null)
                        <div class="mb-6 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-500">
                            <x-icon :name="$item['icon']" class="h-6 w-6 text-white" />
                        </div>
                    @elseif ($item['image_url'] ?? null)
                        <img src="{{ $item['image_url'] }}" alt="" class="mb-6 h-12 w-12 object-contain" loading="lazy">
                    @endif

                    @if ($item['title'] ?? null)
                        <h3 class="mb-2 text-lg font-semibold {{ $isLight ? 'text-ink-900' : 'text-white' }}">{{ $item['title'] }}</h3>
                    @endif

                    @if ($item['body'] ?? null)
                        <p class="text-sm leading-relaxed {{ $isLight ? 'text-ink-900/60' : 'text-white/60' }}">{{ $item['body'] }}</p>
                    @endif

                    @if ($item['url'] ?? null)
                        <a href="{{ $item['url'] }}" class="mt-4 inline-block text-sm font-semibold {{ $isLight ? 'text-brand-600 hover:text-brand-700' : 'text-brand-400 hover:text-brand-300' }}">
                            {{ __('Learn more') }} &rarr;
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
