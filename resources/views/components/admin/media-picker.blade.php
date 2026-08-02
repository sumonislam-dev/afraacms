@props(['name', 'current' => null, 'disabled' => false])

@php
    $currentItem = $current ? \App\Models\MediaItem::find($current) : null;
    $modalName = 'media-picker-'.$name;
@endphp

<div
    x-data="{
        selectedId: @js($current ?: null),
        selectedThumb: @js($currentItem?->thumb_url),
        search: '',
        items: [],
        loading: false,
        uploading: false,
        async loadItems() {
            this.loading = true;
            const response = await fetch(`{{ route('admin.media.index') }}?search=${encodeURIComponent(this.search)}`, {
                headers: { 'Accept': 'application/json' },
            });
            const data = await response.json();
            this.items = data.items.data;
            this.loading = false;
        },
        select(item) {
            this.selectedId = item.id;
            this.selectedThumb = item.thumb_url;
        },
        clear() {
            this.selectedId = null;
            this.selectedThumb = null;
        },
        async upload(event) {
            const file = event.target.files[0];
            if (! file) return;
            this.uploading = true;
            const formData = new FormData();
            formData.append('file', file);
            const response = await fetch('{{ route('admin.media.store') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: formData,
            });
            this.uploading = false;
            event.target.value = '';
            if (response.ok) {
                const data = await response.json();
                this.items.unshift(data.item);
                this.select(data.item);
            }
        },
    }"
>
    <input type="hidden" name="{{ $name }}" x-bind:value="selectedId">

    <div class="mt-1 flex items-center gap-4">
        <template x-if="selectedThumb">
            <img :src="selectedThumb" alt="" class="h-16 w-16 flex-shrink-0 rounded-md border border-gray-200 bg-white object-contain">
        </template>
        <template x-if="! selectedThumb">
            <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-md border border-dashed border-gray-300 text-gray-300">
                <x-admin.icon name="photo" class="h-6 w-6" />
            </div>
        </template>

        <div class="flex items-center gap-3">
            <x-secondary-button
                type="button"
                :disabled="$disabled"
                @click="loadItems(); $dispatch('open-modal', '{{ $modalName }}')"
            >
                {{ __('Select Image') }}
            </x-secondary-button>

            <button
                type="button"
                x-show="selectedId"
                @click="clear()"
                @disabled($disabled)
                class="text-sm font-medium text-red-600 hover:text-red-900"
                style="display: none;"
            >
                {{ __('Remove') }}
            </button>
        </div>
    </div>

    <x-modal :name="$modalName">
        <div class="p-6">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-lg font-medium text-gray-900">{{ __('Media Library') }}</h2>

                <label class="cursor-pointer text-sm font-medium text-indigo-600 hover:text-indigo-900">
                    <span x-show="! uploading">{{ __('Upload New') }}</span>
                    <span x-show="uploading" style="display: none;">{{ __('Uploading...') }}</span>
                    <input type="file" accept="image/*" class="hidden" @change="upload($event)" :disabled="uploading">
                </label>
            </div>

            <div class="mt-4">
                <input
                    type="text"
                    x-model="search"
                    @input.debounce.400ms="loadItems()"
                    placeholder="{{ __('Search media...') }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
            </div>

            <div class="mt-4 grid max-h-96 grid-cols-4 gap-3 overflow-y-auto sm:grid-cols-5">
                <template x-if="loading">
                    <p class="col-span-full text-center text-sm text-gray-500">{{ __('Loading...') }}</p>
                </template>

                <template x-if="! loading && items.length === 0">
                    <p class="col-span-full text-center text-sm text-gray-500">{{ __('No media found.') }}</p>
                </template>

                <template x-for="item in items" :key="item.id">
                    <button
                        type="button"
                        @click="select(item); $dispatch('close')"
                        class="aspect-square overflow-hidden rounded-md border-2 border-transparent hover:border-indigo-500"
                        :class="{ 'border-indigo-600': selectedId === item.id }"
                    >
                        <img :src="item.thumb_url" :alt="item.title" class="h-full w-full object-cover">
                    </button>
                </template>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Close') }}</x-secondary-button>
            </div>
        </div>
    </x-modal>
</div>
