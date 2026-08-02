<x-admin-layout :breadcrumbs="[['label' => __('Pages'), 'url' => route('admin.pages.index')], ['label' => $page->title, 'url' => route('admin.pages.sections.index', $page)], ['label' => __('New Section')]]">
    <x-slot name="title">{{ __('New Section') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('New Section') }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ __('Adding a section to :title', ['title' => $page->title]) }}</p>
    </x-slot>

    <form method="POST" action="{{ route('admin.pages.sections.store', $page) }}">
        @csrf
        @include('admin.pages.sections._section-form', ['page' => $page])
    </form>
</x-admin-layout>
