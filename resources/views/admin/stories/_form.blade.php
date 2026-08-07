@php
    $isEdit = isset($story);
    $currentStatus = old('status', $story->status ?? 'draft');
    $currentProjectId = old('project_id', $story->project_id ?? '');
    $currentPublishedAt = old('published_at', optional($story->published_at ?? null)->format('Y-m-d'));
@endphp

<x-admin.edit-layout>
    <x-slot name="main">
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
                            :value="old('title', $story->title ?? '')"
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
                            :value="old('slug', $story->slug ?? '')"
                            required
                            x-ref="slug"
                            x-on:input="slugTouched = true"
                        />
                        <p class="mt-1 text-xs text-gray-500">{{ __('Public URL, e.g. /stories/from-laborer-to-technician') }}</p>
                        <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="excerpt" :value="__('Excerpt')" />
                    <x-text-input id="excerpt" name="excerpt" type="text" class="mt-1 block w-full" :value="old('excerpt', $story->excerpt ?? '')" />
                    <p class="mt-1 text-xs text-gray-500">{{ __('Short summary shown on the stories listing.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('excerpt')" />
                </div>

                <div>
                    <x-input-label for="content" :value="__('Story')" />
                    <x-admin.rich-text-editor name="content" class="mt-1" :value="old('content', $story->content ?? '')" />
                    <x-input-error class="mt-2" :messages="$errors->get('content')" />
                </div>

                @include('admin._seo-fields', ['seoable' => $story ?? null])
            </div>
        </x-admin.card>
    </x-slot>

    <x-slot name="sidebar">
        <x-admin.card :title="__('Publishing')">
            <div class="space-y-4">
                <div>
                    <x-input-label for="project_id" :value="__('Related Project')" />
                    <select id="project_id" name="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('— None —') }}</option>
                        @foreach (\App\Models\Project::orderBy('title')->get() as $project)
                            <option value="{{ $project->id }}" @selected((string) $currentProjectId === (string) $project->id)>{{ $project->title }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Optional. Lets visitors filter stories by which project they came from.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('project_id')" />
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
                    <x-admin.media-picker name="cover_image" :current="old('cover_image', $story->cover_image ?? null)" />
                    <x-input-error class="mt-2" :messages="$errors->get('cover_image')" />
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <x-input-label :value="__('Featured')" />
                    <div class="mt-1 flex items-center rounded-md border border-gray-200 px-3 py-2">
                        <x-admin.toggle name="is_featured" :checked="old('is_featured', $story->is_featured ?? false)" />
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Featured stories are listed first on the public site.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('is_featured')" />
                </div>
            </div>
        </x-admin.card>
    </x-slot>
</x-admin.edit-layout>

<div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-6">
    <x-secondary-button type="button" onclick="window.location='{{ route('admin.stories.index') }}'">{{ __('Cancel') }}</x-secondary-button>
    <x-primary-button>{{ $isEdit ? __('Update Story') : __('Create Story') }}</x-primary-button>
</div>
