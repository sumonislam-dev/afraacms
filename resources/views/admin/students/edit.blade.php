<x-admin-layout :breadcrumbs="[['label' => __('Students'), 'url' => route('admin.students.index')], ['label' => $student->name]]">
    <x-slot name="title">{{ __('Edit Student') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit Student') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.students.update', $student) }}">
        @csrf
        @method('PUT')
        @include('admin.students._form')
    </form>
</x-admin-layout>
