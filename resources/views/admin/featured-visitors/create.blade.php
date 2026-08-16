<x-admin-layout :breadcrumbs="[['label' => __('Featured Visitors'), 'url' => route('admin.featured-visitors.index')], ['label' => __('Add')]]">
    <x-slot name="title">{{ __('Add Featured Visitor') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Add Featured Visitor') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.featured-visitors.store') }}">
        @csrf
        @include('admin.featured-visitors._form')
    </form>
</x-admin-layout>
