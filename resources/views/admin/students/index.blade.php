<x-admin-layout :breadcrumbs="[['label' => __('Students')]]">
    <x-slot name="title">{{ __('Students') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Students') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Manage student records used for course enrollments and certificates.') }}</p>
            </div>

            <div class="flex items-center gap-3">
                @can('viewAny', \App\Models\Student::class)
                    <x-secondary-button type="button" onclick="window.location='{{ route('admin.students.trash') }}'">
                        {{ __('Trash') }}
                    </x-secondary-button>
                @endcan

                @can('create', \App\Models\Student::class)
                    <x-primary-button type="button" onclick="window.location='{{ route('admin.students.create') }}'">
                        {{ __('Add Student') }}
                    </x-primary-button>
                @endcan
            </div>
        </div>
    </x-slot>

    <x-admin.search-form :placeholder="__('Search by name or student code...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Student Code') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Name') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Date of Birth') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Phone') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($students as $student)
                <tr>
                    <x-admin.table-td class="font-mono text-xs text-gray-700">{{ $student->student_code }}</x-admin.table-td>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $student->name }}</x-admin.table-td>
                    <x-admin.table-td>{{ $student->date_of_birth?->format('M j, Y') }}</x-admin.table-td>
                    <x-admin.table-td>{{ $student->phone ?? '—' }}</x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @can('update', $student)
                                <a href="{{ route('admin.students.edit', $student) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                            @endcan

                            @can('delete', $student)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-student-{{ $student->id }}')" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-student-'.$student->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Delete :name?', ['name' => $student->name]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This student will be moved to Trash. You can restore them or delete them permanently from there.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.students.destroy', $student) }}" class="mt-6 flex justify-end gap-3">
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
                    <x-admin.table-td colspan="5" class="text-center text-gray-500">{{ __('No students added yet.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$students" />
</x-admin-layout>
