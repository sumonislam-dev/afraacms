<x-admin-layout :breadcrumbs="[['label' => __('Annual Reports'), 'url' => route('admin.annual-reports.index')], ['label' => $report->title]]">
    <x-slot name="title">{{ __('Edit Annual Report') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit Annual Report') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.annual-reports.update', $report) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.annual-reports._form')
    </form>
</x-admin-layout>
