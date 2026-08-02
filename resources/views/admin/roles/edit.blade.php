<x-admin-layout :breadcrumbs="[['label' => __('Roles'), 'url' => route('admin.roles.index')], ['label' => $role->name]]">
    <x-slot name="title">{{ __('Edit Role') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit Role') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.roles.update', $role) }}">
        @csrf
        @method('PUT')
        @include('admin.roles._form', ['role' => $role, 'permissionsByModule' => $permissionsByModule, 'assigned' => $assigned])
    </form>
</x-admin-layout>
