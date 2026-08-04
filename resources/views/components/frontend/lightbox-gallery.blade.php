@props(['items' => []])

<div
    x-data="{
        open: false,
        index: 0,
        items: @js(collect($items)->values()->all()),
        show(i) { this.index = i; this.open = true; },
        next() { this.index = (this.index + 1) % this.items.length; },
        prev() { this.index = (this.index - 1 + this.items.length) % this.items.length; },
    }"
    @keydown.escape.window="open = false"
    @keydown.arrow-right.window="if (open) next()"
    @keydown.arrow-left.window="if (open) prev()"
>
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
        @foreach ($items as $index => $item)
            <button type="button" @click="show({{ $index }})" class="group relative block aspect-square w-full cursor-zoom-in overflow-hidden rounded-lg bg-gray-100">
                @if ($item['type'] === 'image' && $item['image_url'])
                    <img src="{{ $item['image_url'] }}" alt="{{ $item['caption'] ?? '' }}" class="h-full w-full object-cover transition group-hover:opacity-90" loading="lazy">
                @else
                    <div class="flex h-full w-full items-center justify-center bg-gray-800">
                        <x-icon name="play" class="h-10 w-10 text-white" />
                    </div>
                @endif

                @if ($item['caption'] ?? null)
                    <span class="absolute inset-x-0 bottom-0 truncate bg-black/50 px-2 py-1 text-xs text-white">{{ $item['caption'] }}</span>
                @endif
            </button>
        @endforeach
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
            <div class="max-h-full max-w-4xl">
                <template x-if="items[index].type === 'image'">
                    <img :src="items[index].image_url" :alt="items[index].caption ?? ''" class="max-h-[80vh] w-full rounded-sm object-contain">
                </template>

                <template x-if="items[index].type === 'video'">
                    <div class="aspect-video w-full max-w-3xl">
                        <iframe :src="items[index].embed_url" class="h-full w-full rounded-sm" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                    </div>
                </template>

                <p x-show="items[index].caption" x-text="items[index].caption" class="mt-3 text-center text-sm text-gray-300"></p>
            </div>
        </template>
    </div>
</div>
