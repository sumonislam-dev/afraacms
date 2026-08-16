@props([
    'heading' => null,
    'subheading' => null,
    'items' => [],
])

<section
    {{ $attributes->merge(['class' => 'bg-white py-20 sm:py-24']) }}
    x-data="{
        open: false,
        index: 0,
        items: @js(array_values($items)),
        show(i) { this.index = i; this.open = true; },
        next() { this.index = (this.index + 1) % this.items.length; },
        prev() { this.index = (this.index - 1 + this.items.length) % this.items.length; },
    }"
    @keydown.escape.window="open = false"
    @keydown.arrow-right.window="if (open) next()"
    @keydown.arrow-left.window="if (open) prev()"
>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto mb-16 max-w-2xl text-center">
            @if ($subheading)
                <p class="mb-3 text-sm font-semibold uppercase tracking-widest text-brand-600">{{ $subheading }}</p>
            @endif

            @if ($heading)
                <h2 class="font-display text-3xl font-bold text-gray-900 sm:text-4xl">{{ $heading }}</h2>
            @endif
        </div>

        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($items as $index => $item)
                <button type="button" @click="show({{ $index }})" class="group w-full cursor-pointer text-center">
                    @if ($item['image_url'] ?? null)
                        <img src="{{ $item['image_url'] }}" alt="{{ $item['title'] ?? '' }}" class="mx-auto h-40 w-40 rounded-full object-cover shadow-md transition group-hover:opacity-90" loading="lazy">
                    @else
                        <div class="mx-auto flex h-40 w-40 items-center justify-center rounded-full bg-gray-100">
                            <x-icon name="user-circle" class="h-16 w-16 text-gray-300" />
                        </div>
                    @endif

                    @if ($item['title'] ?? null)
                        <h3 class="mt-5 text-lg font-semibold text-gray-900">{{ $item['title'] }}</h3>
                    @endif

                    @if ($item['subtitle'] ?? null)
                        <p class="text-sm font-medium text-brand-600">{{ $item['subtitle'] }}</p>
                    @endif

                    @if ($item['meta'] ?? null)
                        <p class="mt-1 text-xs text-gray-500">{{ $item['meta'] }}</p>
                    @endif

                    @if ($item['body'] ?? null)
                        <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-gray-600">{{ $item['body'] }}</p>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    <div x-show="open" x-transition style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4" @click.self="open = false">
        <button type="button" @click="open = false" aria-label="{{ __('Close') }}" class="absolute right-4 top-4 cursor-pointer text-white hover:text-gray-300">
            <x-icon name="x-mark" class="h-8 w-8" />
        </button>

        <button type="button" @click="prev()" x-show="items.length > 1" aria-label="{{ __('Previous') }}" class="absolute left-4 top-1/2 -translate-y-1/2 cursor-pointer text-white hover:text-gray-300">
            <x-icon name="chevron-right" class="h-8 w-8 rotate-180" />
        </button>

        <button type="button" @click="next()" x-show="items.length > 1" aria-label="{{ __('Next') }}" class="absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer text-white hover:text-gray-300">
            <x-icon name="chevron-right" class="h-8 w-8" />
        </button>

        <template x-if="open && items[index]">
            <div class="max-h-full w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-8 text-center" @click.stop>
                <template x-if="items[index].image_url">
                    <img :src="items[index].image_url" :alt="items[index].title ?? ''" class="mx-auto h-32 w-32 rounded-full object-cover shadow-md">
                </template>

                <h3 x-show="items[index].title" x-text="items[index].title" class="mt-5 text-xl font-semibold text-gray-900"></h3>
                <p x-show="items[index].subtitle" x-text="items[index].subtitle" class="text-sm font-medium text-brand-600"></p>
                <p x-show="items[index].meta" x-text="items[index].meta" class="mt-1 text-xs text-gray-500"></p>
                <p x-show="items[index].body" x-text="items[index].body" class="mx-auto mt-4 max-w-lg whitespace-pre-line text-sm leading-relaxed text-gray-600"></p>

                <a
                    x-show="items[index].url"
                    :href="items[index].url"
                    target="_blank"
                    rel="noopener"
                    class="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-500"
                >
                    <x-icon name="link" class="h-4 w-4" />
                    {{ __('Profile') }}
                </a>
            </div>
        </template>
    </div>
</section>
