@php
    $isEdit = isset($project);
    $currentStatus = old('status', $project->status ?? 'draft');
    $currentCategoryId = old('category_id', $project->category_id ?? '');
    $currentGalleryId = old('gallery_id', $project->gallery_id ?? '');
@endphp

<x-admin.form-section
    :title="$isEdit ? __('Project Details') : __('New Project')"
    :description="__('The slug determines the project\'s public URL, e.g. /projects/new-office-tower.')"
>
    <div>
        <x-input-label for="title" :value="__('Title')" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $project->title ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('title')" />
    </div>

    <div>
        <x-input-label for="slug" :value="__('Slug')" />
        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $project->slug ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('slug')" />
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <x-input-label for="category_id" :value="__('Category')" />
            <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">{{ __('— None —') }}</option>
                @foreach (\App\Models\ProjectCategory::orderBy('name')->get() as $category)
                    <option value="{{ $category->id }}" @selected((string) $currentCategoryId === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
        </div>

        <div>
            <x-input-label for="status" :value="__('Status')" />
            <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="draft" @selected($currentStatus === 'draft')>{{ __('Draft') }}</option>
                <option value="published" @selected($currentStatus === 'published')>{{ __('Published') }}</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('status')" />
        </div>
    </div>

    <div>
        <x-input-label for="gallery_id" :value="__('Photo/Video Gallery')" />
        <select id="gallery_id" name="gallery_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">{{ __('— None —') }}</option>
            @foreach (\App\Models\Gallery::orderBy('title')->get() as $gallery)
                <option value="{{ $gallery->id }}" @selected((string) $currentGalleryId === (string) $gallery->id)>{{ $gallery->title }}</option>
            @endforeach
        </select>
        <p class="mt-1 text-sm text-gray-500">{{ __('Optional. Attaches an existing album from the Galleries module to this project\'s page.') }}</p>
        <x-input-error class="mt-2" :messages="$errors->get('gallery_id')" />
    </div>

    <div>
        <x-input-label for="excerpt" :value="__('Excerpt')" />
        <x-text-input id="excerpt" name="excerpt" type="text" class="mt-1 block w-full" :value="old('excerpt', $project->excerpt ?? '')" />
        <p class="mt-1 text-sm text-gray-500">{{ __('Short summary shown on the projects listing.') }}</p>
        <x-input-error class="mt-2" :messages="$errors->get('excerpt')" />
    </div>

    <div>
        <x-input-label for="content" :value="__('Content')" />
        <x-textarea id="content" name="content" class="mt-1 block w-full" rows="8">{{ old('content', $project->content ?? '') }}</x-textarea>
        <x-input-error class="mt-2" :messages="$errors->get('content')" />
    </div>

    <div>
        <x-input-label :value="__('Cover Image')" />
        <x-admin.media-picker name="cover_image" :current="old('cover_image', $project->cover_image ?? null)" />
        <x-input-error class="mt-2" :messages="$errors->get('cover_image')" />
    </div>

    <div>
        <x-input-label :value="__('Featured')" />
        <div class="mt-2">
            <x-admin.toggle name="is_featured" :checked="old('is_featured', $project->is_featured ?? false)" />
        </div>
        <p class="mt-1 text-sm text-gray-500">{{ __('Featured projects are listed first on the public site.') }}</p>
        <x-input-error class="mt-2" :messages="$errors->get('is_featured')" />
    </div>

    <x-slot name="actions">
        <x-secondary-button type="button" onclick="window.location='{{ route('admin.projects.index') }}'">{{ __('Cancel') }}</x-secondary-button>
        <x-primary-button>{{ $isEdit ? __('Update Project') : __('Create Project') }}</x-primary-button>
    </x-slot>
</x-admin.form-section>
