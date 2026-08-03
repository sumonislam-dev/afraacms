<x-admin-layout :breadcrumbs="[['label' => __('Banners'), 'url' => route('admin.banners.index')], ['label' => __('New')]]">
    <x-slot name="title">{{ __('New Banner') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('New Banner') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.banners.store') }}">
        @csrf
        @include('admin.banners._form')
    </form>
</x-admin-layout>
