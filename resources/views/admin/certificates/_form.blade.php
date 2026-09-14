@php
    $isEdit = isset($certificate);
    $currentStatus = old('status', $certificate->status ?? 'valid');
    $currentProjectId = old('project_id', $certificate->project_id ?? '');
    $currentEnrollmentId = old('enrollment_id', $certificate->enrollment_id ?? '');
    $currentIssuedAt = old('issued_at', optional($certificate->issued_at ?? null)->format('Y-m-d') ?? now()->format('Y-m-d'));
@endphp

<x-admin.edit-layout>
    <x-slot name="main">
        <x-admin.card :title="__('Recipient')">
            <div
                x-data="{
                    recipientMode: @js(old('enrollment_id', $certificate->enrollment_id ?? null) ? 'enrollment' : 'manual'),
                }"
            >
                <div class="inline-flex rounded-md border border-gray-300 bg-white p-0.5 text-sm">
                    <label class="cursor-pointer rounded-sm px-3 py-1 font-medium transition-colors" :class="recipientMode === 'manual' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50'">
                        <input type="radio" x-model="recipientMode" value="manual" class="sr-only">
                        {{ __('Manual') }}
                    </label>
                    <label class="cursor-pointer rounded-sm px-3 py-1 font-medium transition-colors" :class="recipientMode === 'enrollment' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50'">
                        <input type="radio" x-model="recipientMode" value="enrollment" class="sr-only">
                        {{ __('From Enrollment') }}
                    </label>
                </div>

                <div class="mt-4" x-show="recipientMode === 'enrollment'" style="display: none;">
                    <x-input-label for="enrollment_id" :value="__('Enrollment (Student + Course)')" />
                    <select
                        id="enrollment_id"
                        name="enrollment_id"
                        x-bind:disabled="recipientMode !== 'enrollment'"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">{{ __('— Select —') }}</option>
                        @foreach ($enrollments as $enrollment)
                            <option value="{{ $enrollment->id }}" @selected((string) $currentEnrollmentId === (string) $enrollment->id)>
                                {{ $enrollment->student->name }} — {{ $enrollment->course->course_name }} ({{ $enrollment->session }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Recipient name and program are taken automatically from the enrollment - not retyped.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('enrollment_id')" />
                </div>

                <div class="mt-4 space-y-4" x-show="recipientMode === 'manual'" style="display: none;">
                    <div>
                        <x-input-label for="recipient_name" :value="__('Recipient Name')" />
                        <x-text-input
                            id="recipient_name"
                            name="recipient_name"
                            type="text"
                            class="mt-1 block w-full"
                            x-bind:disabled="recipientMode !== 'manual'"
                            :value="old('recipient_name', $certificate->recipient_name ?? '')"
                            autofocus
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('recipient_name')" />
                    </div>

                    <div>
                        <x-input-label for="program" :value="__('Program / Course')" />
                        <x-text-input id="program" name="program" type="text" class="mt-1 block w-full" x-bind:disabled="recipientMode !== 'manual'" :value="old('program', $certificate->program ?? '')" placeholder="{{ __('e.g. Web Development Training') }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('program')" />
                    </div>
                </div>
            </div>
        </x-admin.card>

        <x-admin.card>
            <div class="space-y-4">
                <div>
                    <x-input-label for="notes" :value="__('Internal Notes')" />
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $certificate->notes ?? '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">{{ __('For admin reference only - never shown on the public verification page.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                </div>
            </div>
        </x-admin.card>

        @if ($isEdit)
            <x-admin.card :title="__('Verification')">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="space-y-3">
                        <div>
                            <x-input-label :value="__('Certificate Number')" />
                            <p class="mt-1 font-mono text-sm text-gray-900">{{ $certificate->certificate_number }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Printed on the certificate for manual lookup.') }}</p>
                        </div>

                        <div>
                            <x-input-label :value="__('Public Verification Link')" />
                            <p class="mt-1 break-all text-sm text-indigo-600">
                                <a href="{{ route('verify', ['code' => $certificate->verification_code]) }}" target="_blank" rel="noopener" class="hover:underline">
                                    {{ route('verify', ['code' => $certificate->verification_code]) }}
                                </a>
                            </p>
                            <p class="mt-1 text-xs text-gray-500">{{ __('This is the link encoded in the QR code below.') }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col items-center gap-2 sm:items-end">
                        <img src="{{ route('admin.certificates.qr', $certificate) }}" alt="{{ __('Certificate QR code') }}" class="h-40 w-40 rounded-md border border-gray-200 p-2">
                        <a href="{{ route('admin.certificates.qr', $certificate) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Download QR Code') }}</a>
                    </div>
                </div>
            </x-admin.card>
        @endif
    </x-slot>

    <x-slot name="sidebar">
        <x-admin.card :title="__('Details')">
            <div class="space-y-4">
                <div>
                    <x-input-label for="project_id" :value="__('Related Project')" />
                    <select id="project_id" name="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('— None —') }}</option>
                        @foreach (\App\Models\Project::orderBy('title')->get() as $project)
                            <option value="{{ $project->id }}" @selected((string) $currentProjectId === (string) $project->id)>{{ $project->title }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Optional. Links this certificate to the training/project it was issued for.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('project_id')" />
                </div>

                <div>
                    <x-input-label for="issued_at" :value="__('Issue Date')" />
                    <x-text-input id="issued_at" name="issued_at" type="date" class="mt-1 block w-full" :value="$currentIssuedAt" required />
                    <x-input-error class="mt-2" :messages="$errors->get('issued_at')" />
                </div>

                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="valid" @selected($currentStatus === 'valid')>{{ __('Valid') }}</option>
                        <option value="revoked" @selected($currentStatus === 'revoked')>{{ __('Revoked') }}</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Revoking immediately makes the public verification page show this certificate as invalid.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                </div>
            </div>
        </x-admin.card>
    </x-slot>
</x-admin.edit-layout>

<div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-6">
    <x-secondary-button type="button" onclick="window.location='{{ route('admin.certificates.index') }}'">{{ __('Cancel') }}</x-secondary-button>
    <x-primary-button>{{ $isEdit ? __('Update Certificate') : __('Issue Certificate') }}</x-primary-button>
</div>
