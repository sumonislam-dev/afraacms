@props([
    'heading' => null,
    'subheading' => null,
    'body' => null,
    'imageUrl' => null,
    'buttonText' => null,
    'buttonUrl' => null,
    'headingTag' => 'h1',
])

@php
    $headingTag = in_array($headingTag, ['h1', 'h2', 'h3']) ? $headingTag : 'h1';
@endphp

<section {{ $attributes->merge(['class' => 'bg-gray-50 py-20']) }}>
    <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" alt="" class="mx-auto mb-8 h-16 w-auto">
        @endif

        @if ($heading)
            <{{ $headingTag }} class="text-4xl font-bold text-gray-900">{{ $heading }}</{{ $headingTag }}>
        @endif

        @if ($subheading)
            <p class="mt-4 text-lg text-gray-600">{{ $subheading }}</p>
        @endif

        @if ($body)
            <div class="mt-4 text-gray-600">{!! nl2br(e($body)) !!}</div>
        @endif

        @if ($buttonText && $buttonUrl)
            <a href="{{ $buttonUrl }}" class="mt-8 inline-block rounded-md bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-500">
                {{ $buttonText }}
            </a>
        @endif
    </div>
</section>
