<x-admin-layout :breadcrumbs="[['label' => __('Project Categories')]]">
    <x-slot name="title">{{ __('Project Categories') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Project Categories') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Group projects for filtering on the public site.') }}</p>
            </div>

            @can('create', \App\Models\ProjectCategory::class)
                <x-primary-button type="button" x-data="" x-on:click="$dispatch('open-modal', 'create-category')">
                    {{ __('New Category') }}
                </x-primary-button>
            @endcan
        </div>
    </x-slot>

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Name') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Slug') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Projects') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($categories as $category)
                <tr>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $category->name }}</x-admin.table-td>
                    <x-admin.table-td><code class="text-xs">{{ $category->slug }}</code></x-admin.table-td>
                    <x-admin.table-td>{{ $category->projects_count }}</x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @can('update', $category)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'edit-category-{{ $category->id }}')" class="cursor-pointer text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                    {{ __('Edit') }}
                                </button>

                                <x-modal :name="'edit-category-'.$category->id">
                                    <form method="POST" action="{{ route('admin.project-categories.update', $category) }}" class="p-6">
                                        @csrf
                                        @method('PUT')
                                        <h2 class="text-lg font-medium text-gray-900">{{ __('Edit Category') }}</h2>

                                        <div class="mt-6">
                                            @include('admin.project-categories._form', ['category' => $category, 'prefix' => 'edit-'.$category->id.'-'])
                                        </div>

                                        <div class="mt-6 flex justify-end gap-3">
                                            <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                            <x-primary-button>{{ __('Save') }}</x-primary-button>
                                        </div>
                                    </form>
                                </x-modal>
                            @endcan

                            @can('delete', $category)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-category-{{ $category->id }}')" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-category-'.$category->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Delete :name?', ['name' => $category->name]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('Projects in this category are not deleted - they just become uncategorized.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.project-categories.destroy', $category) }}" class="mt-6 flex justify-end gap-3">
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
                    <x-admin.table-td colspan="4" class="text-center text-gray-500">{{ __('No categories yet.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    @can('create', \App\Models\ProjectCategory::class)
        <x-modal name="create-category">
            <form method="POST" action="{{ route('admin.project-categories.store') }}" class="p-6">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">{{ __('New Category') }}</h2>

                <div class="mt-6">
                    @include('admin.project-categories._form', ['category' => null, 'prefix' => 'new-'])
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                    <x-primary-button>{{ __('Create Category') }}</x-primary-button>
                </div>
            </form>
        </x-modal>
    @endcan
</x-admin-layout>
