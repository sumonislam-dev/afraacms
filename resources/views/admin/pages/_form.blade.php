@php
    $isEdit = isset($page);
    $currentStatus = old('status', $page->status ?? 'draft');
    $currentTemplate = old('template', $page->template ?? 'default');
    $currentPublishedAt = old('published_at', $isEdit ? $page->published_at?->format('Y-m-d\TH:i') : '');
@endphp

<x-admin.form-section
    :title="$isEdit ? __('Page Details') : __('New Page')"
    :description="__('The slug determines the page\'s public URL, e.g. /about.')"
>
    <div>
        <x-input-label for="title" :value="__('Title')" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $page->title ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('title')" />
    </div>

    <div>
        <x-input-label for="slug" :value="__('Slug')" />
        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $page->slug ?? '')" required />
        <p class="mt-1 text-sm text-gray-500">{{ __('Lowercase letters, numbers, dashes and underscores only.') }}</p>
        <x-input-error class="mt-2" :messages="$errors->get('slug')" />
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
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
    </div>

    <div>
        <x-input-label for="published_at" :value="__('Publish Date')" />
        <x-text-input id="published_at" name="published_at" type="datetime-local" class="mt-1 block w-full" :value="$currentPublishedAt" />
        <p class="mt-1 text-sm text-gray-500">{{ __('Leave blank to publish immediately once the status is set to Published.') }}</p>
        <x-input-error class="mt-2" :messages="$errors->get('published_at')" />
    </div>

    <div>
        <x-input-label for="content" :value="__('Content')" />
        <x-textarea id="content" name="content" class="mt-1 block w-full" rows="8">{{ old('content', $page->content ?? '') }}</x-textarea>
        <p class="mt-1 text-sm text-gray-500">{{ __('Plain text for now - the Section Engine will replace this with structured content blocks.') }}</p>
        <x-input-error class="mt-2" :messages="$errors->get('content')" />
    </div>

    @include('admin._seo-fields', ['seoable' => $page ?? null])

    <x-slot name="actions">
        <x-secondary-button type="button" onclick="window.location='{{ route('admin.pages.index') }}'">{{ __('Cancel') }}</x-secondary-button>
        <x-primary-button>{{ $isEdit ? __('Update Page') : __('Create Page') }}</x-primary-button>
    </x-slot>
</x-admin.form-section>
