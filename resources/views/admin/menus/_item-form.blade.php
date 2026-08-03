@php
    $isEdit = isset($item);
    $prefix = $isEdit ? 'edit-'.$item->id.'-' : 'new-';
    $currentType = old('type', $item->type ?? 'internal');
    $currentUrl = old('url', $item->url ?? '');
    $pageOptions = $pageOptions ?? [];
    $newTabDefault = old('open_in_new_tab', $item->open_in_new_tab ?? ($currentType === 'external'));

    $currentParentId = old('parent_id', $item->parent_id ?? '');
    $excludedParentIds = $isEdit ? [$item->id, ...$item->descendantIds()] : [];
@endphp

<div
    x-data="{
        type: @js($currentType),
        url: @js($currentUrl),
        label: @js(old('label', $item->label ?? '')),
        labelTouched: @js($isEdit || old('label') ? true : false),
    }"
    class="space-y-4"
>
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="sm:col-span-2">
            <x-input-label for="{{ $prefix }}label" :value="__('Label')" />
            <x-text-input
                id="{{ $prefix }}label"
                name="label"
                type="text"
                class="mt-1 block w-full"
                x-model="label"
                x-on:input="labelTouched = true"
                required
            />
            <x-input-error class="mt-2" :messages="$errors->get('label')" />
        </div>

        <div>
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
    </div>

    <div class="grid gap-4 sm:grid-cols-2 rounded-md border border-gray-200 bg-gray-50 p-3">
        <div>
            <x-input-label :value="__('Link Type')" />
            <div class="mt-1 inline-flex rounded-md border border-gray-300 bg-white p-0.5 text-sm">
                <label class="cursor-pointer rounded px-3 py-1 font-medium transition-colors" :class="type === 'internal' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50'">
                    <input type="radio" name="type" value="internal" x-model="type" class="sr-only">
                    {{ __('Internal') }}
                </label>
                <label class="cursor-pointer rounded px-3 py-1 font-medium transition-colors" :class="type === 'external' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50'">
                    <input type="radio" name="type" value="external" x-model="type" class="sr-only">
                    {{ __('External') }}
                </label>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('type')" />
        </div>

        <div>
            <x-input-label for="{{ $prefix }}parent_id" :value="__('Parent Item')" />
            <select id="{{ $prefix }}parent_id" name="parent_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">{{ __('— Top Level —') }}</option>
                @foreach ($parentOptions ?? [] as $id => $label)
                    @continue(in_array($id, $excludedParentIds, true))
                    <option value="{{ $id }}" @selected((string) $currentParentId === (string) $id)>{{ $label }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">{{ __('Nest under a top-level item to make it a dropdown.') }}</p>
            <x-input-error class="mt-2" :messages="$errors->get('parent_id')" />
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2">
            <x-input-label :value="__('Visible')" />
            <x-admin.toggle name="is_active" :checked="(bool) old('is_active', $item->is_active ?? true)" />
        </div>

        <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2">
            <x-input-label :value="__('New Tab')" />
            <x-admin.toggle name="open_in_new_tab" :checked="(bool) $newTabDefault" />
        </div>
    </div>

    <div class="grid gap-4" :class="type === 'internal' ? 'sm:grid-cols-2' : 'grid-cols-1'">
        <div x-show="type === 'internal'" style="display: none;">
            <x-input-label :value="__('Select a Page')" />
            <select
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                x-on:change="if ($event.target.value) { url = $event.target.value; if (! labelTouched) { label = $event.target.options[$event.target.selectedIndex].text; } }"
            >
                <option value="">{{ __('— Choose a page —') }}</option>
                @if (! empty($pageOptions))
                    <optgroup label="{{ __('Pages') }}">
                        @foreach ($pageOptions as $page)
                            <option value="{{ $page['url'] }}" @selected($currentUrl === $page['url'])>{{ $page['title'] }}</option>
                        @endforeach
                    </optgroup>
                @endif
                <optgroup label="{{ __('Other') }}">
                    <option value="/projects" @selected($currentUrl === '/projects')>{{ __('Projects') }}</option>
                    <option value="/gallery" @selected($currentUrl === '/gallery')>{{ __('Gallery') }}</option>
                </optgroup>
            </select>
        </div>

        <div>
            <x-input-label for="{{ $prefix }}url" :value="__('URL')" />
            <x-text-input
                id="{{ $prefix }}url"
                name="url"
                type="text"
                class="mt-1 block w-full"
                x-model="url"
                required
                :placeholder="$currentType === 'external' ? 'https://example.com' : '/about'"
            />
            <p class="mt-1 text-xs text-gray-500" x-show="type === 'internal'" style="display: none;">
                {{ __('Auto-filled from the page you select, or type any custom path.') }}
            </p>
            <x-input-error class="mt-2" :messages="$errors->get('url')" />
        </div>
    </div>
</div>
