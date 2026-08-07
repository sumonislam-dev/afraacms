@php
    $isEdit = isset($album);
@endphp

<x-admin.card>
    <div
        x-data="{
            slugTouched: {{ $isEdit || old('slug') ? 'true' : 'false' }},
            slugify(value) {
                return value.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            },
        }"
        class="space-y-4"
    >
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="title" :value="__('Title')" />
                <x-text-input
                    id="title"
                    name="title"
                    type="text"
                    class="mt-1 block w-full"
                    :value="old('title', $album->title ?? '')"
                    required
                    autofocus
                    x-on:input="if (! slugTouched) $refs.slug.value = slugify($event.target.value)"
                />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="slug" :value="__('Slug')" />
                <x-text-input
                    id="slug"
                    name="slug"
                    type="text"
                    class="mt-1 block w-full"
                    :value="old('slug', $album->slug ?? '')"
                    required
                    x-ref="slug"
                    x-on:input="slugTouched = true"
                />
                <p class="mt-1 text-xs text-gray-500">{{ __('Public URL, e.g. /gallery/summer-fair') }}</p>
                <x-input-error class="mt-2" :messages="$errors->get('slug')" />
            </div>
        </div>

        <div>
            <x-input-label for="description" :value="__('Description')" />
            <x-textarea id="description" name="description" class="mt-1 block w-full" rows="3">{{ old('description', $album->description ?? '') }}</x-textarea>
            <x-input-error class="mt-2" :messages="$errors->get('description')" />
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <x-input-label :value="__('Cover Image')" />
                <x-admin.media-picker name="cover_image" :current="old('cover_image', $album->cover_image ?? null)" />
                <x-input-error class="mt-2" :messages="$errors->get('cover_image')" />
            </div>

            @if ($isEdit)
                <div>
                    <x-input-label :value="__('Active')" />
                    <div class="mt-1 flex items-center rounded-md border border-gray-200 px-3 py-2 sm:max-w-xs">
                        <x-admin.toggle name="is_active" :checked="old('is_active', $album->is_active)" />
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Turn off to fully disable this album everywhere, including Hero backgrounds and Photo Sliders that use it.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                </div>

                <div>
                    <x-input-label :value="__('Show in Public Gallery')" />
                    <div class="mt-1 flex items-center rounded-md border border-gray-200 px-3 py-2 sm:max-w-xs">
                        <x-admin.toggle name="is_public" :checked="old('is_public', $album->is_public)" />
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Turn off if this album is only a photo source for a Hero background or Photo Slider section, and shouldn\'t appear on the /gallery page or get its own public URL.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('is_public')" />
                </div>
            @endif
        </div>

        @include('admin._seo-fields', ['seoable' => $album ?? null])
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-3">
            <x-secondary-button type="button" onclick="window.location='{{ route('admin.galleries.index') }}'">{{ __('Cancel') }}</x-secondary-button>
            <x-primary-button>{{ $isEdit ? __('Update Album') : __('Create Album') }}</x-primary-button>
        </div>
    </x-slot>
</x-admin.card>
