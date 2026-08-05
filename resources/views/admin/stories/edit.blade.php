<x-admin-layout :breadcrumbs="[['label' => __('Success Stories'), 'url' => route('admin.stories.index')], ['label' => $story->title]]">
    <x-slot name="title">{{ __('Edit Story') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit Story') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.stories.update', $story) }}">
        @csrf
        @method('PUT')
        @include('admin.stories._form')
    </form>
</x-admin-layout>
