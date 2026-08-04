<x-admin-layout :breadcrumbs="[['label' => __('Team')]]">
    <x-slot name="title">{{ __('Team') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Team') }}</h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Manage your directory of people once, then choose who shows up per page.') }}
                    @can('viewAny', \App\Models\TeamCategory::class)
                        <a href="{{ route('admin.team-categories.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">{{ __('Manage Categories') }}</a>
                    @endcan
                </p>
            </div>

            <div class="flex items-center gap-3">
                @can('viewAny', \App\Models\TeamMember::class)
                    <x-secondary-button type="button" onclick="window.location='{{ route('admin.team.trash') }}'">
                        {{ __('Trash') }}
                    </x-secondary-button>
                @endcan

                @can('create', \App\Models\TeamMember::class)
                    <x-primary-button type="button" onclick="window.location='{{ route('admin.team.create') }}'">
                        {{ __('New Member') }}
                    </x-primary-button>
                @endcan
            </div>
        </div>
    </x-slot>

    <x-admin.search-form :placeholder="__('Search team members...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Name') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Role') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Category') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Status') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($members as $member)
                <tr>
                    <x-admin.table-td class="font-medium text-gray-900">
                        <div class="flex items-center gap-3">
                            @if ($member->photo_url)
                                <img src="{{ $member->photo_url }}" alt="" class="h-8 w-8 rounded-full object-cover">
                            @else
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100">
                                    <x-icon name="user-circle" class="h-5 w-5 text-gray-300" />
                                </span>
                            @endif
                            {{ $member->name }}
                        </div>
                    </x-admin.table-td>
                    <x-admin.table-td>{{ $member->role ?? '—' }}</x-admin.table-td>
                    <x-admin.table-td>{{ $member->category?->name ?? '—' }}</x-admin.table-td>
                    <x-admin.table-td>
                        @if ($member->is_active)
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Active') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ __('Hidden') }}</span>
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @can('update', $member)
                                <a href="{{ route('admin.team.edit', $member) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                            @endcan

                            @can('delete', $member)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-member-{{ $member->id }}')" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-member-'.$member->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Delete :name?', ['name' => $member->name]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This member will be moved to Trash. You can restore them or delete them permanently from there.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.team.destroy', $member) }}" class="mt-6 flex justify-end gap-3">
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
                    <x-admin.table-td colspan="5" class="text-center text-gray-500">{{ __('No team members yet.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$members" />
</x-admin-layout>
