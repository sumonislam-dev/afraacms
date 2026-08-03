<x-frontend-layout
    :title="$page['seo']['title'] ?: $page['title']"
    :description="$page['seo']['description'] ?? null"
    :image="$page['seo']['image_url'] ?? null"
    :canonical="$page['seo']['canonical_url'] ?? null"
    :robots="$page['seo']['robots'] ?? null"
>
    <x-banner :type="($isHome ?? false) ? 'homepage' : 'page'" />

    @if (! empty($page['sections']))
        <x-sections :sections="$page['sections']" />
    @else
        <div class="mx-auto max-w-3xl px-4 py-16 sm:px-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $page['title'] }}</h1>

            @if ($page['content'])
                <div class="mt-6 space-y-4 text-gray-700">
                    {!! nl2br(e($page['content'])) !!}
                </div>
            @endif
        </div>
    @endif
</x-frontend-layout>
