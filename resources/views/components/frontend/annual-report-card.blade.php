@props(['report'])

<a
    href="{{ $report['attachment_url'] ?? '#' }}"
    target="_blank"
    rel="noopener"
    class="group flex items-center gap-4 rounded-2xl bg-white p-6 shadow-md ring-1 ring-black/5 transition hover:shadow-xl"
>
    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600">
        <x-icon name="document-text" class="h-8 w-8" />
    </div>

    <div class="min-w-0">
        <h3 class="truncate text-lg font-semibold text-ink-900">{{ $report['title'] ?? '' }}</h3>
        <p class="mt-1 text-sm font-medium text-brand-600">{{ $report['year'] ?? '' }}</p>
    </div>
</a>
