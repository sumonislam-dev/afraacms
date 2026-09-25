<x-frontend-layout
    :title="$cmsPage['seo']['title'] ?? __('Annual Reports')"
    :description="$cmsPage['seo']['description'] ?? null"
    :canonical="$cmsPage['seo']['canonical_url'] ?? null"
    :robots="$cmsPage['seo']['robots'] ?? null"
>
    @php
        $startsWithHero = ($cmsPage['sections'][0]['type'] ?? null) === 'hero';
    @endphp

    <x-banner
        type="page"
        :override="['title' => $cmsPage['banner_eyebrow'] ?? null, 'image_url' => $cmsPage['banner_image_url'] ?? null]"
        :page-title="$startsWithHero ? null : ($cmsPage['title'] ?? __('Annual Reports'))"
    />

    <x-sections :sections="$cmsPage['sections'] ?? []" />

    <div class="mx-auto max-w-4xl px-4 py-16 sm:px-6">
        @if (empty($reports))
            <p class="text-center text-gray-500">{{ __('No annual reports published yet.') }}</p>
        @else
            <ul class="divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white">
                @foreach ($reports as $report)
                    <li>
                        <a
                            href="{{ $report['attachment_url'] }}"
                            target="_blank"
                            rel="noopener"
                            class="flex items-center justify-between gap-4 px-6 py-4 hover:bg-gray-50"
                        >
                            <span class="flex items-center gap-3">
                                <x-icon name="document-text" class="h-6 w-6 shrink-0 text-brand-600" />
                                <span class="font-medium text-gray-900">{{ $report['title'] }}</span>
                            </span>

                            <span class="flex shrink-0 items-center gap-2 text-sm text-gray-500">
                                {{ $report['year'] }}
                                <x-icon name="arrow-top-right-on-square" class="h-4 w-4" />
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-frontend-layout>
