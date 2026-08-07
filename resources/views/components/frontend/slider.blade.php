@props([
    'items' => [],
    'autoplay' => true,
    'interval' => 5000,
])

@if (count($items))
    <div
        {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-lg']) }}
        x-data="{
            active: 0,
            count: {{ count($items) }},
            timer: null,
            next() { this.active = (this.active + 1) % this.count; },
            prev() { this.active = (this.active - 1 + this.count) % this.count; },
            goTo(i) { this.active = i; },
            start() {
                if (! {{ $autoplay ? 'true' : 'false' }} || this.count < 2) return;
                this.timer = setInterval(() => this.next(), {{ (int) $interval }});
            },
        }"
        x-init="start()"
    >
        @foreach ($items as $index => $item)
            <div x-show="active === {{ $index }}" x-transition.opacity.duration.500ms class="relative">
                @if ($item['image_url'] ?? $item['imageUrl'] ?? null)
                    <img
                        src="{{ $item['image_url'] ?? $item['imageUrl'] }}"
                        alt="{{ $item['title'] ?? '' }}"
                        class="aspect-video w-full object-cover"
                        @if ($index > 0) loading="lazy" @endif
                    >
                @endif

                @if (($item['title'] ?? null) || ($item['body'] ?? null))
                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/40 px-6 text-center text-white">
                        @if ($item['title'] ?? null)
                            <h3 class="text-2xl font-bold">{{ $item['title'] }}</h3>
                        @endif

                        @if ($item['body'] ?? null)
                            <p class="mt-2">{{ $item['body'] }}</p>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach

        @if (count($items) > 1)
            <button type="button" @click="prev()" aria-label="{{ __('Previous slide') }}" class="absolute left-2 top-1/2 -translate-y-1/2 cursor-pointer rounded-full bg-white/80 p-2 text-gray-700 hover:bg-white">
                <x-icon name="chevron-right" class="rotate-180" />
            </button>

            <button type="button" @click="next()" aria-label="{{ __('Next slide') }}" class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer rounded-full bg-white/80 p-2 text-gray-700 hover:bg-white">
                <x-icon name="chevron-right" class="h-5 w-5" />
            </button>

            <div class="absolute bottom-3 left-1/2 flex -translate-x-1/2 gap-2">
                @foreach ($items as $index => $item)
                    <button
                        type="button"
                        @click="goTo({{ $index }})"
                        :class="active === {{ $index }} ? 'bg-white' : 'bg-white/50'"
                        class="h-2 w-2 cursor-pointer rounded-full"
                        aria-label="{{ __('Go to slide') }} {{ $index + 1 }}"
                    ></button>
                @endforeach
            </div>
        @endif
    </div>
@endif
