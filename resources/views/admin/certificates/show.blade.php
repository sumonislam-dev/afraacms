<x-admin-layout :breadcrumbs="[['label' => __('Certificates'), 'url' => route('admin.certificates.index')], ['label' => $certificate->recipient_name]]">
    <x-slot name="title">{{ __('Certificate') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">{{ __('Certificate') }}</h2>

            @can('update', $certificate)
                <x-secondary-button type="button" onclick="window.location='{{ route('admin.certificates.edit', $certificate) }}'">
                    {{ __('Edit Certificate') }}
                </x-secondary-button>
            @endcan
        </div>
    </x-slot>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-admin.card :title="__('Recipient & Program')">
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Recipient Name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $certificate->recipient_name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Program') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $certificate->program ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Related Project') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $certificate->project?->title ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Issued On') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $certificate->issued_at?->format('M j, Y') }}</dd>
                    </div>
                </dl>

                @if ($certificate->notes)
                    <div class="mt-4 border-t border-gray-100 pt-4">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Notes') }}</dt>
                        <dd class="mt-1 whitespace-pre-line text-sm text-gray-900">{{ $certificate->notes }}</dd>
                    </div>
                @endif
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card :title="__('Certificate')">
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Certificate Number') }}</dt>
                        <dd class="mt-1 font-mono text-sm text-gray-900">{{ $certificate->certificate_number }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                        <dd class="mt-1">
                            @if ($certificate->status === 'valid')
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Valid') }}</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">{{ __('Revoked') }}</span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Public Verification Link') }}</dt>
                        <dd class="mt-1 break-all text-sm text-indigo-600">
                            <a href="{{ route('verify', ['code' => $certificate->verification_code]) }}" target="_blank" rel="noopener" class="hover:underline">
                                {{ route('verify', ['code' => $certificate->verification_code]) }}
                            </a>
                        </dd>
                    </div>
                </dl>

                <div class="mt-6 flex flex-col items-center gap-2 border-t border-gray-100 pt-6">
                    <img src="{{ route('admin.certificates.qr', $certificate) }}" alt="{{ __('Certificate QR code') }}" class="h-40 w-40 rounded-md border border-gray-200 p-2">
                    <a href="{{ route('admin.certificates.qr', $certificate) }}" download class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Download QR Code') }}</a>
                    <p class="max-w-xs text-center text-xs text-gray-500">{{ __('Print this QR code and place it on the physical certificate for verification.') }}</p>
                </div>
            </x-admin.card>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.certificates.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">&larr; {{ __('Back to Certificates') }}</a>
    </div>
</x-admin-layout>
