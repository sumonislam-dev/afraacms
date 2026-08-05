<x-admin-layout :breadcrumbs="[['label' => __('Success Stories')]]">
    <x-slot name="title">{{ __('Success Stories') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Success Stories') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Manage the success stories shown on the public site.') }}</p>
            </div>

            <div class="flex items-center gap-3">
                @can('viewAny', \App\Models\Story::class)
                    <x-secondary-button type="button" onclick="window.location='{{ route('admin.stories.trash') }}'">
                        {{ __('Trash') }}
                    </x-secondary-button>
                @endcan

                @can('create', \App\Models\Story::class)
                    <x-primary-button type="button" onclick="window.location='{{ route('admin.stories.create') }}'">
                        {{ __('New Story') }}
                    </x-primary-button>
                @endcan
            </div>
        </div>
    </x-slot>

    <x-admin.search-form :placeholder="__('Search stories...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Title') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Project') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Published') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Status') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Featured') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($stories as $story)
                <tr>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $story->title }}</x-admin.table-td>
                    <x-admin.table-td>{{ $story->project?->title ?? '—' }}</x-admin.table-td>
                    <x-admin.table-td>{{ $story->published_at?->format('M j, Y') ?? '—' }}</x-admin.table-td>
                    <x-admin.table-td>
                        @if ($story->status === 'published')
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Published') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ __('Draft') }}</span>
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        @if ($story->is_featured)
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">{{ __('Featured') }}</span>
                        @else
                            &mdash;
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @if ($story->status === 'published')
                                <a href="{{ route('stories.show', $story->slug) }}" target="_blank" rel="noopener" class="text-sm font-medium text-gray-500 hover:text-gray-700">{{ __('View') }}</a>
                            @endif

                            @can('update', $story)
                                <a href="{{ route('admin.stories.edit', $story) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                            @endcan

                            @can('delete', $story)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-story-{{ $story->id }}')" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-story-'.$story->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Delete :title?', ['title' => $story->title]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This story will be moved to Trash. You can restore it or delete it permanently from there.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.stories.destroy', $story) }}" class="mt-6 flex justify-end gap-3">
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
                    <x-admin.table-td colspan="6" class="text-center text-gray-500">{{ __('No success stories found.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$stories" />
</x-admin-layout>
