@php
    $isEdit = isset($course);
    $currentStatus = old('status', $course->status ?? 'active');
@endphp

<x-admin.edit-layout>
    <x-slot name="main">
        <x-admin.card>
            <div class="space-y-4">
                <div>
                    <x-input-label for="course_name" :value="__('Course Name')" />
                    <x-text-input id="course_name" name="course_name" type="text" class="mt-1 block w-full" :value="old('course_name', $course->course_name ?? '')" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('course_name')" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $course->description ?? '') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                </div>
            </div>
        </x-admin.card>
    </x-slot>

    <x-slot name="sidebar">
        <x-admin.card :title="__('Details')">
            <div class="space-y-4">
                @if ($isEdit)
                    <div>
                        <x-input-label :value="__('Course Code')" />
                        <p class="mt-1 font-mono text-sm text-gray-900">{{ $course->course_code }}</p>
                    </div>
                @endif

                <div>
                    <x-input-label for="duration" :value="__('Duration')" />
                    <x-text-input id="duration" name="duration" type="text" class="mt-1 block w-full" :value="old('duration', $course->duration ?? '')" placeholder="{{ __('e.g. 02 Years') }}" required />
                    <x-input-error class="mt-2" :messages="$errors->get('duration')" />
                </div>

                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="active" @selected($currentStatus === 'active')>{{ __('Active') }}</option>
                        <option value="inactive" @selected($currentStatus === 'inactive')>{{ __('Inactive') }}</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Only active courses are offered when creating a new enrollment.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                </div>
            </div>
        </x-admin.card>
    </x-slot>
</x-admin.edit-layout>

<div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-6">
    <x-secondary-button type="button" onclick="window.location='{{ route('admin.courses.index') }}'">{{ __('Cancel') }}</x-secondary-button>
    <x-primary-button>{{ $isEdit ? __('Update Course') : __('Add Course') }}</x-primary-button>
</div>
