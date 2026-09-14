@php
    $isEdit = isset($post);
    $currentStatus = old('status', $post->status ?? 'draft');
    $currentCategoryId = old('category_id', $post->category_id ?? '');
    $currentPublishedAt = old('published_at', optional($post->published_at ?? null)->format('Y-m-d'));
@endphp

<x-admin.edit-layout>
    <x-slot name="main">
        <x-admin.card>
            <div
                x-data="{
                    slugTouched: {{ $isEdit || old('slug') ? 'true' : 'false' }},
                    attachmentFileName: null,
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
                            :value="old('title', $post->title ?? '')"
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
                            :value="old('slug', $post->slug ?? '')"
                            required
                            x-ref="slug"
                            x-on:input="slugTouched = true"
                        />
                        <p class="mt-1 text-xs text-gray-500">{{ __('Public URL, e.g. /news/new-scholarship-round') }}</p>
                        <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="excerpt" :value="__('Excerpt')" />
                    <x-text-input id="excerpt" name="excerpt" type="text" class="mt-1 block w-full" :value="old('excerpt', $post->excerpt ?? '')" />
                    <p class="mt-1 text-xs text-gray-500">{{ __('Short summary shown on the news listing.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('excerpt')" />
                </div>

                <div>
                    <x-input-label for="content" :value="__('Content')" />
                    <x-admin.rich-text-editor name="content" class="mt-1" :value="old('content', $post->content ?? '')" />
                    <x-input-error class="mt-2" :messages="$errors->get('content')" />
                </div>

                @include('admin._seo-fields', ['seoable' => $post ?? null])
            </div>
        </x-admin.card>
    </x-slot>

    <x-slot name="sidebar">
        <x-admin.card :title="__('Publishing')">
            <div class="space-y-4">
                <div>
                    <x-input-label for="category_id" :value="__('Category')" />
                    <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('— None —') }}</option>
                        @foreach (\App\Models\NewsCategory::orderBy('name')->get() as $category)
                            <option value="{{ $category->id }}" @selected((string) $currentCategoryId === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                </div>

                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="draft" @selected($currentStatus === 'draft')>{{ __('Draft') }}</option>
                        <option value="published" @selected($currentStatus === 'published')>{{ __('Published') }}</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                </div>

                <div>
                    <x-input-label for="published_at" :value="__('Published Date')" />
                    <x-text-input id="published_at" name="published_at" type="date" class="mt-1 block w-full" :value="$currentPublishedAt" />
                    <p class="mt-1 text-xs text-gray-500">{{ __('Controls display order and the date shown to visitors.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('published_at')" />
                </div>
            </div>
        </x-admin.card>

        <x-admin.card :title="__('Display')">
            <div class="space-y-4">
                <div>
                    <x-input-label :value="__('Cover Image')" />
                    <x-admin.media-picker name="cover_image" :current="old('cover_image', $post->cover_image ?? null)" />
                    <x-input-error class="mt-2" :messages="$errors->get('cover_image')" />
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <x-input-label for="attachment" :value="__('Attachment (optional)')" />
                    <p class="mt-1 text-xs text-gray-500">{{ __('Attach a PDF or image (e.g. a notice, circular). Pick this post into a Notices section to feature it.') }}</p>

                    @if ($isEdit && $post->attachment_url)
                        <p class="mt-2 text-sm text-gray-700">
                            <a href="{{ $post->attachment_url }}" target="_blank" rel="noopener" class="font-medium text-indigo-600 hover:text-indigo-900">{{ $post->attachment_file_name }}</a>
                        </p>
                    @endif

                    <input
                        id="attachment"
                        name="attachment"
                        type="file"
                        accept="application/pdf,image/*"
                        class="hidden"
                        x-ref="attachmentInput"
                        @change="attachmentFileName = $event.target.files[0]?.name ?? null"
                    >

                    <div class="mt-2 flex items-center gap-3">
                        <x-secondary-button type="button" @click="$refs.attachmentInput.click()">
                            {{ __('Choose File') }}
                        </x-secondary-button>
                        <span class="truncate text-sm text-gray-600" x-text="attachmentFileName ?? '{{ __('No file chosen') }}'"></span>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('attachment')" />

                    @if ($isEdit && $post->attachment_url)
                        <label class="mt-3 flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="remove_attachment" value="1" class="rounded-sm border-gray-300 text-red-600 focus:ring-red-500">
                            {{ __('Remove the current attachment') }}
                        </label>
                    @endif
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <x-input-label :value="__('Featured')" />
                    <div class="mt-1 flex items-center rounded-md border border-gray-200 px-3 py-2">
                        <x-admin.toggle name="is_featured" :checked="old('is_featured', $post->is_featured ?? false)" />
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Featured posts are listed first on the public site.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('is_featured')" />
                </div>
            </div>
        </x-admin.card>
    </x-slot>
</x-admin.edit-layout>

<div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-6">
    <x-secondary-button type="button" onclick="window.location='{{ route('admin.news.index') }}'">{{ __('Cancel') }}</x-secondary-button>
    <x-primary-button>{{ $isEdit ? __('Update Post') : __('Create Post') }}</x-primary-button>
</div>
