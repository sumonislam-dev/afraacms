<x-admin-layout :breadcrumbs="[['label' => __('Pages')]]">
    <x-slot name="title">{{ __('Pages') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Pages') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Create and manage the site\'s standalone pages.') }}</p>
            </div>

            @can('create', \App\Models\Page::class)
                <x-primary-button type="button" onclick="window.location='{{ route('admin.pages.create') }}'">
                    {{ __('New Page') }}
                </x-primary-button>
            @endcan
        </div>
    </x-slot>

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Title') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Slug') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Status') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Template') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Publish Date') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($pages as $page)
                <tr>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $page->title }}</x-admin.table-td>
                    <x-admin.table-td><code class="text-xs">/{{ $page->slug }}</code></x-admin.table-td>
                    <x-admin.table-td>
                        @if ($page->isPublished())
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Published') }}</span>
                        @elseif ($page->status === 'published')
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">{{ __('Scheduled') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ __('Draft') }}</span>
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>{{ config("pages.templates.{$page->template}", $page->template) }}</x-admin.table-td>
                    <x-admin.table-td>{{ $page->published_at?->format('M j, Y g:i A') ?? '—' }}</x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @if ($page->isPublished())
                                <a href="{{ url($page->slug) }}" target="_blank" rel="noopener" class="text-sm font-medium text-gray-500 hover:text-gray-700">{{ __('View') }}</a>
                            @endif

                            @can('viewAny', \App\Models\Section::class)
                                <a href="{{ route('admin.pages.sections.index', $page) }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">{{ __('Sections') }}</a>
                            @endcan

                            @can('update', $page)
                                <a href="{{ route('admin.pages.edit', $page) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                            @endcan

                            @can('delete', $page)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-page-{{ $page->id }}')" class="text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-page-'.$page->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Delete :title?', ['title' => $page->title]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This will permanently remove this page. This action cannot be undone.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="mt-6 flex justify-end gap-3">
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
                    <x-admin.table-td colspan="6" class="text-center text-gray-500">{{ __('No pages found.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$pages" />
</x-admin-layout>
