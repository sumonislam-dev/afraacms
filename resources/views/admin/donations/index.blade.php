<x-admin-layout :breadcrumbs="[['label' => __('Donations')]]">
    <x-slot name="title">{{ __('Donations') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Donations') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Record donations and send automated receipts.') }}</p>
            </div>

            <div class="flex items-center gap-3">
                @can('viewAny', \App\Models\Donation::class)
                    <x-secondary-button type="button" onclick="window.location='{{ route('admin.donations.trash') }}'">
                        {{ __('Trash') }}
                    </x-secondary-button>
                @endcan

                @can('create', \App\Models\Donation::class)
                    <x-primary-button type="button" onclick="window.location='{{ route('admin.donations.create') }}'">
                        {{ __('Record Donation') }}
                    </x-primary-button>
                @endcan
            </div>
        </div>
    </x-slot>

    <x-admin.search-form :placeholder="__('Search by donor or receipt number...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('Receipt #') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Donor') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Amount') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Method') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Date') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Status') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Receipt') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($donations as $donation)
                <tr>
                    <x-admin.table-td class="font-mono text-xs text-gray-700">{{ $donation->receipt_number }}</x-admin.table-td>
                    <x-admin.table-td class="font-medium text-gray-900">{{ $donation->donor_name }}</x-admin.table-td>
                    <x-admin.table-td>{{ $donation->currency }} {{ number_format($donation->amount, 2) }}</x-admin.table-td>
                    <x-admin.table-td>{{ config("donations.methods.{$donation->method}", $donation->method) }}</x-admin.table-td>
                    <x-admin.table-td>{{ $donation->donated_at?->format('M j, Y') }}</x-admin.table-td>
                    <x-admin.table-td>
                        @if ($donation->status === 'completed')
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Completed') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">{{ __('Refunded') }}</span>
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        @if ($donation->receipt_sent_at)
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Sent') }}</span>
                        @elseif (! $donation->donor_email)
                            <span class="text-xs text-gray-400">{{ __('No email') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ __('Not sent') }}</span>
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @can('update', $donation)
                                <a href="{{ route('admin.donations.edit', $donation) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                            @endcan

                            @can('delete', $donation)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-donation-{{ $donation->id }}')" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-donation-'.$donation->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Delete donation from :name?', ['name' => $donation->donor_name]) }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This donation will be moved to Trash. You can restore it or delete it permanently from there.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.donations.destroy', $donation) }}" class="mt-6 flex justify-end gap-3">
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
                    <x-admin.table-td colspan="8" class="text-center text-gray-500">{{ __('No donations recorded yet.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$donations" />
</x-admin-layout>
