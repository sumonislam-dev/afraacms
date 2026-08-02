@php
    $canEdit = auth()->user()->can('update', $menu);
@endphp

<ul class="space-y-2" data-menu-list>
    @foreach ($items as $item)
        <li data-id="{{ $item->id }}" class="rounded-md border border-gray-200 bg-white">
            <div class="flex items-center gap-2 p-2">
                @if ($canEdit)
                    <span class="cursor-move text-gray-300 hover:text-gray-500" data-drag-handle>
                        <x-admin.icon name="bars-4" class="h-5 w-5" />
                    </span>
                @endif

                @if ($item->icon)
                    <x-admin.icon :name="$item->icon" class="h-4 w-4 flex-shrink-0 text-gray-400" />
                @endif

                <span class="flex-1 truncate text-sm font-medium text-gray-900">{{ $item->label }}</span>

                @unless ($item->is_active)
                    <span class="flex-shrink-0 rounded bg-gray-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-gray-500">{{ __('Hidden') }}</span>
                @endunless

                <span class="flex-shrink-0 text-xs text-gray-400">{{ $item->type === 'external' ? __('External') : __('Internal') }}</span>

                @if ($canEdit)
                    <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'edit-item-{{ $item->id }}')" class="flex-shrink-0 rounded p-1 text-gray-500 hover:bg-gray-100" title="{{ __('Edit') }}">
                        <x-admin.icon name="pencil" class="h-4 w-4" />
                    </button>

                    <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-item-{{ $item->id }}')" class="flex-shrink-0 rounded p-1 text-red-500 hover:bg-red-50" title="{{ __('Delete') }}">
                        <x-admin.icon name="trash" class="h-4 w-4" />
                    </button>
                @endif
            </div>

            <div class="min-h-[12px] border-t border-gray-100 py-2 pl-8 pr-2">
                @include('admin.menus._tree', ['items' => $item->children, 'menu' => $menu])
            </div>
        </li>

        @if ($canEdit)
            <x-modal :name="'edit-item-'.$item->id">
                <form method="POST" action="{{ route('admin.menus.items.update', [$menu, $item]) }}" class="p-6">
                    @csrf
                    @method('PUT')
                    <h2 class="text-lg font-medium text-gray-900">{{ __('Edit Menu Item') }}</h2>
                    @include('admin.menus._item-form', ['item' => $item])
                    <div class="mt-6 flex justify-end gap-3">
                        <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                    </div>
                </form>
            </x-modal>

            <x-modal :name="'delete-item-'.$item->id">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900">{{ __('Delete :label?', ['label' => $item->label]) }}</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('This will permanently remove this item and any nested items under it. This action cannot be undone.') }}
                    </p>

                    <form method="POST" action="{{ route('admin.menus.items.destroy', [$menu, $item]) }}" class="mt-6 flex justify-end gap-3">
                        @csrf
                        @method('DELETE')
                        <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                        <x-danger-button>{{ __('Delete') }}</x-danger-button>
                    </form>
                </div>
            </x-modal>
        @endif
    @endforeach
</ul>
