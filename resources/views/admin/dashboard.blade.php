<x-admin-layout>
    <x-slot name="title">{{ __('Dashboard') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Dashboard') }}</h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('Welcome back, :name. Here\'s an overview of your site.', ['name' => Auth::user()->name ?? 'Admin']) }}
        </p>
    </x-slot>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-admin.card>
            <p class="text-sm font-medium text-gray-500">{{ __('Pages') }}</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">&mdash;</p>
        </x-admin.card>

        <x-admin.card>
            <p class="text-sm font-medium text-gray-500">{{ __('Media Files') }}</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">&mdash;</p>
        </x-admin.card>

        <x-admin.card>
            <p class="text-sm font-medium text-gray-500">{{ __('Menus') }}</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">&mdash;</p>
        </x-admin.card>

        <x-admin.card>
            <p class="text-sm font-medium text-gray-500">{{ __('Registered Users') }}</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">&mdash;</p>
        </x-admin.card>
    </div>

    <div class="mt-6">
        <x-admin.card :title="__('Getting Started')">
            <p class="text-sm text-gray-600">
                {{ __('This is the admin framework placeholder. As content modules (Pages, Media, Menus, Settings) are built in upcoming phases, their data and management screens will appear here, and their sidebar entries will activate automatically once each module registers its routes.') }}
            </p>
        </x-admin.card>
    </div>
</x-admin-layout>
