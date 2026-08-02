<x-admin-layout :breadcrumbs="[['label' => __('Pages'), 'url' => route('admin.pages.index')], ['label' => $page->title]]">
    <x-slot name="title">{{ __('Edit Page') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit Page') }}</h2>

            <div class="flex items-center gap-4">
                @can('viewAny', \App\Models\Section::class)
                    <a href="{{ route('admin.pages.sections.index', $page) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                        {{ __('Manage Sections') }}
                    </a>
                @endcan

                @if ($page->isPublished())
                    <a href="{{ url($page->slug) }}" target="_blank" rel="noopener" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                        {{ __('View Page') }} &rarr;
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.pages.update', $page) }}">
        @csrf
        @method('PUT')
        @include('admin.pages._form', ['page' => $page])
    </form>
</x-admin-layout>
