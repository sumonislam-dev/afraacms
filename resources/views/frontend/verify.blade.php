<x-frontend-layout
    :title="__('Verify a Certificate')"
    :description="__('Verify the authenticity of a certificate issued by :site.', ['site' => setting('site_name', config('app.name'))])"
    robots="noindex, follow"
>
    <x-banner type="page" :page-title="__('Verify a Certificate')" />

    <div class="mx-auto max-w-2xl px-4 py-20 sm:px-6 lg:px-8">
        <p class="text-center text-gray-600">
            {{ __('Enter the certificate number printed on the document, or scan its QR code, to confirm it was genuinely issued by us.') }}
        </p>

        <form method="GET" action="{{ route('verify') }}" class="mx-auto mt-8 flex max-w-md gap-3">
            <label for="code" class="sr-only">{{ __('Certificate number or code') }}</label>
            <input
                id="code"
                name="code"
                type="text"
                value="{{ $identifier }}"
                placeholder="{{ __('e.g. CERT-2026-00042') }}"
                class="block w-full rounded-md border-gray-300 shadow-xs focus:border-brand-500 focus:ring-brand-500"
                required
            >
            <button type="submit" class="shrink-0 rounded-md bg-brand-500 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-600">
                {{ __('Verify') }}
            </button>
        </form>

        @if ($identifier !== '')
            <div class="mt-10">
                @if (! $certificate)
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 text-center">
                        <x-icon name="x-circle" class="mx-auto h-10 w-10 text-gray-400" />
                        <p class="mt-3 font-semibold text-gray-900">{{ __('No certificate found') }}</p>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Double-check the certificate number and try again.') }}</p>
                    </div>
                @elseif ($certificate->status === 'valid')
                    <div class="rounded-lg border border-green-200 bg-green-50 p-6">
                        <div class="flex items-center gap-2 text-green-700">
                            <x-icon name="check-circle" class="h-6 w-6" />
                            <p class="font-semibold">{{ __('Valid Certificate') }}</p>
                        </div>

                        <dl class="mt-4 space-y-2 text-sm text-gray-700">
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('Recipient') }}</dt><dd class="font-medium text-gray-900">{{ $certificate->recipient_name }}</dd></div>
                            @if ($certificate->program)
                                <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('Program') }}</dt><dd class="font-medium text-gray-900">{{ $certificate->program }}</dd></div>
                            @endif
                            @if ($certificate->project)
                                <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('Project') }}</dt><dd class="font-medium text-gray-900">{{ $certificate->project->title }}</dd></div>
                            @endif
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('Issued') }}</dt><dd class="font-medium text-gray-900">{{ $certificate->issued_at->format('F j, Y') }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ __('Certificate #') }}</dt><dd class="font-mono font-medium text-gray-900">{{ $certificate->certificate_number }}</dd></div>
                        </dl>
                    </div>
                @else
                    <div class="rounded-lg border border-red-200 bg-red-50 p-6 text-center">
                        <x-icon name="exclamation-triangle" class="mx-auto h-10 w-10 text-red-500" />
                        <p class="mt-3 font-semibold text-red-700">{{ __('This certificate has been revoked') }}</p>
                        <p class="mt-1 text-sm text-red-600">{{ __('It is no longer valid and should not be accepted.') }}</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-frontend-layout>
