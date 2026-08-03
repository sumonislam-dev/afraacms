@props(['name', 'value' => null, 'disabled' => false])

@php
    $color = $value ?: '#4f46e5';
@endphp

<div class="mt-1 flex items-center gap-3">
    <input
        type="color"
        name="{{ $name }}"
        value="{{ $color }}"
        @disabled($disabled)
        class="h-10 w-14 cursor-pointer rounded-sm border border-gray-300 p-1"
    >
    <span class="text-sm text-gray-500">{{ $color }}</span>
</div>
