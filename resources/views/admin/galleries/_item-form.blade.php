@php
    $isEdit = isset($item);
    $prefix = $isEdit ? 'edit-item-'.$item->id.'-' : 'new-item-';
    $currentType = old('type', $item->type ?? 'image');
@endphp

<div x-data="{ type: '{{ $currentType }}' }">
    <div class="mb-4">
        <x-input-label for="{{ $prefix }}type" :value="__('Type')" />
        <select id="{{ $prefix }}type" name="type" x-model="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="image" @selected($currentType === 'image')>{{ __('Photo') }}</option>
            <option value="video" @selected($currentType === 'video')>{{ __('Video') }}</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('type')" />
    </div>

    <div class="mb-4" x-show="type === 'image'" style="display: none;">
        <x-input-label :value="__('Photo')" />
        <x-admin.media-picker name="image" :current="old('image', $item->image ?? null)" />
        <x-input-error class="mt-2" :messages="$errors->get('image')" />
    </div>

    <div class="mb-4" x-show="type === 'video'" style="display: none;">
        <x-input-label for="{{ $prefix }}video_url" :value="__('Video URL')" />
        <x-text-input id="{{ $prefix }}video_url" name="video_url" type="text" class="mt-1 block w-full" :value="old('video_url', $item->video_url ?? '')" />
        <p class="mt-1 text-sm text-gray-500">{{ __('A YouTube/Vimeo link or a direct video file URL.') }}</p>
        <x-input-error class="mt-2" :messages="$errors->get('video_url')" />
    </div>

    <div class="mb-4">
        <x-input-label for="{{ $prefix }}caption" :value="__('Caption')" />
        <x-text-input id="{{ $prefix }}caption" name="caption" type="text" class="mt-1 block w-full" :value="old('caption', $item->caption ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('caption')" />
    </div>
</div>
