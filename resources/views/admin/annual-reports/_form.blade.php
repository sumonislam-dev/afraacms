@php
    $isEdit = isset($report);
@endphp

<x-admin.edit-layout>
    <x-slot name="main">
        <x-admin.card>
            <div x-data="{ attachmentFileName: null }" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input
                            id="title"
                            name="title"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('title', $report->title ?? '')"
                            placeholder="{{ __('e.g. Annual Report 2025-2026') }}"
                            required
                            autofocus
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    <div>
                        <x-input-label for="year" :value="__('Year')" />
                        <x-text-input
                            id="year"
                            name="year"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('year', $report->year ?? '')"
                            placeholder="{{ __('e.g. 2025-2026') }}"
                            required
                        />
                        <p class="mt-1 text-xs text-gray-500">{{ __('Controls display order on the public listing (newest first).') }}</p>
                        <x-input-error class="mt-2" :messages="$errors->get('year')" />
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <x-input-label for="attachment" :value="__('Report PDF')" />

                    @if ($isEdit && $report->attachment_url)
                        <p class="mt-2 text-sm text-gray-700">
                            <a href="{{ $report->attachment_url }}" target="_blank" rel="noopener" class="font-medium text-indigo-600 hover:text-indigo-900">{{ $report->attachment_file_name }}</a>
                        </p>
                    @endif

                    <input
                        id="attachment"
                        name="attachment"
                        type="file"
                        accept="application/pdf"
                        class="hidden"
                        x-ref="attachmentInput"
                        @change="attachmentFileName = $event.target.files[0]?.name ?? null"
                    >

                    <div class="mt-2 flex items-center gap-3">
                        <x-secondary-button type="button" @click="$refs.attachmentInput.click()">
                            {{ __('Choose File') }}
                        </x-secondary-button>
                        <span class="truncate text-sm text-gray-600" x-text="attachmentFileName ?? '{{ __('No file chosen') }}'"></span>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('attachment')" />

                    @if ($isEdit && $report->attachment_url)
                        <label class="mt-3 flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="remove_attachment" value="1" class="rounded-sm border-gray-300 text-red-600 focus:ring-red-500">
                            {{ __('Remove the current file') }}
                        </label>
                    @endif
                </div>
            </div>
        </x-admin.card>
    </x-slot>

    <x-slot name="sidebar">
        <x-admin.card :title="__('Display')">
            <div class="space-y-4">
                <div>
                    <x-input-label :value="__('Active')" />
                    <div class="mt-1 flex items-center rounded-md border border-gray-200 px-3 py-2">
                        <x-admin.toggle name="is_active" :checked="old('is_active', $report->is_active ?? true)" />
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Inactive reports are hidden from the public listing.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                </div>

                <div>
                    <x-input-label for="sort_order" :value="__('Sort Order')" />
                    <x-text-input id="sort_order" name="sort_order" type="number" class="mt-1 block w-full" :value="old('sort_order', $report->sort_order ?? 0)" />
                    <p class="mt-1 text-xs text-gray-500">{{ __('Breaks ties between reports in the same year - lower shows first.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
                </div>
            </div>
        </x-admin.card>
    </x-slot>
</x-admin.edit-layout>

<div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-6">
    <x-secondary-button type="button" onclick="window.location='{{ route('admin.annual-reports.index') }}'">{{ __('Cancel') }}</x-secondary-button>
    <x-primary-button>{{ $isEdit ? __('Update Report') : __('Add Report') }}</x-primary-button>
</div>
