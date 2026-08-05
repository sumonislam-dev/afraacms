@props(['name' => null, 'nameExpression' => null, 'id' => null, 'value' => '', 'rows' => 8])

@php $id = $id ?? $name; @endphp

<div
    data-rich-text-editor
    data-upload-url="{{ route('admin.media.store') }}"
    id="{{ $id }}-wrapper"
    {{ $attributes->merge(['class' => 'rounded-md border border-gray-300']) }}
>
    <div id="{{ $id }}-toolbar"></div>
    <div id="{{ $id }}-editor" style="min-height: {{ $rows * 24 }}px"></div>
    <textarea
        @if ($nameExpression) x-bind:name="{{ $nameExpression }}" @else name="{{ $name }}" @endif
        id="{{ $id }}"
        class="hidden"
        data-rich-text-input
    >{{ $value }}</textarea>
</div>
