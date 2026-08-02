@php
    $menu = config('admin-menu', []);
@endphp

<aside
    class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-gray-900 text-gray-100 transition-transform duration-200 ease-in-out lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <div class="flex h-16 flex-shrink-0 items-center justify-between border-b border-gray-800 px-4">
        <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="text-lg font-semibold tracking-tight text-white">
            {{ config('app.name', 'AfraaCMS') }}
        </a>

        <button type="button" class="text-gray-400 hover:text-white lg:hidden" @click="sidebarOpen = false">
            <span class="sr-only">Close sidebar</span>
            <x-admin.icon name="x-mark" class="h-6 w-6" />
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-2 py-4">
        @foreach ($menu as $item)
            <x-admin.sidebar-menu-item :item="$item" />
        @endforeach
    </nav>
</aside>
