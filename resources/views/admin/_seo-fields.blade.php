@php
    $seo = ($seoable ?? null)?->seo;
@endphp

<div class="border-t border-gray-100 pt-6">
    <h3 class="text-sm font-semibold text-gray-900">{{ __('SEO') }}</h3>
    <p class="mt-1 text-sm text-gray-500">{{ __('Optional overrides. Anything left blank falls back to the site-wide defaults in Settings.') }}</p>
</div>

<div>
    <x-input-label for="seo_title" :value="__('Meta Title')" />
    <x-text-input id="seo_title" name="seo_title" type="text" class="mt-1 block w-full" :value="old('seo_title', $seo->meta_title ?? '')" />
    <x-input-error class="mt-2" :messages="$errors->get('seo_title')" />
</div>

<div>
    <x-input-label for="seo_description" :value="__('Meta Description')" />
    <x-textarea id="seo_description" name="seo_description" class="mt-1 block w-full" rows="2">{{ old('seo_description', $seo->meta_description ?? '') }}</x-textarea>
    <x-input-error class="mt-2" :messages="$errors->get('seo_description')" />
</div>

<div>
    <x-input-label :value="__('Social Share Image')" />
    <x-admin.media-picker name="seo_image" :current="old('seo_image', $seo->meta_image ?? null)" />
    <p class="mt-1 text-sm text-gray-500">{{ __('Used for Open Graph / Twitter previews. Falls back to the cover image, then the site default.') }}</p>
    <x-input-error class="mt-2" :messages="$errors->get('seo_image')" />
</div>

<div>
    <x-input-label for="seo_canonical" :value="__('Canonical URL')" />
    <x-text-input id="seo_canonical" name="seo_canonical" type="text" class="mt-1 block w-full" :value="old('seo_canonical', $seo->canonical_url ?? '')" />
    <p class="mt-1 text-sm text-gray-500">{{ __('Leave blank unless this content is a duplicate of another URL.') }}</p>
    <x-input-error class="mt-2" :messages="$errors->get('seo_canonical')" />
</div>

<div>
    <x-input-label for="seo_robots" :value="__('Robots')" />
    @php
        $currentRobots = old('seo_robots', $seo->robots ?? '');
    @endphp
    <select id="seo_robots" name="seo_robots" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="" @selected($currentRobots === '')>{{ __('— Site Default —') }}</option>
        <option value="index, follow" @selected($currentRobots === 'index, follow')>{{ __('Index, Follow') }}</option>
        <option value="noindex, follow" @selected($currentRobots === 'noindex, follow')>{{ __('No Index, Follow') }}</option>
        <option value="index, nofollow" @selected($currentRobots === 'index, nofollow')>{{ __('Index, No Follow') }}</option>
        <option value="noindex, nofollow" @selected($currentRobots === 'noindex, nofollow')>{{ __('No Index, No Follow') }}</option>
    </select>
    <x-input-error class="mt-2" :messages="$errors->get('seo_robots')" />
</div>
