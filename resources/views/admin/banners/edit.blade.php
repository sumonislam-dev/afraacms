<x-admin-layout :breadcrumbs="[['label' => __('Banners'), 'url' => route('admin.banners.index')], ['label' => $banner->title ?: __('Edit')]]">
    <x-slot name="title">{{ __('Edit Banner') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit Banner') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.banners.update', $banner) }}">
        @csrf
        @method('PUT')
        @include('admin.banners._form')
    </form>
</x-admin-layout>
