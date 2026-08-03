@props([
    'value' => '0',
    'label' => null,
    'icon' => null,
])

@php
    preg_match('/-?\d+(\.\d+)?/', (string) $value, $matches);
    $numeric = $matches ? (float) $matches[0] : 0;
    $prefix = $matches ? substr($value, 0, strpos($value, $matches[0])) : '';
    $suffix = $matches ? substr($value, strpos($value, $matches[0]) + strlen($matches[0])) : (string) $value;
    $decimals = $numeric == (int) $numeric ? 0 : 1;
@endphp

<div
    {{ $attributes->merge(['class' => 'text-center']) }}
    x-data="{
        current: 0,
        target: {{ $numeric }},
        decimals: {{ $decimals }},
        format(n) { return this.decimals ? n.toFixed(this.decimals) : Math.round(n).toLocaleString(); },
    }"
    x-init="
        new IntersectionObserver((entries, observer) => {
            if (! entries[0].isIntersecting) return;
            observer.disconnect();
            const duration = 1200;
            const start = performance.now();
            const tick = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                current = target * progress;
                if (progress < 1) requestAnimationFrame(tick);
                else current = target;
            };
            requestAnimationFrame(tick);
        }).observe($el);
    "
>
    @if ($icon)
        <x-icon :name="$icon" class="mx-auto h-8 w-8 text-indigo-600" />
    @endif

    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $prefix }}<span x-text="format(current)"></span>{{ $suffix }}</p>

    @if ($label)
        <p class="mt-1 text-sm text-gray-500">{{ $label }}</p>
    @endif
</div>
