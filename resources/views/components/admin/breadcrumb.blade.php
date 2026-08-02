@props(['items' => []])

<nav class="mb-4" aria-label="Breadcrumb">
    <ol class="flex flex-wrap items-center gap-1 text-sm text-gray-500">
        <li>
            <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="inline-flex items-center hover:text-gray-700">
                <x-admin.icon name="home" class="h-4 w-4" />
                <span class="sr-only">{{ __('Dashboard') }}</span>
            </a>
        </li>

        @foreach ($items as $item)
            <li class="flex items-center gap-1">
                <x-admin.icon name="chevron-right" class="h-4 w-4 text-gray-300" />

                @if (! empty($item['url']) && ! $loop->last)
                    <a href="{{ $item['url'] }}" class="hover:text-gray-700">{{ $item['label'] }}</a>
                @else
                    <span class="font-medium text-gray-700">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
