<x-admin-layout :breadcrumbs="[['label' => __('Students'), 'url' => route('admin.students.index')], ['label' => __('Add')]]">
    <x-slot name="title">{{ __('Add Student') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Add Student') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.students.store') }}">
        @csrf
        @include('admin.students._form')
    </form>
</x-admin-layout>
