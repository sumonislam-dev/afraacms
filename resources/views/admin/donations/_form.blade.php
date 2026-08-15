@php
    $isEdit = isset($donation);
    $currentStatus = old('status', $donation->status ?? 'completed');
    $currentProjectId = old('project_id', $donation->project_id ?? '');
    $currentCurrency = old('currency', $donation->currency ?? config('donations.default_currency'));
    $currentMethod = old('method', $donation->method ?? array_key_first(config('donations.methods')));
    $currentDonatedAt = old('donated_at', optional($donation->donated_at ?? null)->format('Y-m-d') ?? now()->format('Y-m-d'));
@endphp

<x-admin.edit-layout>
    <x-slot name="main">
        <x-admin.card>
            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="donor_name" :value="__('Donor Name')" />
                        <x-text-input
                            id="donor_name"
                            name="donor_name"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('donor_name', $donation->donor_name ?? '')"
                            required
                            autofocus
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('donor_name')" />
                    </div>

                    <div>
                        <x-input-label for="donor_email" :value="__('Donor Email')" />
                        <x-text-input id="donor_email" name="donor_email" type="email" class="mt-1 block w-full" :value="old('donor_email', $donation->donor_email ?? '')" />
                        <p class="mt-1 text-xs text-gray-500">{{ __('Optional - the receipt is only emailed if this is filled in.') }}</p>
                        <x-input-error class="mt-2" :messages="$errors->get('donor_email')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="notes" :value="__('Internal Notes')" />
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $donation->notes ?? '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">{{ __('For admin reference only - never shown to the donor.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                </div>
            </div>
        </x-admin.card>

        @if ($isEdit)
            <x-admin.card :title="__('Receipt')">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-700">
                            <span class="font-medium text-gray-900">{{ __('Receipt Number') }}:</span> {{ $donation->receipt_number }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            @if ($donation->receipt_sent_at)
                                {{ __('Sent :date', ['date' => $donation->receipt_sent_at->format('M j, Y g:i A')]) }}
                            @elseif (! $donation->donor_email)
                                {{ __('No donor email on file - nothing to send.') }}
                            @else
                                {{ __('Not sent yet.') }}
                            @endif
                        </p>
                    </div>

                    @if ($donation->donor_email)
                        {{-- A real <form> can't nest inside this page's main edit <form> (HTML
                             forbids nested forms - browsers silently close the outer one early,
                             breaking its submit button), so this posts via fetch instead. --}}
                        <div x-data="{ sending: false }">
                            <x-secondary-button
                                type="button"
                                x-bind:disabled="sending"
                                x-on:click="
                                    sending = true;
                                    fetch('{{ route('admin.donations.resend-receipt', $donation) }}', {
                                        method: 'POST',
                                        redirect: 'manual',
                                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                                    }).finally(() => window.location.reload());
                                "
                            >
                                <span x-show="!sending">{{ $donation->receipt_sent_at ? __('Resend Receipt') : __('Send Receipt') }}</span>
                                <span x-show="sending" style="display: none;">{{ __('Sending...') }}</span>
                            </x-secondary-button>
                        </div>
                    @endif
                </div>
            </x-admin.card>
        @endif
    </x-slot>

    <x-slot name="sidebar">
        <x-admin.card :title="__('Donation Details')">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="amount" :value="__('Amount')" />
                        <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('amount', $donation->amount ?? '')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                    </div>

                    <div>
                        <x-input-label for="currency" :value="__('Currency')" />
                        <select id="currency" name="currency" required class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach (config('donations.currencies') as $code => $label)
                                <option value="{{ $code }}" @selected($currentCurrency === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('currency')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="method" :value="__('Payment Method')" />
                    <select id="method" name="method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach (config('donations.methods') as $code => $label)
                            <option value="{{ $code }}" @selected($currentMethod === $code)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('method')" />
                </div>

                <div>
                    <x-input-label for="donated_at" :value="__('Donation Date')" />
                    <x-text-input id="donated_at" name="donated_at" type="date" class="mt-1 block w-full" :value="$currentDonatedAt" required />
                    <x-input-error class="mt-2" :messages="$errors->get('donated_at')" />
                </div>

                <div>
                    <x-input-label for="project_id" :value="__('Related Project')" />
                    <select id="project_id" name="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('— None —') }}</option>
                        @foreach (\App\Models\Project::orderBy('title')->get() as $project)
                            <option value="{{ $project->id }}" @selected((string) $currentProjectId === (string) $project->id)>{{ $project->title }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Optional. Which project/campaign this donation supports.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('project_id')" />
                </div>

                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="completed" @selected($currentStatus === 'completed')>{{ __('Completed') }}</option>
                        <option value="refunded" @selected($currentStatus === 'refunded')>{{ __('Refunded') }}</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                </div>
            </div>
        </x-admin.card>
    </x-slot>
</x-admin.edit-layout>

<div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-6">
    <x-secondary-button type="button" onclick="window.location='{{ route('admin.donations.index') }}'">{{ __('Cancel') }}</x-secondary-button>
    <x-primary-button>{{ $isEdit ? __('Update Donation') : __('Record Donation') }}</x-primary-button>
</div>
