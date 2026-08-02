@props(['name', 'current' => null, 'disabled' => false])

<div class="mt-1 flex items-center gap-4">
    @if ($current)
        <img
            src="{{ media_url($current) }}"
            alt=""
            class="h-16 w-16 flex-shrink-0 rounded-md border border-gray-200 bg-white object-contain"
        >
    @else
        <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-md border border-dashed border-gray-300 text-gray-300">
            <x-admin.icon name="photo" class="h-6 w-6" />
        </div>
    @endif

    <div class="flex-1">
        <input
            type="file"
            name="{{ $name }}"
            accept="image/*"
            @disabled($disabled)
            class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200"
        >

        @if ($current)
            <p class="mt-1 text-xs text-gray-500">{{ __('Uploading a new file will replace the current image.') }}</p>
        @endif
    </div>
</div>
