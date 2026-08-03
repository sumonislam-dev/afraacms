@props([
    'heading' => null,
    'items' => [],
])

<section {{ $attributes->merge(['class' => 'py-16']) }}>
    <div class="mx-auto max-w-3xl px-4 sm:px-6">
        @if ($heading)
            <h2 class="text-center text-2xl font-bold text-gray-900">{{ $heading }}</h2>
        @endif

        <div class="mt-10 divide-y divide-gray-200">
            @foreach ($items as $item)
                <div x-data="{ open: false }" class="py-4">
                    @if ($item['title'] ?? null)
                        <button type="button" @click="open = ! open" class="flex w-full items-center justify-between text-left" :aria-expanded="open">
                            <h3 class="font-semibold text-gray-900">{{ $item['title'] }}</h3>
                            <span class="shrink-0 text-gray-400 transition-transform" :class="{ 'rotate-180': open }">
                                <x-icon name="chevron-down" class="h-5 w-5" />
                            </span>
                        </button>
                    @endif

                    @if ($item['body'] ?? null)
                        <p class="mt-2 text-sm text-gray-600" x-show="open">{{ $item['body'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
