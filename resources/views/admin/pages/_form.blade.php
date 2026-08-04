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
                <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="draft" @selected($currentStatus === 'draft')>{{ __('Draft') }}</option>
                    <option value="published" @selected($currentStatus === 'published')>{{ __('Published') }}</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('status')" />
            </div>

            <div>
                <x-input-label for="template" :value="__('Template')" />
                <select id="template" name="template" required class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
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

        @php
            $hasBannerErrors = $errors->hasAny(['banner_image', 'banner_eyebrow']);
            $hasBannerData = $isEdit && ($page->banner_image || $page->banner_eyebrow);
            $bannerOpen = $hasBannerErrors || $hasBannerData;
        @endphp

        <div x-data="{ open: {{ $bannerOpen ? 'true' : 'false' }} }" class="border-t border-gray-100 pt-6">
            <button type="button" x-on:click="open = ! open" class="flex w-full items-center justify-between text-left">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Page Banner') }}</h3>
                    <p class="mt-1 text-sm text-gray-500" x-show="! open" x-cloak>{{ __('Optional overrides. Left blank, this page uses the site-wide Page Banner from Banners.') }}</p>
                </div>
                <span class="shrink-0 text-sm font-medium text-indigo-600 hover:text-indigo-900">
                    <span x-show="! open" x-cloak>{{ __('Show Banner Fields') }}</span>
                    <span x-show="open" x-cloak>{{ __('Hide Banner Fields') }}</span>
                </span>
            </button>

            <div x-show="open" x-cloak class="mt-4 space-y-4">
                <p class="text-sm text-gray-500">{{ __('Optional overrides. Left blank, this page uses the site-wide Page Banner from Banners.') }}</p>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label :value="__('Banner Image')" />
                        <x-admin.media-picker name="banner_image" :current="old('banner_image', $page->banner_image ?? null)" />
                        <x-input-error class="mt-2" :messages="$errors->get('banner_image')" />
                    </div>

                    <div>
                        <x-input-label for="banner_eyebrow" :value="__('Eyebrow Text')" />
                        <x-text-input id="banner_eyebrow" name="banner_eyebrow" type="text" class="mt-1 block w-full" :value="old('banner_eyebrow', $page->banner_eyebrow ?? '')" placeholder="{{ __('e.g. About RSUF') }}" />
                        <p class="mt-1 text-xs text-gray-500">{{ __('Small label shown above the page title in the banner.') }}</p>
                        <x-input-error class="mt-2" :messages="$errors->get('banner_eyebrow')" />
                    </div>
                </div>
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
