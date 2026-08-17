<x-admin-layout :breadcrumbs="[['label' => __('Enrollments')]]">
    <x-slot name="title">{{ __('Enrollments') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Enrollments') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Enroll students into courses, record results, and issue certificates.') }}</p>
            </div>

            <div class="flex items-center gap-3">
                @can('viewAny', \App\Models\Enrollment::class)
                    <x-secondary-button type="button" onclick="window.location='{{ route('admin.enrollments.trash') }}'">
                        {{ __('Trash') }}
                    </x-secondary-button>
                @endcan

                @can('create', \App\Models\Enrollment::class)
                    <x-primary-button type="button" onclick="window.location='{{ route('admin.enrollments.create') }}'">
                        {{ __('Add Enrollment') }}
                    </x-primary-button>
                @endcan
            </div>
        </div>
    </x-slot>

    <x-admin.search-form :placeholder="__('Search by student name, roll number, or certificate number...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Student') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Course') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Session') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Result') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Certificate') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($enrollments as $enrollment)
                <tr>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $enrollment->student->name }}</x-admin.table-td>
                    <x-admin.table-td>{{ $enrollment->course->course_name }}</x-admin.table-td>
                    <x-admin.table-td>{{ $enrollment->session }}</x-admin.table-td>
                    <x-admin.table-td>
                        @if ($enrollment->result_status === 'passed')
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Passed') }}</span>
                        @elseif ($enrollment->result_status === 'failed')
                            <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">{{ __('Failed') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-yellow-50 px-2 py-0.5 text-xs font-medium text-yellow-700">{{ __('Pending') }}</span>
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        @if ($enrollment->certificate_status === 'valid')
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Issued') }}</span>
                        @elseif ($enrollment->certificate_status === 'revoked')
                            <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">{{ __('Revoked') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ __('Not Issued') }}</span>
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @if ($enrollment->certificate_status !== 'not_issued')
                                @can('view', $enrollment)
                                    <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">{{ __('View Certificate') }}</a>
                                @endcan
                            @endif

                            @can('update', $enrollment)
                                <a href="{{ route('admin.enrollments.edit', $enrollment) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                            @endcan

                            @can('delete', $enrollment)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-enrollment-{{ $enrollment->id }}')" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-enrollment-'.$enrollment->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Delete this enrollment?') }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This enrollment will be moved to Trash and its certificate (if issued) will stop verifying as valid. You can restore it or delete it permanently from there.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.enrollments.destroy', $enrollment) }}" class="mt-6 flex justify-end gap-3">
                                            @csrf
                                            @method('DELETE')
                                            <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                            <x-danger-button>{{ __('Delete') }}</x-danger-button>
                                        </form>
                                    </div>
                                </x-modal>
                            @endcan
                        </div>
                    </x-admin.table-td>
                </tr>
            @empty
                <tr>
                    <x-admin.table-td colspan="6" class="text-center text-gray-500">{{ __('No enrollments added yet.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$enrollments" />
</x-admin-layout>
