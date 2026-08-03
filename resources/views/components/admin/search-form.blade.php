@props(['placeholder' => null])

<form method="GET" class="mb-4">
    <div class="relative max-w-sm">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <x-admin.icon name="magnifying-glass" class="h-4 w-4 text-gray-400" />
        </div>
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="{{ $placeholder ?? __('Search...') }}"
            class="block w-full rounded-md border-gray-300 pl-9 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        >
    </div>
</form>
