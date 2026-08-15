<x-admin-layout :breadcrumbs="[['label' => __('Donations'), 'url' => route('admin.donations.index')], ['label' => $donation->donor_name]]">
    <x-slot name="title">{{ __('Edit Donation') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit Donation') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.donations.update', $donation) }}">
        @csrf
        @method('PUT')
        @include('admin.donations._form')
    </form>
</x-admin-layout>
