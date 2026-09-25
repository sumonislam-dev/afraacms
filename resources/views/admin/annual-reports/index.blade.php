<x-admin-layout :breadcrumbs="[['label' => __('Annual Reports')]]">
    <x-slot name="title">{{ __('Annual Reports') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Annual Reports') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Manage the yearly report PDFs shown on the public site.') }}</p>
            </div>

            @can('create', \App\Models\AnnualReport::class)
                <x-primary-button type="button" onclick="window.location='{{ route('admin.annual-reports.create') }}'">
                    {{ __('Add Report') }}
                </x-primary-button>
            @endcan
        </div>
    </x-slot>

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Title') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Year') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('File') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Status') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($reports as $report)
                <tr>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $report->title }}</x-admin.table-td>
                    <x-admin.table-td>{{ $report->year }}</x-admin.table-td>
                    <x-admin.table-td>
                        @if ($report->attachment_url)
                            <a href="{{ $report->attachment_url }}" target="_blank" rel="noopener" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ $report->attachment_file_name }}</a>
                        @else
                            <span class="text-sm text-gray-400">{{ __('No file') }}</span>
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        @if ($report->is_active)
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Active') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ __('Inactive') }}</span>
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @can('update', $report)
                                <a href="{{ route('admin.annual-reports.edit', $report) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                            @endcan

                            @can('delete', $report)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-annual-report-{{ $report->id }}')" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-annual-report-'.$report->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Delete :title?', ['title' => $report->title]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This will permanently remove this report. This action cannot be undone.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.annual-reports.destroy', $report) }}" class="mt-6 flex justify-end gap-3">
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
                    <x-admin.table-td colspan="5" class="text-center text-gray-500">{{ __('No annual reports yet.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$reports" />
</x-admin-layout>
