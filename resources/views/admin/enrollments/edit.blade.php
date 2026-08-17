<x-admin-layout :breadcrumbs="[['label' => __('Enrollments'), 'url' => route('admin.enrollments.index')], ['label' => $enrollment->student->name]]">
    <x-slot name="title">{{ __('Edit Enrollment') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit Enrollment') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.enrollments.update', $enrollment) }}">
        @csrf
        @method('PUT')
        @include('admin.enrollments._form')
    </form>
</x-admin-layout>
