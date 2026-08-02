@php
    $isEdit = isset($user);
@endphp

<x-admin.form-section
    :title="$isEdit ? __('User Details') : __('New User')"
    :description="__('Basic account information and role assignment.')"
>
    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email ?? '')" required autocomplete="username" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <div>
        <x-input-label for="password" :value="$isEdit ? __('New Password') : __('Password')" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
        @if ($isEdit)
            <p class="mt-1 text-sm text-gray-500">{{ __('Leave blank to keep the current password.') }}</p>
        @endif
    </div>

    <div>
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
    </div>

    <div>
        <x-input-label for="role" :value="__('Role')" />
        <select id="role" name="role" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">{{ __('Select a role') }}</option>
            @foreach ($roles as $roleName)
                <option value="{{ $roleName }}" @selected(old('role', $user->roles->first()->name ?? '') === $roleName)>{{ $roleName }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('role')" />
    </div>

    <div class="flex items-center gap-2">
        <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $user->is_active ?? true)) class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
        <x-input-label for="is_active" :value="__('Active')" class="mb-0" />
    </div>

    <x-slot name="actions">
        <x-secondary-button type="button" onclick="window.location='{{ route('admin.users.index') }}'">{{ __('Cancel') }}</x-secondary-button>
        <x-primary-button>{{ $isEdit ? __('Update User') : __('Create User') }}</x-primary-button>
    </x-slot>
</x-admin.form-section>
