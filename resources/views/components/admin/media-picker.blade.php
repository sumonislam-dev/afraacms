@props(['name', 'current' => null, 'disabled' => false, 'cropAspectRatio' => null, 'cropAspectExpression' => null])

@php
    $currentItem = $current ? \App\Models\MediaItem::find($current) : null;
    $modalName = 'media-picker-'.$name;
@endphp

<div
    x-data="{
        selectedId: @js($current ?: null),
        selectedThumb: @js($currentItem?->thumb_url),
        cropAspectRatio: @js($cropAspectRatio),
        rootEl: null,
        search: '',
        items: [],
        loading: false,
        uploading: false,
        cropping: false,
        cropSrc: null,
        cropObjectUrl: null,
        cropper: null,
        cropperReady: false,
        cropFailed: false,
        cropTimeout: null,
        savingCrop: false,
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
        // Existing-library pick: crop first if this field requires a fixed
        // ratio, otherwise select immediately (unchanged prior behavior).
        chooseExisting(item) {
            if (this.cropAspectRatio) {
                this.beginCrop(item.file_url);
            } else {
                this.select(item);
                this.$dispatch('close');
            }
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
        // Fresh upload: crop locally in the browser before anything is ever
        // sent to the server, so no uncropped duplicate lands in the Library.
        handleFileInput(event) {
            const file = event.target.files[0];
            if (! file) return;
            if (this.cropAspectRatio) {
                this.cropObjectUrl = URL.createObjectURL(file);
                this.beginCrop(this.cropObjectUrl);
                event.target.value = '';
            } else {
                this.upload(event);
            }
        },
        beginCrop(src) {
            // Retry re-calls beginCrop() on the same img element, which
            // Cropper.js otherwise treats as a no-op if it's still attached
            // to a dead prior instance - destroy it first so a new one can
            // actually attach and load again.
            if (this.cropper) {
                this.cropper.destroy();
                this.cropper = null;
            }
            clearTimeout(this.cropTimeout);
            this.cropSrc = src;
            this.cropping = true;
            this.cropperReady = false;
            this.cropFailed = false;
            this.$nextTick(() => {
                // Not this.$refs.cropImage: the modal component wraps its
                // slot in its own separate x-data, so a ref inside it is
                // invisible to this component's $refs. Not this.$el either:
                // called from a click/change handler on a nested element,
                // $el resolves to THAT element here, not the component root.
                // rootEl is captured once via x-init, so it's always right.
                const image = this.rootEl.querySelector('[x-ref=cropImage]');
                // Cropper.js only builds its crop-box (and only then can
                // getCroppedCanvas() return anything but null) once the
                // source image has actually finished loading - on a slow
                // connection or a large original photo that can take a
                // couple of seconds, so cropperReady is tracked explicitly
                // and the Use This Crop button stays disabled until it fires.
                image.addEventListener('ready', () => {
                    this.cropperReady = true;
                    clearTimeout(this.cropTimeout);
                });
                image.addEventListener('error', () => {
                    this.cropFailed = true;
                    clearTimeout(this.cropTimeout);
                });
                // Belt-and-suspenders: if neither event ever fires (e.g. a
                // network hiccup or a browser-specific hang while Cropper
                // re-fetches the image to read its EXIF orientation), surface
                // an error instead of leaving the loading state stuck forever.
                this.cropTimeout = setTimeout(() => {
                    if (! this.cropperReady) this.cropFailed = true;
                }, 15000);
                this.cropper = new Cropper(image, {
                    aspectRatio: this.cropAspectRatio,
                    viewMode: 1,
                    autoCropArea: 1,
                    // Cropper.js otherwise re-fetches the same image over a
                    // second, separate request purely to read old-style EXIF
                    // rotation for browsers that don't already auto-rotate
                    // photos - modern Chrome/Edge/Firefox/Safari all do, and
                    // on PHP's single-threaded local dev server that second
                    // request can queue for a long time behind the first,
                    // which read as the whole crop UI being stuck forever.
                    checkOrientation: false,
                });
            });
        },
        cancelCrop() {
            clearTimeout(this.cropTimeout);
            if (this.cropper) {
                this.cropper.destroy();
                this.cropper = null;
            }
            if (this.cropObjectUrl) {
                URL.revokeObjectURL(this.cropObjectUrl);
                this.cropObjectUrl = null;
            }
            this.cropping = false;
            this.cropSrc = null;
            this.cropperReady = false;
            this.cropFailed = false;
        },
        async saveCrop() {
            if (! this.cropper || ! this.cropperReady) return;
            this.savingCrop = true;
            const canvas = this.cropper.getCroppedCanvas();
            if (! canvas) {
                this.savingCrop = false;
                return;
            }
            canvas.toBlob(async (blob) => {
                const formData = new FormData();
                formData.append('file', blob, 'cropped.jpg');
                const response = await fetch('{{ route('admin.media.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: formData,
                });
                this.savingCrop = false;
                if (response.ok) {
                    const data = await response.json();
                    this.select(data.item);
                    this.cancelCrop();
                    this.$dispatch('close');
                }
            }, 'image/jpeg', 0.92);
        },
    }"
    x-init="rootEl = $el"
    @if ($cropAspectExpression)
        x-effect="cropAspectRatio = ({{ $cropAspectExpression }})"
    @endif
>
    <input type="hidden" name="{{ $name }}" x-bind:value="selectedId">

    <div class="mt-1 flex items-center gap-4">
        <template x-if="selectedThumb">
            <img :src="selectedThumb" alt="" class="h-16 w-16 shrink-0 rounded-md border border-gray-200 bg-white object-contain">
        </template>
        <template x-if="! selectedThumb">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-md border border-dashed border-gray-300 text-gray-300">
                <x-admin.icon name="photo" class="h-6 w-6" />
            </div>
        </template>

        <div class="flex items-center gap-3">
            <x-secondary-button
                type="button"
                :disabled="$disabled"
                @click="cancelCrop(); loadItems(); $dispatch('open-modal', '{{ $modalName }}')"
            >
                <span x-text="selectedId ? @js(__('Replace Image')) : @js(__('Select Image'))"></span>
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
            <template x-if="! cropping">
                <div>
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-lg font-medium text-gray-900">{{ __('Media Library') }}</h2>

                        <label class="cursor-pointer text-sm font-medium text-indigo-600 hover:text-indigo-900">
                            <span x-show="! uploading">{{ __('Upload New') }}</span>
                            <span x-show="uploading" style="display: none;">{{ __('Uploading...') }}</span>
                            <input type="file" accept="image/*" class="hidden" @change="handleFileInput($event)" :disabled="uploading">
                        </label>
                    </div>

                    @if ($cropAspectRatio)
                        <p class="mt-2 text-xs text-gray-500">{{ __("Whatever you pick or upload, you'll crop it to fit this banner's shape next.") }}</p>
                    @endif

                    <div class="mt-4">
                        <input
                            type="text"
                            x-model="search"
                            @input.debounce.400ms="loadItems()"
                            placeholder="{{ __('Search media...') }}"
                            class="block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
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
                                @click="chooseExisting(item)"
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
            </template>

            <template x-if="cropping">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">{{ __('Adjust Crop') }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Drag and resize the box to frame the image, then save.') }}</p>

                    <div class="relative mt-4 min-h-64 max-h-[60vh] overflow-hidden bg-gray-900">
                        <img x-ref="cropImage" :src="cropSrc" class="block max-w-full" style="max-height: 60vh;">
                        <div x-show="! cropperReady && ! cropFailed" class="absolute inset-0 flex items-center justify-center text-sm text-gray-300">
                            {{ __('Loading image...') }}
                        </div>
                        <div x-show="cropFailed" class="absolute inset-0 flex flex-col items-center justify-center gap-2 bg-gray-900 px-6 text-center text-sm text-red-300" style="display: none;">
                            <span>{{ __("This image didn't load in time. Check your connection and try again.") }}</span>
                            <button type="button" @click="beginCrop(cropSrc)" class="font-medium text-white underline">{{ __('Retry') }}</button>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <x-secondary-button type="button" @click="cancelCrop()">{{ __('Cancel') }}</x-secondary-button>
                        <x-primary-button type="button" @click="saveCrop()" x-bind:disabled="savingCrop || ! cropperReady">
                            <span x-show="! savingCrop && cropperReady" style="display: none;">{{ __('Use This Crop') }}</span>
                            <span x-show="! cropperReady">{{ __('Loading...') }}</span>
                            <span x-show="savingCrop" style="display: none;">{{ __('Saving...') }}</span>
                        </x-primary-button>
                    </div>
                </div>
            </template>
        </div>
    </x-modal>
</div>
