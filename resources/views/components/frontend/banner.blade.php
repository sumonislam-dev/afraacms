@props([
    'title' => null,
    'subtitle' => null,
    'imageUrl' => null,
    'buttonText' => null,
    'buttonUrl' => null,
    'pageTitle' => null,
])

@if ($pageTitle)
    {{-- Page-header band: small eyebrow + the page's own title, over an
         optional background image. Used at the top of every inner page. --}}
    <section class="relative bg-ink-900 py-20 text-center">
        @if ($imageUrl)
            <div class="absolute inset-0 bg-cover bg-center opacity-20" style="background-image:url('{{ $imageUrl }}')"></div>
        @endif
        <div class="absolute inset-0 bg-ink-900/80"></div>

        <div class="relative">
            @if ($title)
                <p class="mb-3 text-sm font-semibold uppercase tracking-widest text-brand-400">{{ $title }}</p>
            @endif

            <h1 class="font-display text-4xl font-bold text-white">{{ $pageTitle }}</h1>
        </div>
    </section>
@else
    <div {{ $attributes->merge(['class' => 'relative overflow-hidden bg-ink-900 py-8']) }}>
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-40" loading="lazy">
        @endif

        <div class="relative mx-auto flex max-w-6xl flex-col items-center gap-3 px-4 text-center sm:flex-row sm:justify-between sm:px-6 sm:text-left">
            <div>
                @if ($title)
                    <p class="text-lg font-semibold text-white">{{ $title }}</p>
                @endif

                @if ($subtitle)
                    <p class="mt-1 text-sm text-white/70">{{ $subtitle }}</p>
                @endif
            </div>

            @if ($buttonText && $buttonUrl)
                <a href="{{ $buttonUrl }}" class="shrink-0 rounded-full bg-brand-500 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-600">
                    {{ $buttonText }}
                </a>
            @endif
        </div>
    </div>
@endif
