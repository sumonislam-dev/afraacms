@php
    $isEdit = isset($page);
    $currentStatus = old('status', $page->status ?? 'draft');
    $currentTemplate = old('template', $page->template ?? 'default');
    $currentPublishedAt = old('published_at', $isEdit ? $page->published_at?->format('Y-m-d\TH:i') : '');
@endphp

<x-admin.card>
    <div
        x-data="{
            slugTouched: {{ $isEdit || old('slug') ? 'true' : 'false' }},
            slugify(value) {
                return value.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            },
        }"
        class="space-y-4"
    >
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="title" :value="__('Title')" />
                <x-text-input
                    id="title"
                    name="title"
                    type="text"
                    class="mt-1 block w-full"
                    :value="old('title', $page->title ?? '')"
                    required
                    autofocus
                    x-on:input="if (! slugTouched) $refs.slug.value = slugify($event.target.value)"
                />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="slug" :value="__('Slug')" />
                <x-text-input
                    id="slug"
                    name="slug"
                    type="text"
                    class="mt-1 block w-full"
                    :value="old('slug', $page->slug ?? '')"
                    required
                    x-ref="slug"
                    x-on:input="slugTouched = true"
                />
                <p class="mt-1 text-xs text-gray-500">{{ __('Public URL, e.g. /about') }}</p>
                <x-input-error class="mt-2" :messages="$errors->get('slug')" />
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <x-input-label for="status" :value="__('Status')" />
                <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="draft" @selected($currentStatus === 'draft')>{{ __('Draft') }}</option>
                    <option value="published" @selected($currentStatus === 'published')>{{ __('Published') }}</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('status')" />
            </div>

            <div>
                <x-input-label for="template" :value="__('Template')" />
                <select id="template" name="template" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach (config('pages.templates', []) as $templateKey => $templateLabel)
                        <option value="{{ $templateKey }}" @selected($currentTemplate === $templateKey)>{{ $templateLabel }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('template')" />
            </div>

            <div>
                <x-input-label for="published_at" :value="__('Publish Date')" />
                <x-text-input id="published_at" name="published_at" type="datetime-local" class="mt-1 block w-full" :value="$currentPublishedAt" />
                <p class="mt-1 text-xs text-gray-500">{{ __('Blank = publish immediately once Published.') }}</p>
                <x-input-error class="mt-2" :messages="$errors->get('published_at')" />
            </div>
        </div>

        @include('admin._seo-fields', ['seoable' => $page ?? null])
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-3">
            <x-secondary-button type="button" onclick="window.location='{{ route('admin.pages.index') }}'">{{ __('Cancel') }}</x-secondary-button>
            <x-primary-button>{{ $isEdit ? __('Update Page') : __('Create Page') }}</x-primary-button>
        </div>
    </x-slot>
</x-admin.card>
