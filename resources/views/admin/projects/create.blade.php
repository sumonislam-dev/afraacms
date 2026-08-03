<x-admin-layout :breadcrumbs="[['label' => __('Projects'), 'url' => route('admin.projects.index')], ['label' => __('New')]]">
    <x-slot name="title">{{ __('New Project') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('New Project') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.projects.store') }}">
        @csrf
        @include('admin.projects._form')
    </form>
</x-admin-layout>
