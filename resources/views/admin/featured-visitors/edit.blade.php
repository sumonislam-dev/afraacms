<x-admin-layout :breadcrumbs="[['label' => __('Featured Visitors'), 'url' => route('admin.featured-visitors.index')], ['label' => $visitor->name]]">
    <x-slot name="title">{{ __('Edit Featured Visitor') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit Featured Visitor') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.featured-visitors.update', $visitor) }}">
        @csrf
        @method('PUT')
        @include('admin.featured-visitors._form')
    </form>
</x-admin-layout>
