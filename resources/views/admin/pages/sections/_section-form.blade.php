@php
    $isEdit = isset($section);
    $currentType = old('type', $section->type ?? '');
    $typesConfig = config('sections.types', []);
@endphp

<div x-data="{ type: @js($currentType), fields: @js(collect($typesConfig)->map(fn ($t) => $t['fields'])->all()) }">
    <x-admin.form-section
        :title="$isEdit ? __('Section Details') : __('New Section')"
        :description="__('Choose a type, then fill in the fields that type uses.')"
    >
        <div>
            <x-input-label for="type" :value="__('Type')" />
            <select id="type" name="type" x-model="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">{{ __('Select a type') }}</option>
                @foreach ($typesConfig as $typeKey => $typeMeta)
                    <option value="{{ $typeKey }}" @selected($currentType === $typeKey)>{{ $typeMeta['label'] }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('type')" />
        </div>

        <div x-show="fields[type]?.includes('heading')" style="display: none;">
            <x-input-label for="heading" :value="__('Heading')" />
            <x-text-input id="heading" name="heading" type="text" class="mt-1 block w-full" :value="old('heading', $section->heading ?? '')" />
            <x-input-error class="mt-2" :messages="$errors->get('heading')" />
        </div>

        <div x-show="fields[type]?.includes('subheading')" style="display: none;">
            <x-input-label for="subheading" :value="__('Subheading')" />
            <x-text-input id="subheading" name="subheading" type="text" class="mt-1 block w-full" :value="old('subheading', $section->subheading ?? '')" />
            <x-input-error class="mt-2" :messages="$errors->get('subheading')" />
        </div>

        <div x-show="fields[type]?.includes('body')" style="display: none;">
            <x-input-label for="body" :value="__('Content')" />
            <x-textarea id="body" name="body" class="mt-1 block w-full" rows="5">{{ old('body', $section->body ?? '') }}</x-textarea>
            <x-input-error class="mt-2" :messages="$errors->get('body')" />
        </div>

        <div x-show="fields[type]?.includes('image')" style="display: none;">
            <x-input-label :value="__('Image')" />
            <x-admin.media-picker name="image" :current="old('image', $section->image ?? null)" />
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2" x-show="fields[type]?.includes('button_text')" style="display: none;">
            <div>
                <x-input-label for="button_text" :value="__('Button Text')" />
                <x-text-input id="button_text" name="button_text" type="text" class="mt-1 block w-full" :value="old('button_text', $section->button_text ?? '')" />
                <x-input-error class="mt-2" :messages="$errors->get('button_text')" />
            </div>
            <div>
                <x-input-label for="button_url" :value="__('Button URL')" />
                <x-text-input id="button_url" name="button_url" type="text" class="mt-1 block w-full" :value="old('button_url', $section->button_url ?? '')" />
                <x-input-error class="mt-2" :messages="$errors->get('button_url')" />
            </div>
        </div>

        <div x-show="fields[type]?.includes('layout')" style="display: none;">
            <x-input-label for="layout" :value="__('Layout')" />
            <select id="layout" name="layout" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="image-left" @selected(old('layout', $section->layout ?? '') === 'image-left')>{{ __('Image Left') }}</option>
                <option value="image-right" @selected(old('layout', $section->layout ?? '') === 'image-right')>{{ __('Image Right') }}</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('layout')" />
        </div>

        @if ($isEdit)
            <div>
                <x-input-label :value="__('Visible')" />
                <div class="mt-1">
                    <x-admin.toggle name="is_active" :checked="(bool) old('is_active', $section->is_active ?? true)" />
                </div>
            </div>
        @endif

        <x-slot name="actions">
            <x-secondary-button type="button" onclick="window.location='{{ route('admin.pages.sections.index', $page) }}'">{{ __('Cancel') }}</x-secondary-button>
            <x-primary-button>{{ $isEdit ? __('Update Section') : __('Create Section') }}</x-primary-button>
        </x-slot>
    </x-admin.form-section>
</div>
