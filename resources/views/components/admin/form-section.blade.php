@props(['title' => null, 'description' => null])

<div class="md:grid md:grid-cols-3 md:gap-6">
    <div class="px-4 sm:px-0 md:col-span-1">
        @if ($title)
            <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
        @endif

        @if ($description)
            <p class="mt-1 text-sm text-gray-600">{{ $description }}</p>
        @endif
    </div>

    <div class="mt-5 md:col-span-2 md:mt-0">
        <div class="overflow-hidden shadow sm:rounded-md">
            <div class="space-y-6 bg-white px-4 py-5 sm:p-6">
                {{ $slot }}
            </div>

            @isset($actions)
                <div class="flex items-center justify-end gap-3 bg-gray-50 px-4 py-3 sm:px-6">
                    {{ $actions }}
                </div>
            @endisset
        </div>
    </div>
</div>
