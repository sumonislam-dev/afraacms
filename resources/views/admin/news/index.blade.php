<x-admin-layout :breadcrumbs="[['label' => __('News')]]">
    <x-slot name="title">{{ __('News') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('News') }}</h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Manage news posts and updates.') }}
                    @can('viewAny', \App\Models\NewsCategory::class)
                        <a href="{{ route('admin.news-categories.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">{{ __('Manage Categories') }}</a>
                    @endcan
                </p>
            </div>

            <div class="flex items-center gap-3">
                @can('viewAny', \App\Models\NewsPost::class)
                    <x-secondary-button type="button" onclick="window.location='{{ route('admin.news.trash') }}'">
                        {{ __('Trash') }}
                    </x-secondary-button>
                @endcan

                @can('create', \App\Models\NewsPost::class)
                    <x-primary-button type="button" onclick="window.location='{{ route('admin.news.create') }}'">
                        {{ __('New Post') }}
                    </x-primary-button>
                @endcan
            </div>
        </div>
    </x-slot>

    <x-admin.search-form :placeholder="__('Search posts...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Title') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Category') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Published') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Status') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Featured') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($posts as $post)
                <tr>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $post->title }}</x-admin.table-td>
                    <x-admin.table-td>{{ $post->category?->name ?? '—' }}</x-admin.table-td>
                    <x-admin.table-td>{{ $post->published_at?->format('M j, Y') ?? '—' }}</x-admin.table-td>
                    <x-admin.table-td>
                        @if ($post->status === 'published')
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Published') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ __('Draft') }}</span>
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        @if ($post->is_featured)
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">{{ __('Featured') }}</span>
                        @else
                            &mdash;
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @if ($post->status === 'published')
                                <a href="{{ route('news.show', $post->slug) }}" target="_blank" rel="noopener" class="text-sm font-medium text-gray-500 hover:text-gray-700">{{ __('View') }}</a>
                            @endif

                            @can('update', $post)
                                <a href="{{ route('admin.news.edit', $post) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                            @endcan

                            @can('delete', $post)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-post-{{ $post->id }}')" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-post-'.$post->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Delete :title?', ['title' => $post->title]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This post will be moved to Trash. You can restore it or delete it permanently from there.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.news.destroy', $post) }}" class="mt-6 flex justify-end gap-3">
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
                    <x-admin.table-td colspan="6" class="text-center text-gray-500">{{ __('No news posts found.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$posts" />
</x-admin-layout>
