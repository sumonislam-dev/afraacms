@props([
    'heading' => null,
    'subheading' => null,
    'body' => null,
    'imageUrl' => null,
    'backgroundImages' => [],
    'buttonText' => null,
    'buttonUrl' => null,
    'headingTag' => 'h1',
])

@php
    $headingTag = in_array($headingTag, ['h1', 'h2', 'h3']) ? $headingTag : 'h1';
    $slides = ! empty($backgroundImages) ? $backgroundImages : ($imageUrl ? [$imageUrl] : []);
    $rotating = count($slides) > 1;
@endphp

<section
    {{ $attributes->merge(['class' => 'relative flex h-[92vh] min-h-[560px] w-full items-center overflow-hidden bg-ink-900']) }}
    @if ($rotating)
        x-data="{
            active: 0,
            count: {{ count($slides) }},
            timer: null,
            next() { this.active = (this.active + 1) % this.count; },
            start() { this.timer = setInterval(() => this.next(), 5000); },
        }"
        x-init="start()"
    @endif
>
    @if (! empty($slides))
        <div class="absolute inset-0">
            @foreach ($slides as $index => $image)
                <div
                    class="absolute inset-0 bg-cover bg-center transition-opacity duration-1000 {{ $rotating ? '' : 'opacity-100' }}"
                    style="background-image:url('{{ $image }}')"
                    @if ($rotating)
                        :class="active === {{ $index }} ? 'opacity-100' : 'opacity-0'"
                    @endif
                ></div>
            @endforeach
        </div>
    @endif

    <div class="absolute inset-0 bg-hero-overlay"></div>

    <div class="relative mx-auto flex max-w-3xl flex-col items-center px-4 py-24 text-center sm:px-6">
        @if ($subheading)
            <p class="mb-4 text-sm font-semibold uppercase tracking-widest text-brand-400">{{ $subheading }}</p>
        @endif

        @if ($heading)
            <{{ $headingTag }} class="mb-6 font-display text-4xl font-bold leading-tight text-white sm:text-5xl md:text-6xl">{{ $heading }}</{{ $headingTag }}>
        @endif

        @if ($body)
            <div class="mb-10 max-w-2xl text-lg text-white/85">{!! nl2br(e($body)) !!}</div>
        @endif

        @if ($buttonText && $buttonUrl)
            <a
                href="{{ $buttonUrl }}"
                class="rounded-full bg-brand-500 px-7 py-3.5 font-semibold text-white shadow-lg shadow-brand-900/30 transition hover:bg-brand-600"
            >
                {{ $buttonText }}
            </a>
        @endif
    </div>

    @if ($rotating)
        <div class="absolute bottom-6 left-1/2 flex -translate-x-1/2 gap-2">
            @foreach ($slides as $index => $image)
                <button
                    type="button"
                    @click="active = {{ $index }}"
                    :class="active === {{ $index }} ? 'w-8 bg-white' : 'w-2.5 bg-white/40'"
                    class="h-2.5 rounded-full transition-all"
                    aria-label="{{ __('Slide') }} {{ $index + 1 }}"
                ></button>
            @endforeach
        </div>
    @endif
</section>
