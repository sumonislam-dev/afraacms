@php
    $isEdit = isset($member);
    $currentCategoryId = old('category_id', $member->category_id ?? '');
@endphp

<x-admin.edit-layout>
    <x-slot name="main">
        <x-admin.card>
            <div class="space-y-4">
                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $member->name ?? '')" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="role" :value="__('Role / Position')" />
                    <x-text-input id="role" name="role" type="text" class="mt-1 block w-full" :value="old('role', $member->role ?? '')" placeholder="e.g. Executive Director" />
                    <x-input-error class="mt-2" :messages="$errors->get('role')" />
                </div>

                <div>
                    <x-input-label for="bio" :value="__('Bio')" />
                    <x-textarea id="bio" name="bio" class="mt-1 block w-full" rows="6">{{ old('bio', $member->bio ?? '') }}</x-textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                </div>
            </div>
        </x-admin.card>
    </x-slot>

    <x-slot name="sidebar">
        <x-admin.card :title="__('Details')">
            <div class="space-y-4">
                <div>
                    <x-input-label for="category_id" :value="__('Category')" />
                    <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('— None —') }}</option>
                        @foreach (\App\Models\TeamCategory::orderBy('name')->get() as $category)
                            <option value="{{ $category->id }}" @selected((string) $currentCategoryId === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Lets a page show just this group, e.g. Volunteers.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                </div>

                <div>
                    <x-input-label for="link" :value="__('Profile Link (optional)')" />
                    <x-text-input id="link" name="link" type="text" class="mt-1 block w-full" :value="old('link', $member->link ?? '')" placeholder="e.g. LinkedIn profile URL" />
                    <x-input-error class="mt-2" :messages="$errors->get('link')" />
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <x-input-label :value="__('Photo')" />
                    <x-admin.media-picker name="photo" :current="old('photo', $member->photo ?? null)" />
                    <x-input-error class="mt-2" :messages="$errors->get('photo')" />
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <x-input-label :value="__('Active')" />
                    <div class="mt-1 flex items-center rounded-md border border-gray-200 px-3 py-2">
                        <x-admin.toggle name="is_active" :checked="old('is_active', $member->is_active ?? true)" />
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Hidden members never show on the public site.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                </div>

                <div>
                    <x-input-label for="sort_order" :value="__('Display Order')" />
                    <x-text-input id="sort_order" name="sort_order" type="number" class="mt-1 block w-full" :value="old('sort_order', $member->sort_order ?? 0)" />
                    <p class="mt-1 text-xs text-gray-500">{{ __('Lower numbers show first.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
                </div>
            </div>
        </x-admin.card>
    </x-slot>
</x-admin.edit-layout>

<div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-6">
    <x-secondary-button type="button" onclick="window.location='{{ route('admin.team.index') }}'">{{ __('Cancel') }}</x-secondary-button>
    <x-primary-button>{{ $isEdit ? __('Update Member') : __('Add Member') }}</x-primary-button>
</div>
