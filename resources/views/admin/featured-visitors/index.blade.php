<x-admin-layout :breadcrumbs="[['label' => __('Featured Visitors')]]">
    <x-slot name="title">{{ __('Featured Visitors') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Featured Visitors') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Showcase notable visitors and dignitaries who visited RSUF.') }}</p>
            </div>

            <div class="flex items-center gap-3">
                @can('viewAny', \App\Models\FeaturedVisitor::class)
                    <x-secondary-button type="button" onclick="window.location='{{ route('admin.featured-visitors.trash') }}'">
                        {{ __('Trash') }}
                    </x-secondary-button>
                @endcan

                @can('create', \App\Models\FeaturedVisitor::class)
                    <x-primary-button type="button" onclick="window.location='{{ route('admin.featured-visitors.create') }}'">
                        {{ __('Add Visitor') }}
                    </x-primary-button>
                @endcan
            </div>
        </div>
    </x-slot>

    <x-admin.search-form :placeholder="__('Search by name...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Name') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Organization') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Country') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Visited') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Status') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($visitors as $visitor)
                <tr>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $visitor->name }}</x-admin.table-td>
                    <x-admin.table-td>{{ $visitor->organization ?? '—' }}</x-admin.table-td>
                    <x-admin.table-td>{{ $visitor->country }}</x-admin.table-td>
                    <x-admin.table-td>{{ $visitor->visited_at?->format('M j, Y') }}</x-admin.table-td>
                    <x-admin.table-td>
                        @if ($visitor->is_active)
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Active') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ __('Hidden') }}</span>
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @can('update', $visitor)
                                <a href="{{ route('admin.featured-visitors.edit', $visitor) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                            @endcan

                            @can('delete', $visitor)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-visitor-{{ $visitor->id }}')" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-visitor-'.$visitor->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Delete :name?', ['name' => $visitor->name]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This visitor will be moved to Trash. You can restore it or delete it permanently from there.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.featured-visitors.destroy', $visitor) }}" class="mt-6 flex justify-end gap-3">
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
                    <x-admin.table-td colspan="6" class="text-center text-gray-500">{{ __('No featured visitors yet.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$visitors" />
</x-admin-layout>
