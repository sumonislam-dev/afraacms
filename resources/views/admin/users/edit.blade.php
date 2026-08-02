<x-admin-layout :breadcrumbs="[['label' => __('Users'), 'url' => route('admin.users.index')], ['label' => $user->name]]">
    <x-slot name="title">{{ __('Edit User') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit User') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')
        @include('admin.users._form', ['user' => $user, 'roles' => $roles])
    </form>
</x-admin-layout>
