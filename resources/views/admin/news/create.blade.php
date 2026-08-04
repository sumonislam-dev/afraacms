<x-admin-layout :breadcrumbs="[['label' => __('News'), 'url' => route('admin.news.index')], ['label' => __('New')]]">
    <x-slot name="title">{{ __('New Post') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('New Post') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.news.store') }}">
        @csrf
        @include('admin.news._form')
    </form>
</x-admin-layout>
