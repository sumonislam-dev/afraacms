<x-admin-layout :breadcrumbs="[['label' => __('Annual Reports'), 'url' => route('admin.annual-reports.index')], ['label' => __('New')]]">
    <x-slot name="title">{{ __('New Annual Report') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('New Annual Report') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.annual-reports.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.annual-reports._form')
    </form>
</x-admin-layout>
