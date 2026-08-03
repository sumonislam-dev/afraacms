@props(['type'])

@php
    $data = banner($type);
@endphp

@if ($data)
    @if ($type === 'cta')
        <x-frontend.cta
            :heading="$data['title']"
            :subheading="$data['subtitle']"
            :button-text="$data['button_text']"
            :button-url="$data['button_url']"
        />
    @elseif ($type === 'popup')
        <div
            x-data="{
                open: false,
                key: 'banner-popup-dismissed-{{ $data['id'] }}',
                init() { this.open = ! localStorage.getItem(this.key); },
                dismiss() { this.open = false; localStorage.setItem(this.key, '1'); },
            }"
            x-show="open"
            x-transition
            style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >
            <div @click.outside="dismiss()" class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <button type="button" @click="dismiss()" aria-label="{{ __('Close') }}" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                    <x-icon name="x-mark" class="h-5 w-5" />
                </button>

                @if ($data['image_url'])
                    <img src="{{ $data['image_url'] }}" alt="" class="mb-4 w-full rounded-md object-cover" loading="lazy">
                @endif

                @if ($data['title'])
                    <h3 class="text-lg font-bold text-gray-900">{{ $data['title'] }}</h3>
                @endif

                @if ($data['subtitle'])
                    <p class="mt-2 text-sm text-gray-600">{{ $data['subtitle'] }}</p>
                @endif

                @if ($data['button_text'] && $data['button_url'])
                    <a href="{{ $data['button_url'] }}" class="mt-4 inline-block rounded-md bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        {{ $data['button_text'] }}
                    </a>
                @endif
            </div>
        </div>
    @else
        <x-frontend.banner
            :title="$data['title']"
            :subtitle="$data['subtitle']"
            :image-url="$data['image_url']"
            :button-text="$data['button_text']"
            :button-url="$data['button_url']"
        />
    @endif
@endif
