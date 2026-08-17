<x-admin-layout :breadcrumbs="[['label' => __('Courses'), 'url' => route('admin.courses.index')], ['label' => $course->course_name]]">
    <x-slot name="title">{{ __('Edit Course') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit Course') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.courses.update', $course) }}">
        @csrf
        @method('PUT')
        @include('admin.courses._form')
    </form>
</x-admin-layout>
