@props(['post'])

@php
    $isNotice = (bool) ($post['attachment_url'] ?? null);
@endphp

<a
    href="{{ $isNotice ? $post['attachment_url'] : route('news.show', $post['slug']) }}"
    @if ($isNotice) target="_blank" rel="noopener" @endif
    class="group block overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-black/5 transition hover:shadow-xl"
>
    <div class="relative h-52 overflow-hidden">
        @if ($post['cover_image_url'] ?? null)
            <img src="{{ $post['cover_image_url'] }}" alt="" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gray-100 text-gray-300">
                <x-icon name="photo" class="h-10 w-10" />
            </div>
        @endif

        @if ($post['is_featured'] ?? false)
            <span class="absolute left-2 top-2 rounded-full bg-brand-500 px-2 py-0.5 text-xs font-semibold text-white">{{ __('Featured') }}</span>
        @endif

        @if ($isNotice)
            <span class="absolute right-2 top-2 flex items-center gap-1 rounded-full bg-ink-900/80 px-2 py-0.5 text-xs font-semibold text-white">
                <x-icon name="document-text" class="h-3.5 w-3.5" />
                {{ __('Notice') }}
            </span>
        @endif
    </div>

    <div class="p-6">
        <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-brand-600">
            @if ($post['category'] ?? null)
                <span>{{ $post['category']['name'] }}</span>
                <span>&middot;</span>
            @endif

            @if ($post['published_at'] ?? null)
                <span class="text-ink-900/50">{{ \Illuminate\Support\Carbon::parse($post['published_at'])->format('M j, Y') }}</span>
            @endif
        </div>

        <h3 class="mt-2 flex items-center gap-1.5 text-lg font-semibold text-ink-900">
            {{ $post['title'] }}
            @if ($isNotice)
                <x-icon name="arrow-top-right-on-square" class="h-4 w-4 shrink-0 text-ink-900/40" />
            @endif
        </h3>

        @if ($post['excerpt'] ?? null)
            <p class="mt-2 text-sm text-ink-900/60">{{ $post['excerpt'] }}</p>
        @endif
    </div>
</a>
