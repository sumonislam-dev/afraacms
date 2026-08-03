@props([
    'heading' => null,
    'subheading' => null,
    'buttonText' => null,
    'buttonUrl' => null,
])

<section {{ $attributes->merge(['class' => 'bg-indigo-600 py-16']) }}>
    <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
        @if ($heading)
            <h2 class="text-3xl font-bold text-white">{{ $heading }}</h2>
        @endif

        @if ($subheading)
            <p class="mt-4 text-indigo-100">{{ $subheading }}</p>
        @endif

        @if ($buttonText && $buttonUrl)
            <a href="{{ $buttonUrl }}" class="mt-8 inline-block rounded-md bg-white px-6 py-3 text-sm font-semibold text-indigo-600 hover:bg-indigo-50">
                {{ $buttonText }}
            </a>
        @endif
    </div>
</section>
