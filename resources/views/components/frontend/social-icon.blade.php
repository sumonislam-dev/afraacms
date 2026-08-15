@props(['name'])

@php
    $icon = config("social-icons.{$name}");
@endphp

@if ($icon)
    <svg {{ $attributes->merge(['class' => 'h-4 w-4', 'fill' => 'currentColor', 'viewBox' => $icon['viewBox'], 'aria-hidden' => 'true']) }}>
        <path d="{{ $icon['path'] }}" />
    </svg>
@endif
