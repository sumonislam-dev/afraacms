@props(['item', 'level' => 0])

@php
    $routeMatches = function (?string $routeName) {
        return $routeName
            && \Illuminate\Support\Facades\Route::has($routeName)
            && (request()->routeIs($routeName) || request()->routeIs($routeName.'.*'));
    };

    $hasChildren = ! empty($item['children']);
    $routeName = $item['route'] ?? null;
    $routeExists = $routeName && \Illuminate\Support\Facades\Route::has($routeName);
    $href = $routeExists ? route($routeName) : '#';
    $disabled = ! $hasChildren && (! $routeExists || ! empty($item['disabled']));

    $isActive = $routeMatches($routeName);

    if ($hasChildren) {
        foreach ($item['children'] as $child) {
            if ($routeMatches($child['route'] ?? null)) {
                $isActive = true;
                break;
            }
        }
    }
@endphp

@if ($hasChildren)
    <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">
        <button
            type="button"
            @click="open = ! open"
            class="flex w-full items-center justify-between rounded-md px-3 py-2 text-sm font-medium transition-colors {{ $isActive ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
        >
            <span class="flex items-center gap-3">
                <x-admin.icon :name="$item['icon'] ?? 'squares-2x2'" class="h-5 w-5 flex-shrink-0" />
                {{ $item['label'] }}
            </span>

            <x-admin.icon name="chevron-down" class="h-4 w-4 flex-shrink-0 transition-transform duration-150" x-bind:class="{ 'rotate-180': open }" />
        </button>

        <div x-show="open" x-transition class="mt-1 space-y-1 pl-8" @if (! $isActive) style="display: none;" @endif>
            @foreach ($item['children'] as $child)
                <x-admin.sidebar-menu-item :item="$child" :level="$level + 1" />
            @endforeach
        </div>
    </div>
@else
    <a
        href="{{ $href }}"
        @if ($disabled) aria-disabled="true" onclick="event.preventDefault();" @endif
        class="flex items-center justify-between gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ $isActive ? 'bg-indigo-600 text-white' : ($disabled ? 'cursor-not-allowed text-gray-500' : 'text-gray-300 hover:bg-gray-800 hover:text-white') }}"
    >
        <span class="flex items-center gap-3">
            @if ($level === 0)
                <x-admin.icon :name="$item['icon'] ?? 'squares-2x2'" class="h-5 w-5 flex-shrink-0" />
            @endif
            {{ $item['label'] }}
        </span>

        @if ($disabled)
            <span class="rounded bg-gray-700 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-300">Soon</span>
        @endif
    </a>
@endif
