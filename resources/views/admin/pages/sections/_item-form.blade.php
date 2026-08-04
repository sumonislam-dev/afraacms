@php
    $isEdit = isset($item);
    $prefix = $isEdit ? 'edit-item-'.$item->id.'-' : 'new-item-';
    $itemLabels = $itemLabels ?? [];
@endphp

@if (in_array('title', $itemFields))
    <div class="mb-4">
        <x-input-label for="{{ $prefix }}title" :value="$itemLabels['title'] ?? __('Title')" />
        <x-text-input id="{{ $prefix }}title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $item->title ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('title')" />
    </div>
@endif

@if (in_array('subtitle', $itemFields))
    <div class="mb-4">
        <x-input-label for="{{ $prefix }}subtitle" :value="$itemLabels['subtitle'] ?? __('Subtitle')" />
        <x-text-input id="{{ $prefix }}subtitle" name="subtitle" type="text" class="mt-1 block w-full" :value="old('subtitle', $item->subtitle ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('subtitle')" />
    </div>
@endif

@if (in_array('body', $itemFields))
    <div class="mb-4">
        <x-input-label for="{{ $prefix }}body" :value="$itemLabels['body'] ?? __('Content')" />
        <x-textarea id="{{ $prefix }}body" name="body" class="mt-1 block w-full" rows="3">{{ old('body', $item->body ?? '') }}</x-textarea>
        <x-input-error class="mt-2" :messages="$errors->get('body')" />
    </div>
@endif

@if (in_array('image', $itemFields))
    <div class="mb-4">
        <x-input-label :value="$itemLabels['image'] ?? __('Image')" />
        <x-admin.media-picker name="image" :current="old('image', $item->image ?? null)" />
    </div>
@endif

@if (in_array('value', $itemFields))
    <div class="mb-4">
        <x-input-label for="{{ $prefix }}value" :value="$itemLabels['value'] ?? __('Value')" />
        <x-text-input id="{{ $prefix }}value" name="value" type="text" class="mt-1 block w-full" :value="old('value', $item->value ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('value')" />
    </div>
@endif

@if (in_array('url', $itemFields))
    <div class="mb-4">
        <x-input-label for="{{ $prefix }}url" :value="$itemLabels['url'] ?? __('URL')" />
        <x-text-input id="{{ $prefix }}url" name="url" type="text" class="mt-1 block w-full" :value="old('url', $item->url ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('url')" />
    </div>
@endif

@if (in_array('icon', $itemFields))
    <div class="mb-4">
        <x-input-label for="{{ $prefix }}icon" :value="__('Icon')" />
        <select id="{{ $prefix }}icon" name="icon" class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">{{ __('None') }}</option>
            @foreach (array_keys(config('icons', [])) as $iconName)
                <option value="{{ $iconName }}" @selected(old('icon', $item->icon ?? '') === $iconName)>
                    {{ ucwords(str_replace('-', ' ', $iconName)) }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('icon')" />
    </div>
@endif
