@php
    $isEdit = isset($visitor);
    $currentVisitedAt = old('visited_at', optional($visitor->visited_at ?? null)->format('Y-m-d') ?? now()->format('Y-m-d'));
@endphp

<x-admin.edit-layout>
    <x-slot name="main">
        <x-admin.card>
            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $visitor->name ?? '')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="organization" :value="__('Organization (optional)')" />
                        <x-text-input id="organization" name="organization" type="text" class="mt-1 block w-full" :value="old('organization', $visitor->organization ?? '')" placeholder="e.g. UNICEF Bangladesh" />
                        <x-input-error class="mt-2" :messages="$errors->get('organization')" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="country" :value="__('Country')" />
                        <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" :value="old('country', $visitor->country ?? '')" required placeholder="e.g. Bangladesh" />
                        <x-input-error class="mt-2" :messages="$errors->get('country')" />
                    </div>

                    <div>
                        <x-input-label for="visited_at" :value="__('Visit Date')" />
                        <x-text-input id="visited_at" name="visited_at" type="date" class="mt-1 block w-full" :value="$currentVisitedAt" required />
                        <x-input-error class="mt-2" :messages="$errors->get('visited_at')" />
                    </div>
                </div>
            </div>
        </x-admin.card>
    </x-slot>

    <x-slot name="sidebar">
        <x-admin.card :title="__('Display')">
            <div class="space-y-4">
                <div>
                    <x-input-label :value="__('Photo')" />
                    <x-admin.media-picker name="photo" :current="old('photo', $visitor->photo ?? null)" />
                    <x-input-error class="mt-2" :messages="$errors->get('photo')" />
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <x-input-label :value="__('Active')" />
                    <div class="mt-1 flex items-center rounded-md border border-gray-200 px-3 py-2">
                        <x-admin.toggle name="is_active" :checked="old('is_active', $visitor->is_active ?? true)" />
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Hidden visitors never show on the public site.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                </div>

                <div>
                    <x-input-label for="sort_order" :value="__('Display Order')" />
                    <x-text-input id="sort_order" name="sort_order" type="number" class="mt-1 block w-full" :value="old('sort_order', $visitor->sort_order ?? 0)" />
                    <p class="mt-1 text-xs text-gray-500">{{ __('Lower numbers show first.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
                </div>
            </div>
        </x-admin.card>
    </x-slot>
</x-admin.edit-layout>

<div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-6">
    <x-secondary-button type="button" onclick="window.location='{{ route('admin.featured-visitors.index') }}'">{{ __('Cancel') }}</x-secondary-button>
    <x-primary-button>{{ $isEdit ? __('Update Visitor') : __('Add Visitor') }}</x-primary-button>
</div>
