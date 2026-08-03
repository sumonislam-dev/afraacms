<x-admin-layout :breadcrumbs="[['label' => __('Project Categories'), 'url' => route('admin.project-categories.index')], ['label' => $category->name]]">
    <x-slot name="title">{{ __('Edit Category') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit Category') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.project-categories.update', $category) }}">
        @csrf
        @method('PUT')
        @include('admin.project-categories._form')
    </form>
</x-admin-layout>
