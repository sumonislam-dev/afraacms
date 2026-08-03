@php
    $isEdit = isset($category);
@endphp

<x-admin.form-section
    :title="$isEdit ? __('Category Details') : __('New Category')"
    :description="__('The slug is used to filter projects by category, e.g. /projects?category=web-design.')"
>
    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $category->name ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="slug" :value="__('Slug')" />
        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $category->slug ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('slug')" />
    </div>

    <x-slot name="actions">
        <x-secondary-button type="button" onclick="window.location='{{ route('admin.project-categories.index') }}'">{{ __('Cancel') }}</x-secondary-button>
        <x-primary-button>{{ $isEdit ? __('Save') : __('Create Category') }}</x-primary-button>
    </x-slot>
</x-admin.form-section>
