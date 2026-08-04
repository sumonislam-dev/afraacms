<x-admin-layout :breadcrumbs="[['label' => __('Team'), 'url' => route('admin.team.index')], ['label' => $member->name]]">
    <x-slot name="title">{{ __('Edit Team Member') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit Team Member') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.team.update', $member) }}">
        @csrf
        @method('PUT')
        @include('admin.team._form')
    </form>
</x-admin-layout>
