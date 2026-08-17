@php
    $isEdit = isset($enrollment);
    $currentStudentId = old('student_id', $enrollment->student_id ?? '');
    $currentCourseId = old('course_id', $enrollment->course_id ?? '');
    $currentResultStatus = old('result_status', $enrollment->result_status ?? 'pending');
@endphp

<x-admin.edit-layout>
    <x-slot name="main">
        <x-admin.card :title="__('Enrollment')">
            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="student_id" :value="__('Student')" />
                        <select id="student_id" name="student_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('— Select —') }}</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}" @selected((string) $currentStudentId === (string) $student->id)>{{ $student->name }} ({{ $student->student_code }})</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('student_id')" />
                    </div>

                    <div>
                        <x-input-label for="course_id" :value="__('Course')" />
                        <select id="course_id" name="course_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('— Select —') }}</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" @selected((string) $currentCourseId === (string) $course->id)>{{ $course->course_name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">{{ __('Only active courses are listed here.') }}</p>
                        <x-input-error class="mt-2" :messages="$errors->get('course_id')" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <x-input-label for="session" :value="__('Session')" />
                        <x-text-input id="session" name="session" type="text" class="mt-1 block w-full" :value="old('session', $enrollment->session ?? '')" placeholder="{{ __('e.g. 2024-2025') }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('session')" />
                    </div>

                    <div>
                        <x-input-label for="roll_number" :value="__('Roll Number')" />
                        <x-text-input id="roll_number" name="roll_number" type="text" class="mt-1 block w-full" :value="old('roll_number', $enrollment->roll_number ?? '')" />
                        <x-input-error class="mt-2" :messages="$errors->get('roll_number')" />
                    </div>

                    <div>
                        <x-input-label for="registration_number" :value="__('Registration Number')" />
                        <x-text-input id="registration_number" name="registration_number" type="text" class="mt-1 block w-full" :value="old('registration_number', $enrollment->registration_number ?? '')" />
                        <x-input-error class="mt-2" :messages="$errors->get('registration_number')" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="admission_date" :value="__('Admission Date')" />
                        <x-text-input id="admission_date" name="admission_date" type="date" class="mt-1 block w-full" :value="old('admission_date', optional($enrollment->admission_date ?? null)->format('Y-m-d'))" />
                        <x-input-error class="mt-2" :messages="$errors->get('admission_date')" />
                    </div>

                    <div>
                        <x-input-label for="completion_date" :value="__('Completion Date')" />
                        <x-text-input id="completion_date" name="completion_date" type="date" class="mt-1 block w-full" :value="old('completion_date', optional($enrollment->completion_date ?? null)->format('Y-m-d'))" />
                        <x-input-error class="mt-2" :messages="$errors->get('completion_date')" />
                    </div>
                </div>
            </div>
        </x-admin.card>

        <x-admin.card :title="__('Result')">
            <div class="space-y-4">
                <div>
                    <x-input-label for="result_status" :value="__('Result Status')" />
                    <select id="result_status" name="result_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500 sm:w-64">
                        <option value="pending" @selected($currentResultStatus === 'pending')>{{ __('Pending') }}</option>
                        <option value="passed" @selected($currentResultStatus === 'passed')>{{ __('Passed') }}</option>
                        <option value="failed" @selected($currentResultStatus === 'failed')>{{ __('Failed') }}</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">{{ __('A certificate can only be issued once this is set to Passed.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('result_status')" />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <x-input-label for="grade" :value="__('Grade')" />
                        <x-text-input id="grade" name="grade" type="text" class="mt-1 block w-full" :value="old('grade', $enrollment->grade ?? '')" placeholder="{{ __('e.g. B+') }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('grade')" />
                    </div>

                    <div>
                        <x-input-label for="grade_point" :value="__('Grade Point')" />
                        <x-text-input id="grade_point" name="grade_point" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('grade_point', $enrollment->grade_point ?? '')" placeholder="{{ __('e.g. 2.55') }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('grade_point')" />
                    </div>

                    <div>
                        <x-input-label for="grade_scale" :value="__('Grade Scale')" />
                        <x-text-input id="grade_scale" name="grade_scale" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('grade_scale', $enrollment->grade_scale ?? '')" placeholder="{{ __('e.g. 4.00') }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('grade_scale')" />
                    </div>
                </div>
            </div>
        </x-admin.card>

        @if ($isEdit)
            <x-admin.card :title="__('Certificate')">
                @if ($enrollment->certificate_status === 'not_issued')
                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm text-gray-500">
                            {{ __('No certificate has been issued for this enrollment yet.') }}
                        </p>

                        @if ($enrollment->result_status === 'passed')
                            {{-- A real <form> can't nest inside this page's main edit <form> (HTML
                                 forbids nested forms), so this posts via fetch instead. --}}
                            <div x-data="{ sending: false }">
                                <x-primary-button
                                    type="button"
                                    x-bind:disabled="sending"
                                    x-on:click="
                                        sending = true;
                                        fetch('{{ route('admin.enrollments.issue-certificate', $enrollment) }}', {
                                            method: 'POST',
                                            redirect: 'manual',
                                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                                        }).finally(() => window.location.reload());
                                    "
                                >
                                    <span x-show="!sending">{{ __('Issue Certificate') }}</span>
                                    <span x-show="sending" style="display: none;">{{ __('Issuing...') }}</span>
                                </x-primary-button>
                            </div>
                        @else
                            <p class="text-sm text-gray-400">{{ __('Set Result Status to Passed and save first.') }}</p>
                        @endif
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="space-y-3">
                            <div>
                                <x-input-label :value="__('Certificate Number')" />
                                <p class="mt-1 font-mono text-sm text-gray-900">{{ $enrollment->certificate_number }}</p>
                                <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="mt-1 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('View Certificate Page') }} &rarr;</a>
                            </div>

                            <div>
                                <x-input-label :value="__('Public Verification Link')" />
                                <p class="mt-1 break-all text-sm text-indigo-600">
                                    <a href="{{ route('verify', ['code' => $enrollment->verification_code]) }}" target="_blank" rel="noopener" class="hover:underline">
                                        {{ route('verify', ['code' => $enrollment->verification_code]) }}
                                    </a>
                                </p>
                                <p class="mt-1 text-xs text-gray-500">{{ __('This is the link encoded in the QR code.') }}</p>
                            </div>

                            <div>
                                <x-input-label :value="__('Status')" />
                                @if ($enrollment->certificate_status === 'valid')
                                    <p class="mt-1"><span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Valid') }}</span></p>
                                @else
                                    <p class="mt-1"><span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">{{ __('Revoked') }}</span></p>
                                @endif
                            </div>

                            <div x-data="{ sending: false }">
                                @if ($enrollment->certificate_status === 'valid')
                                    <x-danger-button
                                        type="button"
                                        x-bind:disabled="sending"
                                        x-on:click="
                                            sending = true;
                                            fetch('{{ route('admin.enrollments.revoke-certificate', $enrollment) }}', {
                                                method: 'POST',
                                                redirect: 'manual',
                                                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                                            }).finally(() => window.location.reload());
                                        "
                                    >
                                        <span x-show="!sending">{{ __('Revoke Certificate') }}</span>
                                        <span x-show="sending" style="display: none;">{{ __('Revoking...') }}</span>
                                    </x-danger-button>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col items-center gap-2 sm:items-end">
                            <img src="{{ route('admin.enrollments.qr', $enrollment) }}" alt="{{ __('Certificate QR code') }}" class="h-40 w-40 rounded-md border border-gray-200 p-2">
                            <a href="{{ route('admin.enrollments.qr', $enrollment) }}" download="{{ $enrollment->certificate_number }}-qr.png" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Download QR Code') }}</a>
                            <p class="max-w-xs text-center text-xs text-gray-500">{{ __('Print this QR code and place it on the physical certificate for verification.') }}</p>
                        </div>
                    </div>
                @endif
            </x-admin.card>
        @endif
    </x-slot>
</x-admin.edit-layout>

<div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-6">
    <x-secondary-button type="button" onclick="window.location='{{ route('admin.enrollments.index') }}'">{{ __('Cancel') }}</x-secondary-button>
    <x-primary-button>{{ $isEdit ? __('Update Enrollment') : __('Add Enrollment') }}</x-primary-button>
</div>
