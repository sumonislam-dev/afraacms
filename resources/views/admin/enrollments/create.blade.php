<x-admin-layout :breadcrumbs="[['label' => __('Enrollments'), 'url' => route('admin.enrollments.index')], ['label' => __('Add')]]">
    <x-slot name="title">{{ __('Add Enrollment') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Add Enrollment') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.enrollments.store') }}">
        @csrf
        @include('admin.enrollments._form')
    </form>
</x-admin-layout>
