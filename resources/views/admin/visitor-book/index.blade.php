<x-admin-layout :breadcrumbs="[['label' => __('Visitor Book')]]">
    <x-slot name="title">{{ __('Visitor Book') }}</x-slot>

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">{{ __('Visitor Book') }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('Review and approve visitor opinions before they show publicly.') }}</p>
        </div>
    </x-slot>

    <div class="mb-4 flex gap-2">
        @foreach (['pending' => __('Pending'), 'approved' => __('Approved'), 'rejected' => __('Rejected'), 'all' => __('All')] as $key => $label)
            <a
                href="{{ route('admin.visitor-book.index', ['status' => $key]) }}"
                class="rounded-full px-3 py-1 text-sm font-medium {{ $status === $key ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
            >
                {{ $label }}
                @if ($key !== 'all')
                    ({{ $counts[$key] }})
                @endif
            </a>
        @endforeach
    </div>

    <x-admin.search-form :placeholder="__('Search by visitor name...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Visitor') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Opinion') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Project') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Submitted') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Status') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($entries as $entry)
                <tr>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $entry->visitor_name }}</x-admin.table-td>
                    <x-admin.table-td class="max-w-xs truncate">{{ $entry->opinion }}</x-admin.table-td>
                    <x-admin.table-td>{{ $entry->project?->title ?? '—' }}</x-admin.table-td>
                    <x-admin.table-td>{{ $entry->created_at->format('M j, Y') }}</x-admin.table-td>
                    <x-admin.table-td>
                        @if ($entry->status === 'approved')
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Approved') }}</span>
                        @elseif ($entry->status === 'rejected')
                            <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">{{ __('Rejected') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">{{ __('Pending') }}</span>
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @can('view', $entry)
                                <a href="{{ route('admin.visitor-book.show', $entry) }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">{{ __('View') }}</a>
                            @endcan

                            @can('update', $entry)
                                @if ($entry->status !== 'approved')
                                    <form method="POST" action="{{ route('admin.visitor-book.approve', $entry) }}">
                                        @csrf
                                        <button type="submit" class="cursor-pointer text-sm font-medium text-green-600 hover:text-green-800">{{ __('Approve') }}</button>
                                    </form>
                                @endif

                                @if ($entry->status !== 'rejected')
                                    <form method="POST" action="{{ route('admin.visitor-book.reject', $entry) }}">
                                        @csrf
                                        <button type="submit" class="cursor-pointer text-sm font-medium text-amber-600 hover:text-amber-800">{{ __('Reject') }}</button>
                                    </form>
                                @endif
                            @endcan

                            @can('delete', $entry)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-entry-{{ $entry->id }}')" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-entry-'.$entry->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Delete entry from :name?', ['name' => $entry->visitor_name]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This cannot be undone.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.visitor-book.destroy', $entry) }}" class="mt-6 flex justify-end gap-3">
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
                    <x-admin.table-td colspan="6" class="text-center text-gray-500">{{ __('No entries found.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$entries" />
</x-admin-layout>
