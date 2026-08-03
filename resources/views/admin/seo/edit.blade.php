<x-admin-layout :breadcrumbs="[['label' => __('SEO')]]">
    <x-slot name="title">{{ __('SEO') }}</x-slot>

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">{{ __('SEO') }}</h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('Sitemap and robots configuration. Per-page meta title/description/canonical are set on each page, project, or album\'s own edit screen.') }}
            </p>
        </div>
    </x-slot>

    <x-admin.card>
        <form method="POST" action="{{ route('admin.seo.update') }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                @foreach ($fields as $key => $field)
                    @include('admin.settings._field', ['key' => $key, 'field' => $field, 'value' => $values[$key] ?? null, 'canEdit' => true])
                @endforeach
            </div>

            <div class="mt-6 flex justify-end border-t border-gray-100 pt-6">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
            </div>
        </form>
    </x-admin.card>

    <div class="mt-6">
        <x-admin.card :title="__('Sitemap')">
            <p class="text-sm text-gray-600">
                {{ __('Your sitemap is generated automatically and always available at:') }}
            </p>
            <a href="{{ route('sitemap') }}" target="_blank" rel="noopener" class="mt-2 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-500">
                {{ route('sitemap') }}
            </a>
        </x-admin.card>
    </div>
</x-admin-layout>
