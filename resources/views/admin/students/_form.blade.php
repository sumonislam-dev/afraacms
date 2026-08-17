@php
    $isEdit = isset($student);
@endphp

<x-admin.edit-layout>
    <x-slot name="main">
        <x-admin.card>
            <div class="space-y-4">
                <div>
                    <x-input-label for="name" :value="__('Full Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $student->name ?? '')" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="father_name" :value="__('Father\'s Name')" />
                        <x-text-input id="father_name" name="father_name" type="text" class="mt-1 block w-full" :value="old('father_name', $student->father_name ?? '')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('father_name')" />
                    </div>

                    <div>
                        <x-input-label for="mother_name" :value="__('Mother\'s Name')" />
                        <x-text-input id="mother_name" name="mother_name" type="text" class="mt-1 block w-full" :value="old('mother_name', $student->mother_name ?? '')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('mother_name')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                    <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full sm:w-64" :value="old('date_of_birth', optional($student->date_of_birth ?? null)->format('Y-m-d'))" required />
                    <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
                </div>

                <div>
                    <x-input-label for="address" :value="__('Address')" />
                    <textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $student->address ?? '') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                </div>
            </div>
        </x-admin.card>
    </x-slot>

    <x-slot name="sidebar">
        <x-admin.card :title="__('Contact')">
            <div class="space-y-4">
                @if ($isEdit)
                    <div>
                        <x-input-label :value="__('Student Code')" />
                        <p class="mt-1 font-mono text-sm text-gray-900">{{ $student->student_code }}</p>
                    </div>
                @endif

                <div>
                    <x-input-label for="phone" :value="__('Phone')" />
                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $student->phone ?? '')" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $student->email ?? '')" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>
            </div>
        </x-admin.card>
    </x-slot>
</x-admin.edit-layout>

<div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-6">
    <x-secondary-button type="button" onclick="window.location='{{ route('admin.students.index') }}'">{{ __('Cancel') }}</x-secondary-button>
    <x-primary-button>{{ $isEdit ? __('Update Student') : __('Add Student') }}</x-primary-button>
</div>
