<x-admin-layout :breadcrumbs="[['label' => __('Certificates'), 'url' => route('admin.certificates.index')], ['label' => $certificate->recipient_name]]">
    <x-slot name="title">{{ __('Edit Certificate') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit Certificate') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.certificates.update', $certificate) }}">
        @csrf
        @method('PUT')
        @include('admin.certificates._form')
    </form>
</x-admin-layout>
