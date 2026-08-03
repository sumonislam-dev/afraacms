@props(['title' => null])

<div {{ $attributes->merge(['class' => 'rounded-lg bg-white shadow-sm ring-1 ring-black/5']) }}>
    @if ($title || isset($header))
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-4 sm:px-6">
            @if ($title)
                <h3 class="text-base font-semibold text-gray-900">{{ $title }}</h3>
            @endif

            @isset($header)
                {{ $header }}
            @endisset
        </div>
    @endif

    <div class="p-4 sm:p-6">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="rounded-b-lg border-t border-gray-100 bg-gray-50 px-4 py-3 sm:px-6">
            {{ $footer }}
        </div>
    @endisset
</div>
