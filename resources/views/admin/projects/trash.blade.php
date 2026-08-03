<x-admin-layout :breadcrumbs="[['label' => __('Projects'), 'url' => route('admin.projects.index')], ['label' => __('Trash')]]">
    <x-slot name="title">{{ __('Trash') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Trashed Projects') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Restore a project, or delete it permanently.') }}</p>
            </div>

            <x-secondary-button type="button" onclick="window.location='{{ route('admin.projects.index') }}'">
                {{ __('Back to Projects') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <x-admin.search-form :placeholder="__('Search trashed projects...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Title') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Category') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Deleted') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($projects as $project)
                <tr>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $project->title }}</x-admin.table-td>
                    <x-admin.table-td>{{ $project->category?->name ?? '—' }}</x-admin.table-td>
                    <x-admin.table-td>{{ $project->deleted_at?->format('M j, Y g:i A') }}</x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @can('restore', $project)
                                <form method="POST" action="{{ route('admin.projects.restore', $project) }}">
                                    @csrf
                                    <button type="submit" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                        {{ __('Restore') }}
                                    </button>
                                </form>
                            @endcan

                            @can('forceDelete', $project)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'force-delete-project-{{ $project->id }}')" class="text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete Permanently') }}
                                </button>

                                <x-modal :name="'force-delete-project-'.$project->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Permanently delete :title?', ['title' => $project->title]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This cannot be undone - the project will be gone for good.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.projects.force-delete', $project) }}" class="mt-6 flex justify-end gap-3">
                                            @csrf
                                            @method('DELETE')
                                            <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                            <x-danger-button>{{ __('Delete Permanently') }}</x-danger-button>
                                        </form>
                                    </div>
                                </x-modal>
                            @endcan
                        </div>
                    </x-admin.table-td>
                </tr>
            @empty
                <tr>
                    <x-admin.table-td colspan="4" class="text-center text-gray-500">{{ __('Trash is empty.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$projects" />
</x-admin-layout>
