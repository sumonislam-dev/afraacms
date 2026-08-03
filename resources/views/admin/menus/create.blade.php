<x-admin-layout :breadcrumbs="[['label' => __('Menus'), 'url' => route('admin.menus.index')], ['label' => __('New Menu')]]">
    <x-slot name="title">{{ __('New Menu') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('New Menu') }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('admin.menus.store') }}">
        @csrf

        <x-admin.form-section :title="__('New Menu')">
            @include('admin.menus._form')

            <x-slot name="actions">
                <x-secondary-button type="button" onclick="window.location='{{ route('admin.menus.index') }}'">{{ __('Cancel') }}</x-secondary-button>
                <x-primary-button>{{ __('Create Menu') }}</x-primary-button>
            </x-slot>
        </x-admin.form-section>
    </form>
</x-admin-layout>
