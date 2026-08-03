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
            @if ($album->items->isNotEmpty())
                <div id="gallery-items-root" data-reorder-url="{{ route('admin.galleries.items.reorder', $album) }}">
                    <div class="space-y-3" data-sortable-list>
                        @foreach ($album->items as $item)
                            <div data-id="{{ $item->id }}" class="flex items-center justify-between rounded-md border border-gray-200 p-3">
                                <div class="flex items-center gap-3">
                                    <span class="cursor-move text-gray-300 hover:text-gray-500" data-drag-handle>
                                        <x-admin.icon name="bars-4" class="h-5 w-5" />
                                    </span>

                                    @if ($item->type === 'image' && $item->image_url)
                                        <img src="{{ $item->image_url }}" alt="" class="h-10 w-10 flex-shrink-0 rounded object-cover">
                                    @else
                                        <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded bg-gray-100 text-gray-400">
                                            <x-admin.icon name="photo" class="h-5 w-5" />
                                        </span>
                                    @endif

                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $item->caption ?: ($item->type === 'video' ? __('(video)') : __('(photo)')) }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ ucfirst($item->type) }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-shrink-0 items-center gap-3">
                                    <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'edit-gallery-item-{{ $item->id }}')" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                        {{ __('Edit') }}
                                    </button>
                                    <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-gallery-item-{{ $item->id }}')" class="text-sm font-medium text-red-600 hover:text-red-900">
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            </div>

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
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500">{{ __('No photos or videos yet. Add one below.') }}</p>
            @endif

            <div class="mt-6 border-t border-gray-100 pt-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Add Photo/Video') }}</h3>
                    @include('admin.galleries._bulk-add', ['album' => $album])
                </div>

                <form method="POST" action="{{ route('admin.galleries.items.store', $album) }}" class="mt-4">
                    @csrf
                    @include('admin.galleries._item-form', ['item' => null])
                    <div class="mt-4 flex justify-end">
                        <x-primary-button>{{ __('Add Item') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </x-admin.card>
    </div>
</x-admin-layout>
