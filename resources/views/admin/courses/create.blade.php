<x-admin-layout :breadcrumbs="[['label' => __('Courses'), 'url' => route('admin.courses.index')], ['label' => __('Add')]]">
    <x-slot name="title">{{ __('Add Course') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Add Course') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.courses.store') }}">
        @csrf
        @include('admin.courses._form')
    </form>
</x-admin-layout>
