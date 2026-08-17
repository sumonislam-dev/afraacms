<x-admin-layout :breadcrumbs="[['label' => __('Certificates')]]">
    <x-slot name="title">{{ __('Certificates') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Certificates') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Issue and manage verifiable certificates.') }}</p>
            </div>

            <div class="flex items-center gap-3">
                @can('viewAny', \App\Models\Certificate::class)
                    <x-secondary-button type="button" onclick="window.location='{{ route('admin.certificates.trash') }}'">
                        {{ __('Trash') }}
                    </x-secondary-button>
                @endcan

                @can('create', \App\Models\Certificate::class)
                    <x-primary-button type="button" onclick="window.location='{{ route('admin.certificates.create') }}'">
                        {{ __('Issue Certificate') }}
                    </x-primary-button>
                @endcan
            </div>
        </div>
    </x-slot>

    <x-admin.search-form :placeholder="__('Search by recipient or certificate number...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Certificate #') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Recipient') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Program') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Issued') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Status') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($certificates as $certificate)
                <tr>
                    <x-admin.table-td class="font-mono text-xs text-gray-700">{{ $certificate->certificate_number }}</x-admin.table-td>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $certificate->recipient_name }}</x-admin.table-td>
                    <x-admin.table-td>{{ $certificate->program ?? '—' }}</x-admin.table-td>
                    <x-admin.table-td>{{ $certificate->issued_at?->format('M j, Y') }}</x-admin.table-td>
                    <x-admin.table-td>
                        @if ($certificate->status === 'valid')
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Valid') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">{{ __('Revoked') }}</span>
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('verify', ['code' => $certificate->verification_code]) }}" target="_blank" rel="noopener" class="text-sm font-medium text-gray-500 hover:text-gray-700">{{ __('Verify') }}</a>

                            @can('view', $certificate)
                                <a href="{{ route('admin.certificates.show', $certificate) }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">{{ __('View') }}</a>
                            @endcan

                            @can('update', $certificate)
                                <a href="{{ route('admin.certificates.edit', $certificate) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                            @endcan

                            @can('delete', $certificate)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-certificate-{{ $certificate->id }}')" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-certificate-'.$certificate->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Delete certificate for :name?', ['name' => $certificate->recipient_name]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This certificate will be moved to Trash and will stop verifying as valid. You can restore it or delete it permanently from there.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.certificates.destroy', $certificate) }}" class="mt-6 flex justify-end gap-3">
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
                    <x-admin.table-td colspan="6" class="text-center text-gray-500">{{ __('No certificates issued yet.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$certificates" />
</x-admin-layout>
