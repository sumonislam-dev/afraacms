@php
    $flashes = [
        'success' => ['message' => session('success') ?? session('status'), 'class' => 'bg-green-50 text-green-800 ring-green-600/20', 'icon' => 'check-circle'],
        'error' => ['message' => session('error'), 'class' => 'bg-red-50 text-red-800 ring-red-600/20', 'icon' => 'x-circle'],
        'warning' => ['message' => session('warning'), 'class' => 'bg-amber-50 text-amber-800 ring-amber-600/20', 'icon' => 'exclamation-triangle'],
        'info' => ['message' => session('info'), 'class' => 'bg-blue-50 text-blue-800 ring-blue-600/20', 'icon' => 'information-circle'],
    ];
@endphp

@foreach ($flashes as $flash)
    @if ($flash['message'])
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 flex items-start gap-3 rounded-md p-4 ring-1 ring-inset {{ $flash['class'] }}">
            <x-admin.icon :name="$flash['icon']" class="h-5 w-5 flex-shrink-0" />
            <div class="flex-1 text-sm font-medium">{{ $flash['message'] }}</div>
            <button type="button" class="flex-shrink-0" @click="show = false">
                <span class="sr-only">{{ __('Dismiss') }}</span>
                <x-admin.icon name="x-mark" class="h-4 w-4" />
            </button>
        </div>
    @endif
@endforeach

@if ($errors->any())
    <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 flex items-start gap-3 rounded-md p-4 ring-1 ring-inset bg-red-50 text-red-800 ring-red-600/20">
        <x-admin.icon name="x-circle" class="h-5 w-5 flex-shrink-0" />
        <div class="flex-1">
            <p class="text-sm font-medium">{{ __('Whoops! Something went wrong.') }}</p>
            <ul class="mt-1 list-inside list-disc text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="flex-shrink-0" @click="show = false">
            <span class="sr-only">{{ __('Dismiss') }}</span>
            <x-admin.icon name="x-mark" class="h-4 w-4" />
        </button>
    </div>
@endif
