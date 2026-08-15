<x-admin-layout :breadcrumbs="[['label' => __('Certificates'), 'url' => route('admin.certificates.index')], ['label' => __('Trash')]]">
    <x-slot name="title">{{ __('Trash') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Trashed Certificates') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Restore a certificate, or delete it permanently.') }}</p>
            </div>

            <x-secondary-button type="button" onclick="window.location='{{ route('admin.certificates.index') }}'">
                {{ __('Back to Certificates') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <x-admin.search-form :placeholder="__('Search trashed certificates...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Certificate #') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Recipient') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Deleted') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($certificates as $certificate)
                <tr>
                    <x-admin.table-td class="font-mono text-xs text-gray-700">{{ $certificate->certificate_number }}</x-admin.table-td>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $certificate->recipient_name }}</x-admin.table-td>
                    <x-admin.table-td>{{ $certificate->deleted_at?->format('M j, Y g:i A') }}</x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @can('restore', $certificate)
                                <form method="POST" action="{{ route('admin.certificates.restore', $certificate) }}">
                                    @csrf
                                    <button type="submit" class="cursor-pointer text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                        {{ __('Restore') }}
                                    </button>
                                </form>
                            @endcan

                            @can('forceDelete', $certificate)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'force-delete-certificate-{{ $certificate->id }}')" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete Permanently') }}
                                </button>

                                <x-modal :name="'force-delete-certificate-'.$certificate->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Permanently delete certificate for :name?', ['name' => $certificate->recipient_name]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This cannot be undone - the certificate will be gone for good.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.certificates.force-delete', $certificate) }}" class="mt-6 flex justify-end gap-3">
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

    <x-admin.pagination :paginator="$certificates" />
</x-admin-layout>
