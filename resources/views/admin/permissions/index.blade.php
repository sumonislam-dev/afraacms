<x-admin-layout :breadcrumbs="[['label' => __('Permissions')]]">
    <x-slot name="title">{{ __('Permissions') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Permissions') }}</h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('A read-only reference of every permission available to roles, grouped by module.') }}
        </p>
    </x-slot>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($permissionsByModule as $module => $permissions)
            <x-admin.card :title="ucfirst($module)">
                <ul class="space-y-1 text-sm text-gray-600">
                    @foreach ($permissions as $permission)
                        <li class="flex items-center gap-2">
                            <span class="h-1.5 w-1.5 flex-shrink-0 rounded-full bg-indigo-400"></span>
                            {{ $permission->name }}
                        </li>
                    @endforeach
                </ul>
            </x-admin.card>
        @endforeach
    </div>
</x-admin-layout>
