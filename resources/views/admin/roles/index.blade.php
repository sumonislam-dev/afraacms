<x-admin-layout :breadcrumbs="[['label' => __('Roles')]]">
    <x-slot name="title">{{ __('Roles') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Roles') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Manage roles and the permissions each one grants.') }}</p>
            </div>

            @can('create', \App\Models\Role::class)
                <x-primary-button type="button" onclick="window.location='{{ route('admin.roles.create') }}'">
                    {{ __('New Role') }}
                </x-primary-button>
            @endcan
        </div>
    </x-slot>

    <x-admin.search-form :placeholder="__('Search roles...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Name') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Permissions') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Assigned Users') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($roles as $role)
                <tr>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $role->name }}</x-admin.table-td>
                    <x-admin.table-td>{{ $role->permissions_count }}</x-admin.table-td>
                    <x-admin.table-td>{{ $role->users_count }}</x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @can('update', $role)
                                <a href="{{ route('admin.roles.edit', $role) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                            @endcan

                            @can('delete', $role)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'confirm-role-deletion-{{ $role->id }}')" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'confirm-role-deletion-'.$role->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Delete :name?', ['name' => $role->name]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This will permanently remove this role. This action cannot be undone.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="mt-6 flex justify-end gap-3">
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
                    <x-admin.table-td colspan="4" class="text-center text-gray-500">{{ __('No roles found.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$roles" />
</x-admin-layout>
