<x-admin-layout :breadcrumbs="[['label' => __('Menus'), 'url' => route('admin.menus.index')], ['label' => __('New Menu')]]">
    <x-slot name="title">{{ __('New Menu') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('New Menu') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.menus.store') }}">
        @csrf
        @include('admin.menus._form')
    </form>
</x-admin-layout>
