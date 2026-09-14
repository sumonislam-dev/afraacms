@props(['text'])

<span class="group relative inline-flex cursor-help items-center align-middle">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 text-gray-400 hover:text-gray-600">
        <path fill-rule="evenodd" d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
    </svg>

    <span class="pointer-events-none absolute bottom-full left-1/2 z-20 mb-2 w-64 -translate-x-1/2 rounded-md bg-gray-900 px-3 py-2 text-xs leading-normal text-white opacity-0 shadow-lg transition-opacity duration-150 group-hover:opacity-100">
        {{ $text }}
    </span>
</span>
