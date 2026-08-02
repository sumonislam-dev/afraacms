<x-admin-layout>
    <x-slot name="title">{{ __('Dashboard') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Dashboard') }}</h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('Welcome back, :name. Here\'s an overview of your site.', ['name' => Auth::user()->name]) }}
        </p>
    </x-slot>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-admin.card>
            <p class="text-sm font-medium text-gray-500">{{ __('Logged in as') }}</p>
            <p class="mt-2 truncate text-2xl font-semibold text-gray-900">{{ Auth::user()->name }}</p>
            <p class="mt-1 truncate text-sm text-gray-500">{{ Auth::user()->email }}</p>
        </x-admin.card>

        <x-admin.card>
            <p class="text-sm font-medium text-gray-500">{{ __('Assigned Role') }}</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ Auth::user()->getRoleNames()->join(', ') ?: __('None') }}
            </p>
        </x-admin.card>

        <x-admin.card>
            <p class="text-sm font-medium text-gray-500">{{ __('Total Users') }}</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $totalUsers }}</p>
        </x-admin.card>

        <x-admin.card>
            <p class="text-sm font-medium text-gray-500">{{ __('Total Roles') }}</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $totalRoles }}</p>
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
