<x-admin-layout :breadcrumbs="[['label' => __('Menus')]]">
    <x-slot name="title">{{ __('Menus') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Menus') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Build nested navigation menus for the site.') }}</p>
            </div>

            @can('create', \App\Models\Menu::class)
                <x-primary-button type="button" onclick="window.location='{{ route('admin.menus.create') }}'">
                    {{ __('New Menu') }}
                </x-primary-button>
            @endcan
        </div>
    </x-slot>

    <x-admin.search-form :placeholder="__('Search menus...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Name') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Slug') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Items') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($menus as $menu)
                <tr>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $menu->name }}</x-admin.table-td>
                    <x-admin.table-td><code class="text-xs">{{ $menu->slug }}</code></x-admin.table-td>
                    <x-admin.table-td>{{ $menu->items_count }}</x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @can('update', $menu)
                                <a href="{{ route('admin.menus.edit', $menu) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                            @endcan

                            @can('delete', $menu)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-menu-{{ $menu->id }}')" class="text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-menu-'.$menu->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Delete :name?', ['name' => $menu->name]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This will permanently remove this menu and all of its items. This action cannot be undone.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}" class="mt-6 flex justify-end gap-3">
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
                    <x-admin.table-td colspan="4" class="text-center text-gray-500">{{ __('No menus found.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$menus" />
</x-admin-layout>
