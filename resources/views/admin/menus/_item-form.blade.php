@php
    $isEdit = isset($item);
    $prefix = $isEdit ? 'edit-'.$item->id.'-' : 'new-';
    $currentType = old('type', $item->type ?? 'internal');
@endphp

<div>
    <x-input-label for="{{ $prefix }}label" :value="__('Label')" />
    <x-text-input id="{{ $prefix }}label" name="label" type="text" class="mt-1 block w-full" :value="old('label', $item->label ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('label')" />
</div>

<div class="mt-4 flex items-center justify-between">
    <div>
        <x-input-label :value="__('Link Type')" />
        <div class="mt-1 flex gap-4">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="radio" name="type" value="internal" @checked($currentType === 'internal') class="text-indigo-600 focus:ring-indigo-500">
                {{ __('Internal') }}
            </label>
            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="radio" name="type" value="external" @checked($currentType === 'external') class="text-indigo-600 focus:ring-indigo-500">
                {{ __('External') }}
            </label>
        </div>
    </div>

    <div>
        <x-input-label :value="__('Visible')" />
        <div class="mt-1">
            <x-admin.toggle name="is_active" :checked="(bool) old('is_active', $item->is_active ?? true)" />
        </div>
    </div>
</div>

<div class="mt-4">
    <x-input-label for="{{ $prefix }}url" :value="__('URL')" />
    <x-text-input
        id="{{ $prefix }}url"
        name="url"
        type="text"
        class="mt-1 block w-full"
        :value="old('url', $item->url ?? '')"
        required
        placeholder="/about or https://example.com"
    />
    <x-input-error class="mt-2" :messages="$errors->get('url')" />
</div>

<div class="mt-4">
    <x-input-label for="{{ $prefix }}icon" :value="__('Icon')" />
    <select id="{{ $prefix }}icon" name="icon" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">{{ __('None') }}</option>
        @foreach (array_keys(config('icons', [])) as $iconName)
            <option value="{{ $iconName }}" @selected(old('icon', $item->icon ?? '') === $iconName)>
                {{ ucwords(str_replace('-', ' ', $iconName)) }}
            </option>
        @endforeach
    </select>
</div>
