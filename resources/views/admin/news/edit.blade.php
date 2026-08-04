<x-admin-layout :breadcrumbs="[['label' => __('News'), 'url' => route('admin.news.index')], ['label' => $post->title]]">
    <x-slot name="title">{{ __('Edit Post') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit Post') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.news.update', $post) }}">
        @csrf
        @method('PUT')
        @include('admin.news._form')
    </form>
</x-admin-layout>
