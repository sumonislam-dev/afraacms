<x-admin-layout :breadcrumbs="[['label' => __('Menus'), 'url' => route('admin.menus.index')], ['label' => $menu->name]]">
    <x-slot name="title">{{ $menu->name }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ $menu->name }}</h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Drag items to reorder or nest them. Changes to order save automatically.') }}
                </p>
            </div>

            @can('update', $menu)
                <x-secondary-button type="button" x-data="" x-on:click="$dispatch('open-modal', 'edit-menu-details')">
                    <x-admin.icon name="pencil" class="mr-1.5 h-4 w-4" />
                    {{ __('Edit Menu') }}
                </x-secondary-button>
            @endcan
        </div>
    </x-slot>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        @can('update', $menu)
            <x-admin.card :title="__('Add Item')">
                <form method="POST" action="{{ route('admin.menus.items.store', $menu) }}">
                    @csrf
                    @include('admin.menus._item-form', ['pageOptions' => $pageOptions])

                    <div class="mt-6 flex justify-end">
                        <x-primary-button>{{ __('Add Item') }}</x-primary-button>
                    </div>
                </form>
            </x-admin.card>
        @endcan

        <x-admin.card :title="__('Menu Structure')">
            @if ($tree->isEmpty())
                <p class="text-center text-sm text-gray-500">{{ __('No items yet. Add one to get started.') }}</p>
            @else
                <div id="menu-tree-root" data-reorder-url="{{ route('admin.menus.items.reorder', $menu) }}">
                    @include('admin.menus._tree', ['items' => $tree, 'menu' => $menu, 'pageOptions' => $pageOptions])
                </div>
            @endif
        </x-admin.card>
    </div>

    @can('update', $menu)
        <x-modal name="edit-menu-details" max-width="md">
            <form method="POST" action="{{ route('admin.menus.update', $menu) }}" class="p-6">
                @csrf
                @method('PUT')
                <h2 class="text-lg font-medium text-gray-900">{{ __('Edit Menu') }}</h2>

                <div class="mt-6">
                    @include('admin.menus._form', ['menu' => $menu])
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                </div>
            </form>
        </x-modal>
    @endcan
</x-admin-layout>
