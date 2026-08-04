@php
    $typeConfig = $section->typeConfig();
    $itemFields = $typeConfig['item_fields'] ?? [];
    $itemLabels = $typeConfig['item_labels'] ?? [];
@endphp

<x-admin-layout :breadcrumbs="[['label' => __('Pages'), 'url' => route('admin.pages.index')], ['label' => $page->title, 'url' => route('admin.pages.sections.index', $page)], ['label' => $typeConfig['label'] ?? __('Section')]]">
    <x-slot name="title">{{ __('Edit Section') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Edit Section') }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ __('Editing a section on :title', ['title' => $page->title]) }}</p>
    </x-slot>

    <form method="POST" action="{{ route('admin.pages.sections.update', [$page, $section]) }}">
        @csrf
        @method('PUT')
        @include('admin.pages.sections._section-form', [
            'page' => $page, 'section' => $section, 'galleries' => $galleries, 'selectedGalleryIds' => $selectedGalleryIds,
            'teamMembers' => $teamMembers, 'teamCategories' => $teamCategories,
            'selectedTeamMemberIds' => $selectedTeamMemberIds, 'selectedTeamCategoryIds' => $selectedTeamCategoryIds,
        ])
    </form>

    @if ($typeConfig['has_items'] ?? false)
        <div class="mt-6">
            <x-admin.card :title="__('Items')">
                <div class="space-y-3">
                    @forelse ($section->items as $item)
                        <div class="flex items-center justify-between rounded-md border border-gray-200 p-3">
                            <div class="flex items-center gap-3">
                                @if ($item->image_url)
                                    <img src="{{ $item->image_url }}" alt="" class="h-10 w-10 shrink-0 rounded-sm object-cover">
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $item->title ?: $item->value ?: __('(untitled)') }}</p>
                                    @if ($item->subtitle)
                                        <p class="text-xs text-gray-500">{{ $item->subtitle }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'edit-section-item-{{ $item->id }}')" class="cursor-pointer text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                    {{ __('Edit') }}
                                </button>
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-section-item-{{ $item->id }}')" class="cursor-pointer text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </div>

                        <x-modal :name="'edit-section-item-'.$item->id">
                            <form method="POST" action="{{ route('admin.pages.sections.items.update', [$page, $section, $item]) }}" class="p-6">
                                @csrf
                                @method('PUT')
                                <h2 class="text-lg font-medium text-gray-900">{{ __('Edit Item') }}</h2>
                                @include('admin.pages.sections._item-form', ['item' => $item, 'itemFields' => $itemFields, 'itemLabels' => $itemLabels])
                                <div class="mt-6 flex justify-end gap-3">
                                    <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                                </div>
                            </form>
                        </x-modal>

                        <x-modal :name="'delete-section-item-'.$item->id">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900">{{ __('Delete this item?') }}</h2>
                                <p class="mt-1 text-sm text-gray-600">{{ __('This action cannot be undone.') }}</p>

                                <form method="POST" action="{{ route('admin.pages.sections.items.destroy', [$page, $section, $item]) }}" class="mt-6 flex justify-end gap-3">
                                    @csrf
                                    @method('DELETE')
                                    <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                    <x-danger-button>{{ __('Delete') }}</x-danger-button>
                                </form>
                            </div>
                        </x-modal>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('No items yet. Add one below.') }}</p>
                    @endforelse
                </div>

                <div class="mt-6 border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Add Item') }}</h3>
                    <form method="POST" action="{{ route('admin.pages.sections.items.store', [$page, $section]) }}" class="mt-4">
                        @csrf
                        @include('admin.pages.sections._item-form', ['item' => null, 'itemFields' => $itemFields, 'itemLabels' => $itemLabels])
                        <div class="mt-4 flex justify-end">
                            <x-primary-button>{{ __('Add Item') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </x-admin.card>
        </div>
    @endif
</x-admin-layout>
