<x-admin-layout :breadcrumbs="[['label' => __('Projects'), 'url' => route('admin.projects.index')], ['label' => $project->title]]">
    <x-slot name="title">{{ __('Edit Project') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit Project') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.projects.update', $project) }}">
        @csrf
        @method('PUT')
        @include('admin.projects._form')
    </form>
</x-admin-layout>
