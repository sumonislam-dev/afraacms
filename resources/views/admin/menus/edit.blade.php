<x-admin-layout :breadcrumbs="[['label' => __('Menus'), 'url' => route('admin.menus.index')], ['label' => $menu->name]]">
    <x-slot name="title">{{ $menu->name }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ $menu->name }}</h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('Drag items to reorder or nest them. Changes to order save automatically.') }}
        </p>
    </x-slot>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-1">
            <form method="POST" action="{{ route('admin.menus.update', $menu) }}">
                @csrf
                @method('PUT')
                @include('admin.menus._form', ['menu' => $menu])
            </form>

            @can('update', $menu)
                <x-admin.card :title="__('Add Item')">
                    <form method="POST" action="{{ route('admin.menus.items.store', $menu) }}">
                        @csrf
                        @include('admin.menus._item-form')

                        <div class="mt-6 flex justify-end">
                            <x-primary-button>{{ __('Add Item') }}</x-primary-button>
                        </div>
                    </form>
                </x-admin.card>
            @endcan
        </div>

        <div class="lg:col-span-2">
            <x-admin.card :title="__('Menu Structure')">
                @if ($tree->isEmpty())
                    <p class="text-center text-sm text-gray-500">{{ __('No items yet. Add one to get started.') }}</p>
                @else
                    <div id="menu-tree-root" data-reorder-url="{{ route('admin.menus.items.reorder', $menu) }}">
                        @include('admin.menus._tree', ['items' => $tree, 'menu' => $menu])
                    </div>
                @endif
            </x-admin.card>
        </div>
    </div>
</x-admin-layout>
