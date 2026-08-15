<x-admin-layout :breadcrumbs="[['label' => __('Donations'), 'url' => route('admin.donations.index')], ['label' => __('Record')]]">
    <x-slot name="title">{{ __('Record Donation') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Record Donation') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.donations.store') }}">
        @csrf
        @include('admin.donations._form')
    </form>
</x-admin-layout>
