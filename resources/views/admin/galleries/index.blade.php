<x-admin-layout :breadcrumbs="[['label' => __('Galleries')]]">
    <x-slot name="title">{{ __('Galleries') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Galleries') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Manage photo/video albums. Drag to reorder.') }}</p>
            </div>

            @can('create', \App\Models\Gallery::class)
                <x-primary-button type="button" onclick="window.location='{{ route('admin.galleries.create') }}'">
                    {{ __('New Album') }}
                </x-primary-button>
            @endcan
        </div>
    </x-slot>

    @if ($albums->isEmpty())
        <x-admin.card>
            <p class="text-center text-sm text-gray-500">{{ __('No albums yet. Create one to get started.') }}</p>
        </x-admin.card>
    @else
        <div id="gallery-list-root" data-reorder-url="{{ route('admin.galleries.reorder') }}">
            <ul class="space-y-2" data-sortable-list>
                @foreach ($albums as $album)
                    <li data-id="{{ $album->id }}" class="flex items-center gap-3 rounded-md border border-gray-200 bg-white p-3">
                        <span class="cursor-move text-gray-300 hover:text-gray-500" data-drag-handle>
                            <x-admin.icon name="bars-4" class="h-5 w-5" />
                        </span>

                        @if ($album->cover_image_url)
                            <img src="{{ $album->cover_image_url }}" alt="" class="h-10 w-10 shrink-0 rounded-sm object-cover">
                        @endif

                        <span class="flex-1 truncate text-sm font-medium text-gray-900">{{ $album->title }}</span>

                        <span class="shrink-0 text-xs text-gray-500">{{ trans_choice(':count item|:count items', $album->items_count, ['count' => $album->items_count]) }}</span>

                        @unless ($album->is_active)
                            <span class="shrink-0 rounded-sm bg-gray-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-gray-500">{{ __('Hidden') }}</span>
                        @endunless

                        <div class="flex shrink-0 items-center gap-3">
                            @if ($album->is_active)
                                <a href="{{ route('gallery.show', $album->slug) }}" target="_blank" rel="noopener" class="text-sm font-medium text-gray-500 hover:text-gray-700">{{ __('View') }}</a>
                            @endif

                            @can('update', $album)
                                <a href="{{ route('admin.galleries.edit', $album) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                            @endcan

                            @can('delete', $album)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-album-{{ $album->id }}')" class="text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-album-'.$album->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">{{ __('Delete :title?', ['title' => $album->title]) }}</h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This will permanently remove this album and every photo/video inside it. This action cannot be undone.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.galleries.destroy', $album) }}" class="mt-6 flex justify-end gap-3">
                                            @csrf
                                            @method('DELETE')
                                            <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                            <x-danger-button>{{ __('Delete') }}</x-danger-button>
                                        </form>
                                    </div>
                                </x-modal>
                            @endcan
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</x-admin-layout>
