@php
    $isEdit = isset($album);
@endphp

<x-admin.form-section
    :title="$isEdit ? __('Album Details') : __('New Album')"
    :description="__('The slug determines the album\'s public URL, e.g. /gallery/summer-fair.')"
>
    <div>
        <x-input-label for="title" :value="__('Title')" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $album->title ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('title')" />
    </div>

    <div>
        <x-input-label for="slug" :value="__('Slug')" />
        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $album->slug ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('slug')" />
    </div>

    <div>
        <x-input-label for="description" :value="__('Description')" />
        <x-textarea id="description" name="description" class="mt-1 block w-full" rows="3">{{ old('description', $album->description ?? '') }}</x-textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div>
        <x-input-label :value="__('Cover Image')" />
        <x-admin.media-picker name="cover_image" :current="old('cover_image', $album->cover_image ?? null)" />
        <x-input-error class="mt-2" :messages="$errors->get('cover_image')" />
    </div>

    @if ($isEdit)
        <div>
            <x-input-label :value="__('Active')" />
            <div class="mt-2">
                <x-admin.toggle name="is_active" :checked="old('is_active', $album->is_active)" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
        </div>
    @endif

    <x-slot name="actions">
        <x-secondary-button type="button" onclick="window.location='{{ route('admin.galleries.index') }}'">{{ __('Cancel') }}</x-secondary-button>
        <x-primary-button>{{ $isEdit ? __('Update Album') : __('Create Album') }}</x-primary-button>
    </x-slot>
</x-admin.form-section>
