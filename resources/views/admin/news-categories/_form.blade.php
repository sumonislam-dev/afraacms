@php
    $isEdit = isset($category);
    $slugTouched = $isEdit || old('slug') ? 'true' : 'false';
@endphp

<div
    x-data="{
        slugTouched: {{ $slugTouched }},
        slugify(value) {
            return value.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        },
    }"
    class="space-y-4"
>
    <div>
        <x-input-label for="{{ $prefix ?? '' }}name" :value="__('Name')" />
        <x-text-input
            id="{{ $prefix ?? '' }}name"
            name="name"
            type="text"
            class="mt-1 block w-full"
            :value="old('name', $category->name ?? '')"
            required
            autofocus
            x-on:input="if (! slugTouched) $refs.slug.value = slugify($event.target.value)"
        />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="{{ $prefix ?? '' }}slug" :value="__('Slug')" />
        <x-text-input
            id="{{ $prefix ?? '' }}slug"
            name="slug"
            type="text"
            class="mt-1 block w-full"
            :value="old('slug', $category->slug ?? '')"
            required
            x-ref="slug"
            x-on:input="slugTouched = true"
        />
        <p class="mt-1 text-xs text-gray-500">{{ __('Used to filter news, e.g. /news?category=events') }}</p>
        <x-input-error class="mt-2" :messages="$errors->get('slug')" />
    </div>
</div>
