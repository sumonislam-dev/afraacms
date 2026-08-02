<x-admin-layout :breadcrumbs="[['label' => __('Pages'), 'url' => route('admin.pages.index')], ['label' => __('New Page')]]">
    <x-slot name="title">{{ __('New Page') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('New Page') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.pages.store') }}">
        @csrf
        @include('admin.pages._form')
    </form>
</x-admin-layout>
