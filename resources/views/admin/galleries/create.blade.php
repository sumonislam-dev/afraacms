<x-admin-layout :breadcrumbs="[['label' => __('Galleries'), 'url' => route('admin.galleries.index')], ['label' => __('New')]]">
    <x-slot name="title">{{ __('New Album') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('New Album') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.galleries.store') }}">
        @csrf
        @include('admin.galleries._form')
    </form>
</x-admin-layout>
