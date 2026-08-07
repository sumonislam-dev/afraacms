<x-admin-layout :breadcrumbs="[['label' => __('Galleries'), 'url' => route('admin.galleries.index')], ['label' => $album->title]]">
    <x-slot name="title">{{ __('Edit Album') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit Album') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.galleries.update', $album) }}">
        @csrf
        @method('PUT')
        @include('admin.galleries._form')
    </form>

    <div class="mt-6">
        <x-admin.card :title="__('Photos & Videos')">
            <x-slot name="header">
                <div class="flex items-center gap-2">
                    @include('admin.galleries._bulk-add', ['album' => $album])

                    <x-secondary-button type="button" x-data="" x-on:click="$dispatch('open-modal', 'add-gallery-item-manual')">
                        {{ __('Add Manually') }}
                    </x-secondary-button>
                </div>
            </x-slot>

            @if ($album->items->isNotEmpty())
                <div id="gallery-items-root" data-reorder-url="{{ route('admin.galleries.items.reorder', $album) }}">
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6" data-sortable-list>
                        @foreach ($album->items as $item)
                            <div data-id="{{ $item->id }}" class="group relative aspect-square overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                                <span class="absolute left-1 top-1 z-10 cursor-move rounded-sm bg-black/50 p-1 text-white opacity-0 transition group-hover:opacity-100" data-drag-handle>
                                    <x-admin.icon name="bars-4" class="h-3.5 w-3.5" />
                                </span>

                                @if ($item->type === 'image' && $item->image_url)
                                    <img src="{{ $item->image_url }}" alt="" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gray-100 text-gray-300">
                                        <x-admin.icon name="play" class="h-8 w-8" />
                                    </div>
                                @endif

                                <div class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent px-2 pb-1.5 pt-5">
                                    <p class="truncate text-[11px] font-medium text-white">
                                        {{ $item->caption ?: ($item->type === 'video' ? __('(video)') : __('(photo)')) }}
                                    </p>
                                </div>

                                <div class="absolute right-1 top-1 z-10 flex gap-1 opacity-0 transition group-hover:opacity-100">
                                    <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'edit-gallery-item-{{ $item->id }}')" class="cursor-pointer rounded-sm bg-black/50 p-1 text-white hover:bg-indigo-600">
                                        <x-admin.icon name="pencil" class="h-3.5 w-3.5" />
                                    </button>
                                    <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-gallery-item-{{ $item->id }}')" class="cursor-pointer rounded-sm bg-black/50 p-1 text-white hover:bg-red-600">
                                        <x-admin.icon name="x-mark" class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @foreach ($album->items as $item)
                    <x-modal :name="'edit-gallery-item-'.$item->id">
                        <form method="POST" action="{{ route('admin.galleries.items.update', [$album, $item]) }}" class="p-6">
                            @csrf
                            @method('PUT')
                            <h2 class="text-lg font-medium text-gray-900">{{ __('Edit Item') }}</h2>
                            @include('admin.galleries._item-form')
                            <div class="mt-6 flex justify-end gap-3">
                                <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                <x-primary-button>{{ __('Save') }}</x-primary-button>
                            </div>
                        </form>
                    </x-modal>

                    <x-modal :name="'delete-gallery-item-'.$item->id">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900">{{ __('Delete this item?') }}</h2>
                            <p class="mt-1 text-sm text-gray-600">{{ __('This action cannot be undone.') }}</p>

                            <form method="POST" action="{{ route('admin.galleries.items.destroy', [$album, $item]) }}" class="mt-6 flex justify-end gap-3">
                                @csrf
                                @method('DELETE')
                                <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                <x-danger-button>{{ __('Delete') }}</x-danger-button>
                            </form>
                        </div>
                    </x-modal>
                @endforeach
            @else
                <p class="text-sm text-gray-500">{{ __('No photos or videos yet. Use "Bulk Add Photos" or "Add Manually" above.') }}</p>
            @endif

            <x-modal name="add-gallery-item-manual">
                <form method="POST" action="{{ route('admin.galleries.items.store', $album) }}" class="p-6">
                    @csrf
                    <h2 class="text-lg font-medium text-gray-900">{{ __('Add Photo/Video') }}</h2>
                    @include('admin.galleries._item-form', ['item' => null])
                    <div class="mt-6 flex justify-end gap-3">
                        <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                        <x-primary-button>{{ __('Add Item') }}</x-primary-button>
                    </div>
                </form>
            </x-modal>
        </x-admin.card>
    </div>
</x-admin-layout>
