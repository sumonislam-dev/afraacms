<x-admin-layout :breadcrumbs="[['label' => __('Success Stories'), 'url' => route('admin.stories.index')], ['label' => __('New')]]">
    <x-slot name="title">{{ __('New Story') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('New Story') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.stories.store') }}">
        @csrf
        @include('admin.stories._form')
    </form>
</x-admin-layout>
