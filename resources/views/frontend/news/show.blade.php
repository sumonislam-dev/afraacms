<x-frontend-layout
    :title="$post['seo']['title'] ?: $post['title']"
    :description="$post['seo']['description'] ?: $post['excerpt']"
    :image="$post['seo']['image_url'] ?: $post['cover_image_url']"
    :canonical="$post['seo']['canonical_url'] ?? null"
    :robots="$post['seo']['robots'] ?? null"
>
    <x-banner type="page" :override="['image_url' => $post['cover_image_url'] ?? null]" :page-title="$post['title']" />

    <div class="mx-auto max-w-4xl px-4 py-16 sm:px-6">
        @if ($post['cover_image_url'])
            <img src="{{ $post['cover_image_url'] }}" alt="{{ $post['title'] }}" class="aspect-video w-full rounded-lg object-cover">
        @endif

        <div class="mt-8 text-center">
            <div class="flex items-center justify-center gap-2 text-xs font-medium uppercase tracking-wide text-brand-600">
                @if ($post['category'])
                    <a href="{{ route('news.index', ['category' => $post['category']['slug']]) }}" class="hover:text-brand-500">
                        {{ $post['category']['name'] }}
                    </a>
                    <span>&middot;</span>
                @endif

                @if ($post['published_at'])
                    <span class="text-ink-900/50">{{ \Illuminate\Support\Carbon::parse($post['published_at'])->format('F j, Y') }}</span>
                @endif
            </div>

            @if ($post['excerpt'])
                <p class="mt-4 text-lg text-gray-600">{{ $post['excerpt'] }}</p>
            @endif
        </div>

        @if ($post['content'])
            <div class="prose prose-neutral mt-8 max-w-none text-gray-700">
                {!! str_starts_with(ltrim($post['content']), '<') ? $post['content'] : nl2br(e($post['content'])) !!}
            </div>
        @endif

        <div class="mt-10 text-center">
            <a href="{{ route('news.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-500">&larr; {{ __('Back to News') }}</a>
        </div>
    </div>
</x-frontend-layout>
