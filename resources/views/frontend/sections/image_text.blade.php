@php
    $imageRight = ($section['layout'] ?? 'image-left') === 'image-right';
@endphp

<section class="py-20 sm:py-28">
    <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-14 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div class="{{ $imageRight ? 'lg:order-2' : '' }}">
            @if ($section['image_url'])
                <img src="{{ $section['image_url'] }}" alt="{{ $section['heading'] ?? '' }}" class="h-64 w-full rounded-2xl object-cover shadow-lg sm:h-96" loading="lazy">
            @endif
        </div>

        <div class="{{ $imageRight ? 'lg:order-1' : '' }}">
            @if ($section['subheading'])
                <p class="mb-3 text-sm font-semibold uppercase tracking-widest text-brand-600">{{ $section['subheading'] }}</p>
            @endif

            @if ($section['heading'])
                <h2 class="mb-6 font-display text-3xl font-bold text-ink-900 sm:text-4xl">{{ $section['heading'] }}</h2>
            @endif

            @if ($section['body'])
                <div class="prose prose-neutral max-w-none leading-relaxed text-ink-900/70">{!! str_starts_with(ltrim($section['body']), '<') ? $section['body'] : nl2br(e($section['body'])) !!}</div>
            @endif
        </div>
    </div>
</section>
