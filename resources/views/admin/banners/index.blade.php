<x-admin-layout :breadcrumbs="[['label' => __('Banners')]]">
    <x-slot name="title">{{ __('Banners') }}</x-slot>

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">{{ __('Banners') }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('Manage the promotional banners shown across the site.') }}</p>
        </div>
    </x-slot>

    <div x-data="{ tab: '{{ array_key_first(config('banners.types')) }}' }">
        <nav class="flex gap-1 overflow-x-auto pb-2">
            @foreach (config('banners.types', []) as $typeKey => $typeConfig)
                <button
                    type="button"
                    @click="tab = '{{ $typeKey }}'"
                    :class="tab === '{{ $typeKey }}' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50'"
                    class="flex shrink-0 cursor-pointer items-center gap-2 rounded-md px-3 py-2 text-sm font-medium whitespace-nowrap"
                >
                    {{ $typeConfig['label'] }}
                    <span class="rounded-full bg-gray-100 px-1.5 text-xs text-gray-500">{{ $banners->get($typeKey, collect())->count() }}</span>
                </button>
            @endforeach
        </nav>

        @foreach (config('banners.types', []) as $typeKey => $typeConfig)
            <div x-show="tab === '{{ $typeKey }}'" class="mt-4">
                <div class="mb-4 flex items-center justify-between">
                    <p class="text-sm text-gray-500">{{ $typeConfig['description'] }}</p>

                    @can('create', \App\Models\Banner::class)
                        <x-primary-button type="button" onclick="window.location='{{ route('admin.banners.create', ['type' => $typeKey]) }}'">
                            {{ __('Add Banner') }}
                        </x-primary-button>
                    @endcan
                </div>

                <x-admin.table>
                    <thead>
                        <tr>
                            <x-admin.table-th>{{ __('Title') }}</x-admin.table-th>
                            <x-admin.table-th>{{ __('Priority') }}</x-admin.table-th>
                            <x-admin.table-th>{{ __('Schedule') }}</x-admin.table-th>
                            <x-admin.table-th>{{ __('Status') }}</x-admin.table-th>
                            <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($banners->get($typeKey, collect()) as $banner)
                            <tr>
                                <x-admin.table-td class="font-medium text-gray-900">{{ $banner->title ?: '—' }}</x-admin.table-td>
                                <x-admin.table-td>{{ $banner->sort_order }}</x-admin.table-td>
                                <x-admin.table-td class="text-xs text-gray-500">
                                    {{ $banner->starts_at?->format('M j, Y g:i A') ?? __('Always') }}
                                    &rarr;
                                    {{ $banner->ends_at?->format('M j, Y g:i A') ?? __('Never') }}
                                </x-admin.table-td>
                                <x-admin.table-td>
                                    @if ($banner->is_active)
                                        <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Active') }}</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ __('Inactive') }}</span>
                                    @endif
                                </x-admin.table-td>
                                <x-admin.table-td>
                                    <div class="flex items-center justify-end gap-3">
                                        @can('update', $banner)
                                            <a href="{{ route('admin.banners.edit', $banner) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                        @endcan

                                        @can('delete', $banner)
                                            <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-banner-{{ $banner->id }}')" class="text-sm font-medium text-red-600 hover:text-red-900">
                                                {{ __('Delete') }}
                                            </button>

                                            <x-modal :name="'delete-banner-'.$banner->id">
                                                <div class="p-6">
                                                    <h2 class="text-lg font-medium text-gray-900">
                                                        {{ __('Delete :title?', ['title' => $banner->title ?: __('this banner')]) }}
                                                    </h2>
                                                    <p class="mt-1 text-sm text-gray-600">
                                                        {{ __('This will permanently remove this banner. This action cannot be undone.') }}
                                                    </p>

                                                    <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" class="mt-6 flex justify-end gap-3">
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
                                <x-admin.table-td colspan="5" class="text-center text-gray-500">{{ __('No banners for this placement yet.') }}</x-admin.table-td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-admin.table>
            </div>
        @endforeach
    </div>
</x-admin-layout>
