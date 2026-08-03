<x-admin-layout :breadcrumbs="[['label' => __('Users')]]">
    <x-slot name="title">{{ __('Users') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Users') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Manage admin accounts and their roles.') }}</p>
            </div>

            @can('create', \App\Models\User::class)
                <x-primary-button type="button" onclick="window.location='{{ route('admin.users.create') }}'">
                    {{ __('New User') }}
                </x-primary-button>
            @endcan
        </div>
    </x-slot>

    <x-admin.search-form :placeholder="__('Search users...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Name') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Email') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Role') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Status') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($users as $user)
                <tr>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $user->name }}</x-admin.table-td>
                    <x-admin.table-td>{{ $user->email }}</x-admin.table-td>
                    <x-admin.table-td>
                        @foreach ($user->roles as $role)
                            <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">{{ $role->name }}</span>
                        @endforeach
                    </x-admin.table-td>
                    <x-admin.table-td>
                        @if ($user->is_active)
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Active') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ __('Inactive') }}</span>
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @can('update', $user)
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>

                                <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                        {{ $user->is_active ? __('Deactivate') : __('Activate') }}
                                    </button>
                                </form>
                            @endcan

                            @can('delete', $user)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'confirm-user-deletion-{{ $user->id }}')" class="text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'confirm-user-deletion-'.$user->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Delete :name?', ['name' => $user->name]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This will permanently remove this user. This action cannot be undone.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-6 flex justify-end gap-3">
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
                    <x-admin.table-td colspan="5" class="text-center text-gray-500">{{ __('No users found.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$users" />
</x-admin-layout>
