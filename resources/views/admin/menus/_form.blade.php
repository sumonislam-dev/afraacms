@php
    $isEdit = isset($menu);
    $slugTouched = $isEdit || old('slug') ? 'true' : 'false';
@endphp

<div
    x-data="{
        slugTouched: {{ $slugTouched }},
        slugify(value) {
            return value.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        },
    }"
    class="space-y-6"
>
    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input
            id="name"
            name="name"
            type="text"
            class="mt-1 block w-full"
            :value="old('name', $menu->name ?? '')"
            required
            autofocus
            x-on:input="if (! slugTouched) $refs.slug.value = slugify($event.target.value)"
        />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="slug" :value="__('Slug')" />
        <x-text-input
            id="slug"
            name="slug"
            type="text"
            class="mt-1 block w-full"
            :value="old('slug', $menu->slug ?? '')"
            required
            x-ref="slug"
            x-on:input="slugTouched = true"
        />
        <p class="mt-1 text-sm text-gray-500">{{ __('Auto-generated from the name - edit it directly if you need something different. Templates reference this menu by its slug, e.g. menu(\'header\').') }}</p>
        <x-input-error class="mt-2" :messages="$errors->get('slug')" />
    </div>
</div>
