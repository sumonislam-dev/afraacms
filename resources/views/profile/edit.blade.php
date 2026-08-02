<x-admin-layout :breadcrumbs="[['label' => __('Profile')]]">
    <x-slot name="title">{{ __('Profile') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Profile') }}</h2>
    </x-slot>

    <div class="space-y-6">
        <x-admin.card>
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </x-admin.card>

        <x-admin.card>
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </x-admin.card>

        <x-admin.card>
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-admin.card>
    </div>
</x-admin-layout>
