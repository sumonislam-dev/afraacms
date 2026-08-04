<x-admin-layout :breadcrumbs="[['label' => __('Pages'), 'url' => route('admin.pages.index')], ['label' => $page->title]]">
    <x-slot name="title">{{ __('Sections') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ __('Sections') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Build :title from ordered content blocks. Drag to reorder.', ['title' => $page->title]) }}</p>
            </div>

            @can('create', \App\Models\Section::class)
                <x-primary-button type="button" onclick="window.location='{{ route('admin.pages.sections.create', $page) }}'">
                    {{ __('Add Section') }}
                </x-primary-button>
            @endcan
        </div>
    </x-slot>

    @if ($sections->isEmpty())
        <x-admin.card>
            <p class="text-center text-sm text-gray-500">{{ __('No sections yet. Add one to start building this page.') }}</p>
        </x-admin.card>
    @else
        <div id="section-list-root" data-reorder-url="{{ route('admin.pages.sections.reorder', $page) }}">
            <ul class="space-y-2" data-section-list>
                @foreach ($sections as $section)
                    <li data-id="{{ $section->id }}" class="flex items-center gap-3 rounded-md border border-gray-200 bg-white p-3">
                        <span class="cursor-move text-gray-300 hover:text-gray-500" data-drag-handle>
                            <x-admin.icon name="bars-4" class="h-5 w-5" />
                        </span>

                        <span class="inline-flex shrink-0 items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">
                            {{ config("sections.types.{$section->type}.label", $section->type) }}
                        </span>

                        <span class="flex-1 truncate text-sm font-medium text-gray-900">{{ $section->heading ?: __('(no heading)') }}</span>

                        @unless ($section->is_active)
                            <span class="shrink-0 rounded-sm bg-gray-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-gray-500">{{ __('Hidden') }}</span>
                        @endunless

                        <div class="flex shrink-0 items-center gap-3">
                            @can('update', $section)
                                <a href="{{ route('admin.pages.sections.edit', [$page, $section]) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                            @endcan

                            @can('delete', $section)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-section-{{ $section->id }}')" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-section-'.$section->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">{{ __('Delete this section?') }}</h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('This will permanently remove this section and any items inside it. This action cannot be undone.') }}
                                        </p>

                                        <form method="POST" action="{{ route('admin.pages.sections.destroy', [$page, $section]) }}" class="mt-6 flex justify-end gap-3">
                                            @csrf
                                            @method('DELETE')
                                            <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                            <x-danger-button>{{ __('Delete') }}</x-danger-button>
                                        </form>
                                    </div>
                                </x-modal>
                            @endcan
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</x-admin-layout>
