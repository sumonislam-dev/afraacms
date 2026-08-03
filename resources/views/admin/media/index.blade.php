<x-admin-layout :breadcrumbs="[['label' => __('Media Library')]]">
    <x-slot name="title">{{ __('Media Library') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Media Library') }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ __('Upload, search, and manage every image used across the site.') }}</p>
    </x-slot>

    @can('create', \App\Models\MediaItem::class)
        <x-admin.media-uploader :max-batch="20" />
    @endcan

    <form method="GET" action="{{ route('admin.media.index') }}" class="mt-6 mb-6">
        <x-text-input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="{{ __('Search media...') }}"
            class="block w-full max-w-md"
        />
    </form>

    @if ($items->isEmpty())
        <x-admin.card>
            <p class="text-center text-sm text-gray-500">{{ __('No media uploaded yet.') }}</p>
        </x-admin.card>
    @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
            @foreach ($items as $item)
                <div class="group relative overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-black/5">
                    <img src="{{ $item->thumb_url }}" alt="{{ $item->title }}" class="aspect-square w-full object-cover">

                    <div class="p-2">
                        <p class="truncate text-xs font-medium text-gray-700" title="{{ $item->title }}">{{ $item->title }}</p>
                        <p class="text-xs text-gray-400">{{ $item->dimensions }}</p>
                    </div>

                    <div class="absolute inset-x-0 top-0 flex items-center justify-end gap-1 bg-linear-to-b from-black/50 to-transparent p-1 opacity-0 transition-opacity group-hover:opacity-100">
                        @can('update', $item)
                            <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'rename-media-{{ $item->id }}')" class="rounded-sm bg-white/90 p-1 text-gray-700 hover:bg-white" title="{{ __('Rename') }}">
                                <x-admin.icon name="document-text" class="h-4 w-4" />
                            </button>

                            <label class="cursor-pointer rounded-sm bg-white/90 p-1 text-gray-700 hover:bg-white" title="{{ __('Replace') }}">
                                <x-admin.icon name="photo" class="h-4 w-4" />
                                <form method="POST" action="{{ route('admin.media.replace', $item) }}" enctype="multipart/form-data" class="hidden">
                                    @csrf
                                    <input type="file" name="file" accept="image/*" onchange="this.form.submit()">
                                </form>
                            </label>
                        @endcan

                        @can('delete', $item)
                            <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-media-{{ $item->id }}')" class="rounded-sm bg-white/90 p-1 text-red-600 hover:bg-white" title="{{ __('Delete') }}">
                                <x-admin.icon name="x-mark" class="h-4 w-4" />
                            </button>
                        @endcan
                    </div>
                </div>

                @can('update', $item)
                    <x-modal :name="'rename-media-'.$item->id">
                        <form method="POST" action="{{ route('admin.media.update', $item) }}" class="p-6">
                            @csrf
                            @method('PUT')

                            <h2 class="text-lg font-medium text-gray-900">{{ __('Rename') }}</h2>

                            <div class="mt-4">
                                <x-input-label for="title-{{ $item->id }}" :value="__('Title')" />
                                <x-text-input id="title-{{ $item->id }}" name="title" type="text" class="mt-1 block w-full" :value="$item->title" required />
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                <x-primary-button>{{ __('Save') }}</x-primary-button>
                            </div>
                        </form>
                    </x-modal>
                @endcan

                @can('delete', $item)
                    <x-modal :name="'delete-media-'.$item->id">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Delete :name?', ['name' => $item->title]) }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('This will permanently remove this file. This action cannot be undone.') }}
                            </p>

                            <form method="POST" action="{{ route('admin.media.destroy', $item) }}" class="mt-6 flex justify-end gap-3">
                                @csrf
                                @method('DELETE')
                                <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                <x-danger-button>{{ __('Delete') }}</x-danger-button>
                            </form>
                        </div>
                    </x-modal>
                @endcan
            @endforeach
        </div>

        <x-admin.pagination :paginator="$items" />
    @endif
</x-admin-layout>
