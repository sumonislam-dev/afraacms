@props(['paginator'])

@if ($paginator->hasPages())
    <nav class="mt-10 flex items-center justify-center gap-4 text-sm">
        @if ($paginator->onFirstPage())
            <span class="cursor-not-allowed text-gray-300">&larr; {{ __('Previous') }}</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="font-medium text-brand-600 hover:text-brand-500">&larr; {{ __('Previous') }}</a>
        @endif

        <span class="text-gray-500">{{ __(':current of :last', ['current' => $paginator->currentPage(), 'last' => $paginator->lastPage()]) }}</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="font-medium text-brand-600 hover:text-brand-500">{{ __('Next') }} &rarr;</a>
        @else
            <span class="cursor-not-allowed text-gray-300">{{ __('Next') }} &rarr;</span>
        @endif
    </nav>
@endif
