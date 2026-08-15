<x-admin-layout :breadcrumbs="[['label' => __('Certificates'), 'url' => route('admin.certificates.index')], ['label' => __('Issue')]]">
    <x-slot name="title">{{ __('Issue Certificate') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Issue Certificate') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.certificates.store') }}">
        @csrf
        @include('admin.certificates._form')
    </form>
</x-admin-layout>
