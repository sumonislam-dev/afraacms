<x-admin-layout :breadcrumbs="[['label' => __('News'), 'url' => route('admin.news.index')], ['label' => __('Trash')]]">
    <x-slot name="title">{{ __('Trash') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Trashed Posts') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Restore a post, or delete it permanently.') }}</p>
            </div>

            <x-secondary-button type="button" onclick="window.location='{{ route('admin.news.index') }}'">
                {{ __('Back to News') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <x-admin.search-form :placeholder="__('Search trashed posts...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Title') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Category') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Deleted') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($posts as $post)
                <tr>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $post->title }}</x-admin.table-td>
                    <x-admin.table-td>{{ $post->category?->name ?? '—' }}</x-admin.table-td>
                    <x-admin.table-td>{{ $post->deleted_at?->format('M j, Y g:i A') }}</x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @can('restore', $post)
                                <form method="POST" action="{{ route('admin.news.restore', $post) }}">
                                    @csrf
                                    <button type="submit" class="cursor-pointer text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                        {{ __('Restore') }}
                                    </button>
                                </form>
                            @endcan

                            @can('forceDelete', $post)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'force-delete-post-{{ $post->id }}')" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete Permanently') }}
                                </button>

                                <x-modal :name="'force-delete-post-'.$post->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Permanently delete :title?', ['title' => $post->title]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This cannot be undone - the post will be gone for good.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.news.force-delete', $post) }}" class="mt-6 flex justify-end gap-3">
                                            @csrf
                                            @method('DELETE')
                                            <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                            <x-danger-button>{{ __('Delete Permanently') }}</x-danger-button>
                                        </form>
                                    </div>
                                </x-modal>
                            @endcan
                        </div>
                    </x-admin.table-td>
                </tr>
            @empty
                <tr>
                    <x-admin.table-td colspan="4" class="text-center text-gray-500">{{ __('Trash is empty.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$posts" />
</x-admin-layout>
