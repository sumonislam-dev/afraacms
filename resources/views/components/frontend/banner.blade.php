@props([
    'title' => null,
    'subtitle' => null,
    'imageUrl' => null,
    'buttonText' => null,
    'buttonUrl' => null,
])

<div {{ $attributes->merge(['class' => 'relative overflow-hidden bg-gray-900 py-8']) }}>
    @if ($imageUrl)
        <img src="{{ $imageUrl }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-40">
    @endif

    <div class="relative mx-auto flex max-w-6xl flex-col items-center gap-3 px-4 text-center sm:flex-row sm:justify-between sm:text-left sm:px-6">
        <div>
            @if ($title)
                <p class="text-lg font-semibold text-white">{{ $title }}</p>
            @endif

            @if ($subtitle)
                <p class="mt-1 text-sm text-gray-300">{{ $subtitle }}</p>
            @endif
        </div>

        @if ($buttonText && $buttonUrl)
            <a href="{{ $buttonUrl }}" class="shrink-0 rounded-md bg-white px-5 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-100">
                {{ $buttonText }}
            </a>
        @endif
    </div>
</div>
