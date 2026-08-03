@php
    $modalName = 'bulk-add-photos';
@endphp

<div
    x-data="{
        selected: [],
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
        isSelected(item) {
            return this.selected.some(s => s.id === item.id);
        },
        toggle(item) {
            const index = this.selected.findIndex(s => s.id === item.id);
            if (index === -1) {
                this.selected.push({ id: item.id, thumb_url: item.thumb_url, title: item.title, caption: '' });
            } else {
                this.selected.splice(index, 1);
            }
        },
        remove(id) {
            this.selected = this.selected.filter(s => s.id !== id);
        },
        async uploadMultiple(event) {
            const files = Array.from(event.target.files);
            if (! files.length) return;
            this.uploading = true;
            for (const file of files) {
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
                if (response.ok) {
                    const data = await response.json();
                    this.items.unshift(data.item);
                    this.toggle(data.item);
                }
            }
            this.uploading = false;
            event.target.value = '';
        },
    }"
>
    <x-secondary-button type="button" @click="loadItems(); $dispatch('open-modal', '{{ $modalName }}')">
        {{ __('Bulk Add Photos') }}
    </x-secondary-button>

    <x-modal :name="$modalName" max-width="2xl">
        <form method="POST" action="{{ route('admin.galleries.items.bulkStore', $album) }}" class="p-6">
            @csrf

            <div class="flex items-center justify-between gap-4">
                <h2 class="text-lg font-medium text-gray-900">{{ __('Bulk Add Photos') }}</h2>

                <label class="cursor-pointer text-sm font-medium text-indigo-600 hover:text-indigo-900">
                    <span x-show="! uploading">{{ __('Upload New') }}</span>
                    <span x-show="uploading" style="display: none;">{{ __('Uploading...') }}</span>
                    <input type="file" accept="image/*" multiple class="hidden" @change="uploadMultiple($event)" :disabled="uploading">
                </label>
            </div>

            <p class="mt-1 text-sm text-gray-500">{{ __('Select as many photos as you like, then optionally caption each one below.') }}</p>

            <div class="mt-4">
                <input
                    type="text"
                    x-model="search"
                    @input.debounce.400ms="loadItems()"
                    placeholder="{{ __('Search media...') }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
            </div>

            <div class="mt-4 grid max-h-72 grid-cols-4 gap-3 overflow-y-auto sm:grid-cols-5">
                <template x-if="loading">
                    <p class="col-span-full text-center text-sm text-gray-500">{{ __('Loading...') }}</p>
                </template>

                <template x-if="! loading && items.length === 0">
                    <p class="col-span-full text-center text-sm text-gray-500">{{ __('No media found.') }}</p>
                </template>

                <template x-for="item in items" :key="item.id">
                    <button
                        type="button"
                        @click="toggle(item)"
                        class="relative aspect-square overflow-hidden rounded-md border-2 border-transparent hover:border-indigo-300"
                        :class="{ 'border-indigo-600': isSelected(item) }"
                    >
                        <img :src="item.thumb_url" :alt="item.title" class="h-full w-full object-cover">
                        <span
                            x-show="isSelected(item)"
                            style="display: none;"
                            class="absolute right-1 top-1 flex h-5 w-5 items-center justify-center rounded-full bg-indigo-600 text-white"
                        >
                            <x-admin.icon name="check-circle" class="h-4 w-4" />
                        </span>
                    </button>
                </template>
            </div>

            <div class="mt-6" x-show="selected.length > 0" style="display: none;">
                <x-input-label :value="__('Selected Photos')" />
                <p class="mt-1 text-xs text-gray-500">{{ __('Add an optional caption for each - leave blank to skip.') }}</p>

                <div class="mt-2 max-h-64 space-y-2 overflow-y-auto">
                    <template x-for="(photo, index) in selected" :key="photo.id">
                        <div class="flex items-center gap-3 rounded-md border border-gray-200 p-2">
                            <img :src="photo.thumb_url" :alt="photo.title" class="h-12 w-12 flex-shrink-0 rounded object-cover">

                            <input
                                type="text"
                                x-model="photo.caption"
                                :name="`photos[${index}][caption]`"
                                placeholder="{{ __('Caption (optional)') }}"
                                class="block w-full flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            <input type="hidden" :name="`photos[${index}][id]`" :value="photo.id">

                            <button type="button" @click="remove(photo.id)" class="flex-shrink-0 rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-red-600">
                                <x-admin.icon name="x-mark" class="h-4 w-4" />
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    <span x-text="selected.length"></span> {{ __('selected') }}
                </p>

                <div class="flex gap-3">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                    <x-primary-button x-bind:disabled="selected.length === 0">
                        {{ __('Add Photos') }}
                    </x-primary-button>
                </div>
            </div>
        </form>
    </x-modal>
</div>
