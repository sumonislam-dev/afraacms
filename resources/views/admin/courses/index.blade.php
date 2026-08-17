<x-admin-layout :breadcrumbs="[['label' => __('Courses')]]">
    <x-slot name="title">{{ __('Courses') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Courses') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Manage the training courses students can enroll in.') }}</p>
            </div>

            <div class="flex items-center gap-3">
                @can('viewAny', \App\Models\Course::class)
                    <x-secondary-button type="button" onclick="window.location='{{ route('admin.courses.trash') }}'">
                        {{ __('Trash') }}
                    </x-secondary-button>
                @endcan

                @can('create', \App\Models\Course::class)
                    <x-primary-button type="button" onclick="window.location='{{ route('admin.courses.create') }}'">
                        {{ __('Add Course') }}
                    </x-primary-button>
                @endcan
            </div>
        </div>
    </x-slot>

    <x-admin.search-form :placeholder="__('Search by course name or code...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Course Code') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Course Name') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Duration') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Status') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($courses as $course)
                <tr>
                    <x-admin.table-td class="font-mono text-xs text-gray-700">{{ $course->course_code }}</x-admin.table-td>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $course->course_name }}</x-admin.table-td>
                    <x-admin.table-td>{{ $course->duration }}</x-admin.table-td>
                    <x-admin.table-td>
                        @if ($course->status === 'active')
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Active') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ __('Inactive') }}</span>
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @can('update', $course)
                                <a href="{{ route('admin.courses.edit', $course) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                            @endcan

                            @can('delete', $course)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-course-{{ $course->id }}')" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-course-'.$course->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Delete :name?', ['name' => $course->course_name]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This course will be moved to Trash. You can restore it or delete it permanently from there.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" class="mt-6 flex justify-end gap-3">
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
                    <x-admin.table-td colspan="5" class="text-center text-gray-500">{{ __('No courses added yet.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$courses" />
</x-admin-layout>
