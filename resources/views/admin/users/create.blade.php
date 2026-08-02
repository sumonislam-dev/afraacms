<x-admin-layout :breadcrumbs="[['label' => __('Users'), 'url' => route('admin.users.index')], ['label' => __('New User')]]">
    <x-slot name="title">{{ __('New User') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('New User') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        @include('admin.users._form', ['roles' => $roles])
    </form>
</x-admin-layout>
