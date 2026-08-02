@php
    $isEdit = isset($menu);
@endphp

<x-admin.form-section
    :title="$isEdit ? __('Menu Details') : __('New Menu')"
    :description="__('The slug is how templates reference this menu, e.g. menu(\'header\').')"
>
    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $menu->name ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="slug" :value="__('Slug')" />
        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $menu->slug ?? '')" required />
        <p class="mt-1 text-sm text-gray-500">{{ __('Lowercase letters, numbers, dashes and underscores only.') }}</p>
        <x-input-error class="mt-2" :messages="$errors->get('slug')" />
    </div>

    <x-slot name="actions">
        <x-secondary-button type="button" onclick="window.location='{{ route('admin.menus.index') }}'">{{ __('Cancel') }}</x-secondary-button>
        <x-primary-button>{{ $isEdit ? __('Save') : __('Create Menu') }}</x-primary-button>
    </x-slot>
</x-admin.form-section>
