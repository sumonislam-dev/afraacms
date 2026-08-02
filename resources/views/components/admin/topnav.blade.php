@props(['title' => null])

<header class="sticky top-0 z-30 flex h-16 flex-shrink-0 items-center gap-4 border-b border-gray-200 bg-white px-4 sm:px-6">
    <button type="button" class="text-gray-500 hover:text-gray-700 lg:hidden" @click="sidebarOpen = true">
        <span class="sr-only">Open sidebar</span>
        <x-admin.icon name="bars-3" class="h-6 w-6" />
    </button>

    <div class="min-w-0 flex-1">
        @if ($title)
            <h1 class="truncate text-lg font-semibold text-gray-900">{{ $title }}</h1>
        @endif
    </div>

    <div class="flex items-center gap-4">
        <button type="button" class="relative text-gray-400 hover:text-gray-600">
            <span class="sr-only">Notifications</span>
            <x-admin.icon name="bell" class="h-6 w-6" />
        </button>

        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button type="button" class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none">
                    <x-admin.icon name="user-circle" class="h-7 w-7 text-gray-400" />
                    <span class="hidden sm:inline">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <x-admin.icon name="chevron-down" class="h-4 w-4 text-gray-400" />
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-dropdown-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>
