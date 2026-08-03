<x-admin-layout :breadcrumbs="[['label' => __('Projects')]]">
    <x-slot name="title">{{ __('Projects') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Projects') }}</h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Manage the portfolio.') }}
                    @can('viewAny', \App\Models\ProjectCategory::class)
                        <a href="{{ route('admin.project-categories.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">{{ __('Manage Categories') }}</a>
                    @endcan
                </p>
            </div>

            <div class="flex items-center gap-3">
                @can('viewAny', \App\Models\Project::class)
                    <x-secondary-button type="button" onclick="window.location='{{ route('admin.projects.trash') }}'">
                        {{ __('Trash') }}
                    </x-secondary-button>
                @endcan

                @can('create', \App\Models\Project::class)
                    <x-primary-button type="button" onclick="window.location='{{ route('admin.projects.create') }}'">
                        {{ __('New Project') }}
                    </x-primary-button>
                @endcan
            </div>
        </div>
    </x-slot>

    <x-admin.search-form :placeholder="__('Search projects...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Title') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Category') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Status') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Featured') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($projects as $project)
                <tr>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $project->title }}</x-admin.table-td>
                    <x-admin.table-td>{{ $project->category?->name ?? '—' }}</x-admin.table-td>
                    <x-admin.table-td>
                        @if ($project->status === 'published')
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Published') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ __('Draft') }}</span>
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        @if ($project->is_featured)
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">{{ __('Featured') }}</span>
                        @else
                            &mdash;
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @if ($project->status === 'published')
                                <a href="{{ route('projects.show', $project->slug) }}" target="_blank" rel="noopener" class="text-sm font-medium text-gray-500 hover:text-gray-700">{{ __('View') }}</a>
                            @endif

                            @can('update', $project)
                                <a href="{{ route('admin.projects.edit', $project) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                            @endcan

                            @can('delete', $project)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-project-{{ $project->id }}')" class="text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-project-'.$project->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Delete :title?', ['title' => $project->title]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This project will be moved to Trash. You can restore it or delete it permanently from there.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" class="mt-6 flex justify-end gap-3">
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
                    <x-admin.table-td colspan="5" class="text-center text-gray-500">{{ __('No projects found.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$projects" />
</x-admin-layout>
