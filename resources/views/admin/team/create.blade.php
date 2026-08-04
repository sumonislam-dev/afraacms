<x-admin-layout :breadcrumbs="[['label' => __('Team'), 'url' => route('admin.team.index')], ['label' => __('New')]]">
    <x-slot name="title">{{ __('New Team Member') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('New Team Member') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.team.store') }}">
        @csrf
        @include('admin.team._form')
    </form>
</x-admin-layout>
