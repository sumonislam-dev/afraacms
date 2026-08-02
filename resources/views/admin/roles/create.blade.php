<x-admin-layout :breadcrumbs="[['label' => __('Roles'), 'url' => route('admin.roles.index')], ['label' => __('New Role')]]">
    <x-slot name="title">{{ __('New Role') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('New Role') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf
        @include('admin.roles._form', ['permissionsByModule' => $permissionsByModule, 'assigned' => $assigned])
    </form>
</x-admin-layout>
