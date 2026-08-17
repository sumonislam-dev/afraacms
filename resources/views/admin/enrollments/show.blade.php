<x-admin-layout :breadcrumbs="[['label' => __('Enrollments'), 'url' => route('admin.enrollments.index')], ['label' => $enrollment->student->name]]">
    <x-slot name="title">{{ __('Certificate') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">{{ __('Certificate') }}</h2>

            @can('update', $enrollment)
                <x-secondary-button type="button" onclick="window.location='{{ route('admin.enrollments.edit', $enrollment) }}'">
                    {{ __('Edit Enrollment') }}
                </x-secondary-button>
            @endcan
        </div>
    </x-slot>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-admin.card :title="__('Student')">
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $enrollment->student->name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Student Code') }}</dt>
                        <dd class="mt-1 font-mono text-sm text-gray-900">{{ $enrollment->student->student_code }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __("Father's Name") }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $enrollment->student->father_name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __("Mother's Name") }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $enrollment->student->mother_name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Date of Birth') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $enrollment->student->date_of_birth?->format('M j, Y') }}</dd>
                    </div>
                </dl>
            </x-admin.card>

            <x-admin.card :title="__('Course & Result')">
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Course') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $enrollment->course->course_name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Duration') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $enrollment->course->duration }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Session') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $enrollment->session }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Roll Number') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $enrollment->roll_number ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Registration Number') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $enrollment->registration_number ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Completion Date') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $enrollment->completion_date?->format('M j, Y') ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Grade') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $enrollment->grade ?? '—' }}
                            @if ($enrollment->grade_point && $enrollment->grade_scale)
                                ({{ $enrollment->grade_point }} {{ __('out of') }} {{ $enrollment->grade_scale }})
                            @endif
                        </dd>
                    </div>
                </dl>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card :title="__('Certificate')">
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Certificate Number') }}</dt>
                        <dd class="mt-1 font-mono text-sm text-gray-900">{{ $enrollment->certificate_number }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                        <dd class="mt-1">
                            @if ($enrollment->certificate_status === 'valid')
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Valid') }}</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">{{ __('Revoked') }}</span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Public Verification Link') }}</dt>
                        <dd class="mt-1 break-all text-sm text-indigo-600">
                            <a href="{{ route('verify', ['code' => $enrollment->verification_code]) }}" target="_blank" rel="noopener" class="hover:underline">
                                {{ route('verify', ['code' => $enrollment->verification_code]) }}
                            </a>
                        </dd>
                    </div>
                </dl>

                <div class="mt-6 flex flex-col items-center gap-2 border-t border-gray-100 pt-6">
                    <img src="{{ route('admin.enrollments.qr', $enrollment) }}" alt="{{ __('Certificate QR code') }}" class="h-40 w-40 rounded-md border border-gray-200 p-2">
                    <a href="{{ route('admin.enrollments.qr', $enrollment) }}" download class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Download QR Code') }}</a>
                    <p class="max-w-xs text-center text-xs text-gray-500">{{ __('Print this QR code and place it on the physical certificate for verification.') }}</p>
                </div>

                @can('update', $enrollment)
                    @if ($enrollment->certificate_status === 'valid')
                        <form method="POST" action="{{ route('admin.enrollments.revoke-certificate', $enrollment) }}" class="mt-6 border-t border-gray-100 pt-6">
                            @csrf
                            <x-danger-button type="submit" class="w-full justify-center">{{ __('Revoke Certificate') }}</x-danger-button>
                        </form>
                    @endif
                @endcan
            </x-admin.card>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.enrollments.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">&larr; {{ __('Back to Enrollments') }}</a>
    </div>
</x-admin-layout>
