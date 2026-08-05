@php
    $isEdit = isset($section);
    $currentType = old('type', $section->type ?? '');
    $typesConfig = config('sections.types', []);
@endphp

<div x-data="{ type: @js($currentType), fields: @js(collect($typesConfig)->map(fn ($t) => $t['fields'])->all()) }">
    <x-admin.card>
        <div class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <x-input-label for="type" :value="__('Type')" />
                    <select id="type" name="type" x-model="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('Select a type') }}</option>
                        @foreach ($typesConfig as $typeKey => $typeMeta)
                            <option value="{{ $typeKey }}" @selected($currentType === $typeKey)>{{ $typeMeta['label'] }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('type')" />
                </div>

                <div>
                    <x-input-label for="anchor" :value="__('Anchor ID')" />
                    <x-text-input id="anchor" name="anchor" type="text" class="mt-1 block w-full" :value="old('anchor', $section->anchor ?? '')" placeholder="e.g. history" />
                    <p class="mt-1 text-xs text-gray-500">{{ __('Optional. Lets a menu link jump straight to this section, e.g. /about#history.') }}</p>
                    <x-input-error class="mt-2" :messages="$errors->get('anchor')" />
                </div>

                <div x-show="fields[type]?.includes('heading')" style="display: none;">
                    <x-input-label for="heading" :value="__('Heading')" />
                    <x-text-input id="heading" name="heading" type="text" class="mt-1 block w-full" :value="old('heading', $section->heading ?? '')" />
                    <x-input-error class="mt-2" :messages="$errors->get('heading')" />
                </div>

                <div x-show="fields[type]?.includes('subheading')" style="display: none;">
                    <x-input-label for="subheading" :value="__('Subheading')" />
                    <x-text-input id="subheading" name="subheading" type="text" class="mt-1 block w-full" :value="old('subheading', $section->subheading ?? '')" />
                    <x-input-error class="mt-2" :messages="$errors->get('subheading')" />
                </div>
            </div>

            <div x-show="['rich_text', 'image_text'].includes(type)" style="display: none;">
                <x-input-label for="body" :value="__('Content')" />
                <x-admin.rich-text-editor
                    id="body"
                    name-expression="['rich_text', 'image_text'].includes(type) ? 'body' : null"
                    class="mt-1"
                    :value="old('body', $section->body ?? '')"
                    rows="5"
                />
                <x-input-error class="mt-2" :messages="$errors->get('body')" />
            </div>

            <div x-show="type === 'hero'" style="display: none;">
                <x-input-label for="body-plain" :value="__('Content')" />
                <x-textarea id="body-plain" x-bind:name="type === 'hero' ? 'body' : null" class="mt-1 block w-full" rows="5">{{ old('body', $section->body ?? '') }}</x-textarea>
                <x-input-error class="mt-2" :messages="$errors->get('body')" />
            </div>

            <div x-show="fields[type]?.includes('image')" style="display: none;">
                <x-input-label :value="__('Image')" />
                <x-admin.media-picker name="image" :current="old('image', $section->image ?? null)" />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2" x-show="fields[type]?.includes('button_text')" style="display: none;">
                <div>
                    <x-input-label for="button_text" :value="__('Button Text')" />
                    <x-text-input id="button_text" name="button_text" type="text" class="mt-1 block w-full" :value="old('button_text', $section->button_text ?? '')" />
                    <x-input-error class="mt-2" :messages="$errors->get('button_text')" />
                </div>
                <div>
                    <x-input-label for="button_url" :value="__('Button URL')" />
                    <x-text-input id="button_url" name="button_url" type="text" class="mt-1 block w-full" :value="old('button_url', $section->button_url ?? '')" />
                    <x-input-error class="mt-2" :messages="$errors->get('button_url')" />
                </div>
            </div>

            <div
                x-show="type === 'gallery_albums'"
                style="display: none;"
                x-data="{ galleryMode: @js(! empty($selectedGalleryIds ?? []) ? 'specific' : 'all') }"
            >
                <x-input-label :value="__('Albums to Show')" />
                <div class="mt-1 inline-flex rounded-md border border-gray-300 bg-white p-0.5 text-sm">
                    <label class="cursor-pointer rounded-sm px-3 py-1 font-medium transition-colors" :class="galleryMode === 'all' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50'">
                        <input type="radio" x-model="galleryMode" value="all" class="sr-only">
                        {{ __('All Active Albums') }}
                    </label>
                    <label class="cursor-pointer rounded-sm px-3 py-1 font-medium transition-colors" :class="galleryMode === 'specific' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50'">
                        <input type="radio" x-model="galleryMode" value="specific" class="sr-only">
                        {{ __('Specific Albums') }}
                    </label>
                </div>

                <div class="mt-3 max-h-56 space-y-2 overflow-y-auto rounded-md border border-gray-200 p-3" x-show="galleryMode === 'specific'" style="display: none;">
                    @forelse ($galleries ?? [] as $gallery)
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input
                                type="checkbox"
                                name="galleries[]"
                                value="{{ $gallery->id }}"
                                @checked(in_array($gallery->id, $selectedGalleryIds ?? [], true))
                                x-bind:disabled="galleryMode !== 'specific'"
                                class="rounded-sm border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            {{ $gallery->title }}
                            @unless ($gallery->is_active)
                                <span class="text-xs text-gray-400">({{ __('inactive') }})</span>
                            @endunless
                        </label>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('No albums yet.') }}</p>
                    @endforelse
                </div>

                <p class="mt-2 text-xs text-gray-500">{{ __('"All Active Albums" always shows your latest active albums automatically. Choose "Specific Albums" to hand-pick which ones appear here.') }}</p>
            </div>

            <div
                x-show="type === 'team_members'"
                style="display: none;"
                x-data="{
                    teamMode: @js(! empty($selectedTeamCategoryIds ?? [])
                        ? 'category'
                        : (! empty($selectedTeamMemberIds ?? []) ? 'specific' : 'all')),
                }"
            >
                <x-input-label :value="__('Members to Show')" />
                <div class="mt-1 inline-flex rounded-md border border-gray-300 bg-white p-0.5 text-sm">
                    <label class="cursor-pointer rounded-sm px-3 py-1 font-medium transition-colors" :class="teamMode === 'all' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50'">
                        <input type="radio" x-model="teamMode" value="all" class="sr-only">
                        {{ __('All Active Members') }}
                    </label>
                    <label class="cursor-pointer rounded-sm px-3 py-1 font-medium transition-colors" :class="teamMode === 'category' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50'">
                        <input type="radio" x-model="teamMode" value="category" class="sr-only">
                        {{ __('By Category') }}
                    </label>
                    <label class="cursor-pointer rounded-sm px-3 py-1 font-medium transition-colors" :class="teamMode === 'specific' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50'">
                        <input type="radio" x-model="teamMode" value="specific" class="sr-only">
                        {{ __('Specific Members') }}
                    </label>
                </div>

                <div class="mt-3 max-h-56 space-y-2 overflow-y-auto rounded-md border border-gray-200 p-3" x-show="teamMode === 'category'" style="display: none;">
                    @forelse ($teamCategories ?? [] as $category)
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input
                                type="checkbox"
                                name="team_category_ids[]"
                                value="{{ $category->id }}"
                                @checked(in_array($category->id, $selectedTeamCategoryIds ?? [], true))
                                x-bind:disabled="teamMode !== 'category'"
                                class="rounded-sm border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            {{ $category->name }}
                        </label>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('No categories yet.') }}</p>
                    @endforelse
                </div>

                <div class="mt-3 max-h-56 space-y-2 overflow-y-auto rounded-md border border-gray-200 p-3" x-show="teamMode === 'specific'" style="display: none;">
                    @forelse ($teamMembers ?? [] as $member)
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input
                                type="checkbox"
                                name="team_members[]"
                                value="{{ $member->id }}"
                                @checked(in_array($member->id, $selectedTeamMemberIds ?? [], true))
                                x-bind:disabled="teamMode !== 'specific'"
                                class="rounded-sm border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            {{ $member->name }}
                            @if ($member->role)
                                <span class="text-xs text-gray-400">({{ $member->role }})</span>
                            @endif
                        </label>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('No team members yet. Add some under Team in the sidebar.') }}</p>
                    @endforelse
                </div>

                <p class="mt-2 text-xs text-gray-500">{{ __('"All Active Members" shows everyone. "By Category" shows only the categories you pick here (e.g. Volunteers on one page, Board on another). "Specific Members" lets you hand-pick exactly who appears.') }}</p>
            </div>

            <div x-show="type === 'hero'" style="display: none;">
                <x-input-label :value="__('Background Images')" />
                <p class="mt-1 text-xs text-gray-500">{{ __('Optional. Pick one or more albums to rotate their photos behind the heading. Left blank, the single Image above is used instead.') }}</p>
                <div class="mt-2 max-h-56 space-y-2 overflow-y-auto rounded-md border border-gray-200 p-3">
                    @forelse ($galleries ?? [] as $gallery)
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input
                                type="checkbox"
                                name="galleries[]"
                                value="{{ $gallery->id }}"
                                @checked(in_array($gallery->id, $selectedGalleryIds ?? [], true))
                                class="rounded-sm border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            {{ $gallery->title }}
                            @unless ($gallery->is_active)
                                <span class="text-xs text-gray-400">({{ __('inactive') }})</span>
                            @endunless
                        </label>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('No albums yet.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="sm:max-w-xs" x-show="fields[type]?.includes('layout')" style="display: none;">
                <x-input-label for="layout" :value="__('Layout')" />
                <select id="layout" name="layout" class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="image-left" @selected(old('layout', $section->layout ?? '') === 'image-left')>{{ __('Image Left') }}</option>
                    <option value="image-right" @selected(old('layout', $section->layout ?? '') === 'image-right')>{{ __('Image Right') }}</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('layout')" />
            </div>

            @if ($isEdit)
                <div class="sm:max-w-xs">
                    <x-input-label :value="__('Visible')" />
                    <div class="mt-1 flex items-center rounded-md border border-gray-200 px-3 py-2">
                        <x-admin.toggle name="is_active" :checked="(bool) old('is_active', $section->is_active ?? true)" />
                    </div>
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-3">
                <x-secondary-button type="button" onclick="window.location='{{ route('admin.pages.sections.index', $page) }}'">{{ __('Cancel') }}</x-secondary-button>
                <x-primary-button>{{ $isEdit ? __('Update Section') : __('Create Section') }}</x-primary-button>
            </div>
        </x-slot>
    </x-admin.card>
</div>
