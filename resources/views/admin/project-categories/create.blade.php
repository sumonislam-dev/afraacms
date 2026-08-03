<x-admin-layout :breadcrumbs="[['label' => __('Project Categories'), 'url' => route('admin.project-categories.index')], ['label' => __('New')]]">
    <x-slot name="title">{{ __('New Category') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('New Category') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.project-categories.store') }}">
        @csrf
        @include('admin.project-categories._form')
    </form>
</x-admin-layout>
