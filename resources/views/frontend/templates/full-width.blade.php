<x-frontend-layout :title="$page['title']">
    @if (! empty($page['sections']))
        <x-sections :sections="$page['sections']" />
    @else
        <div class="px-4 py-16 sm:px-6">
            <h1 class="text-center text-3xl font-bold text-gray-900">{{ $page['title'] }}</h1>

            @if ($page['content'])
                <div class="mx-auto mt-6 max-w-5xl space-y-4 text-gray-700">
                    {!! nl2br(e($page['content'])) !!}
                </div>
            @endif
        </div>
    @endif
</x-frontend-layout>
