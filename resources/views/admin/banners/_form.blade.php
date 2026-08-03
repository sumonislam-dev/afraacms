@php
    $isEdit = isset($banner);
    $currentType = old('type', $isEdit ? $banner->type : ($type ?? array_key_first(config('banners.types', []))));
    $currentStartsAt = old('starts_at', $isEdit ? $banner->starts_at?->format('Y-m-d\TH:i') : '');
    $currentEndsAt = old('ends_at', $isEdit ? $banner->ends_at?->format('Y-m-d\TH:i') : '');
@endphp

<x-admin.card>
    <div class="space-y-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <x-input-label for="type" :value="__('Placement')" />
                <select id="type" name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach (config('banners.types', []) as $typeKey => $typeConfig)
                        <option value="{{ $typeKey }}" @selected($currentType === $typeKey)>{{ $typeConfig['label'] }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('type')" />
            </div>

            <div>
                <x-input-label for="title" :value="__('Title')" />
                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $banner->title ?? '')" />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="subtitle" :value="__('Subtitle')" />
                <x-text-input id="subtitle" name="subtitle" type="text" class="mt-1 block w-full" :value="old('subtitle', $banner->subtitle ?? '')" />
                <x-input-error class="mt-2" :messages="$errors->get('subtitle')" />
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-12">
            <div class="sm:col-span-3">
                <x-input-label for="button_text" :value="__('Button Text')" />
                <x-text-input id="button_text" name="button_text" type="text" class="mt-1 block w-full" :value="old('button_text', $banner->button_text ?? '')" />
                <x-input-error class="mt-2" :messages="$errors->get('button_text')" />
            </div>

            <div class="sm:col-span-6">
                <x-input-label for="button_url" :value="__('Button URL')" />
                <x-text-input id="button_url" name="button_url" type="text" class="mt-1 block w-full" :value="old('button_url', $banner->button_url ?? '')" />
                <x-input-error class="mt-2" :messages="$errors->get('button_url')" />
            </div>

            <div class="sm:col-span-3">
                <x-input-label :value="__('Image')" />
                <x-admin.media-picker name="image" :current="old('image', $banner->image ?? null)" />
                <x-input-error class="mt-2" :messages="$errors->get('image')" />
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <x-input-label for="starts_at" :value="__('Starts At')" />
                <x-text-input id="starts_at" name="starts_at" type="datetime-local" class="mt-1 block w-full" :value="$currentStartsAt" />
                <p class="mt-1 text-xs text-gray-500">{{ __('Blank = show immediately.') }}</p>
                <x-input-error class="mt-2" :messages="$errors->get('starts_at')" />
            </div>

            <div>
                <x-input-label for="ends_at" :value="__('Ends At')" />
                <x-text-input id="ends_at" name="ends_at" type="datetime-local" class="mt-1 block w-full" :value="$currentEndsAt" />
                <p class="mt-1 text-xs text-gray-500">{{ __('Blank = show indefinitely.') }}</p>
                <x-input-error class="mt-2" :messages="$errors->get('ends_at')" />
            </div>

            <div>
                <x-input-label for="sort_order" :value="__('Priority')" />
                <x-text-input id="sort_order" name="sort_order" type="number" class="mt-1 block w-full" :value="old('sort_order', $banner->sort_order ?? 0)" />
                <p class="mt-1 text-xs text-gray-500">{{ __('Lowest number wins - only one banner per placement shows at a time.') }}</p>
                <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
            </div>
        </div>

        @if ($isEdit)
            <div class="sm:max-w-xs">
                <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2">
                    <x-input-label :value="__('Active')" />
                    <x-admin.toggle name="is_active" :checked="old('is_active', $banner->is_active)" />
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
            </div>
        @endif
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-3">
            <x-secondary-button type="button" onclick="window.location='{{ route('admin.banners.index') }}'">{{ __('Cancel') }}</x-secondary-button>
            <x-primary-button>{{ $isEdit ? __('Update Banner') : __('Create Banner') }}</x-primary-button>
        </div>
    </x-slot>
</x-admin.card>
