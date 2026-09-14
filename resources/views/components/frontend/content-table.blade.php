@props(['items', 'actionLabel' => null])

<div class="overflow-x-auto rounded-xl border border-gray-200">
    <table class="w-full min-w-[720px] border-collapse text-left">
        <thead>
            <tr class="border-b border-gray-200 bg-brand-50">
                <th class="w-16 px-4 py-3 text-sm font-bold text-brand-700">{{ __('S.L') }}</th>
                <th class="w-32 px-4 py-3 text-sm font-bold text-brand-700">{{ __('Date') }}</th>
                <th class="w-48 px-4 py-3 text-sm font-bold text-brand-700">{{ __('Title') }}</th>
                <th class="px-4 py-3 text-sm font-bold text-brand-700">{{ __('Description') }}</th>
                <th class="w-28 px-4 py-3 text-sm font-bold text-brand-700">{{ __('Image') }}</th>
                <th class="w-32 px-4 py-3 text-sm font-bold text-brand-700">{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($items as $index => $item)
                <tr class="{{ $index % 2 === 1 ? 'bg-gray-50' : 'bg-white' }}">
                    <td class="px-4 py-4 align-top text-sm text-ink-900">{{ $index + 1 }}</td>
                    <td class="px-4 py-4 align-top text-sm whitespace-nowrap text-ink-900">
                        {{ $item['date'] ? \Illuminate\Support\Carbon::parse($item['date'])->format('d-m-Y') : '—' }}
                    </td>
                    <td class="px-4 py-4 align-top text-sm font-semibold text-ink-900">{{ $item['title'] }}</td>
                    <td class="px-4 py-4 align-top text-sm text-ink-900/70">{{ $item['description'] }}</td>
                    <td class="px-4 py-4 align-top">
                        @if ($item['image_url'])
                            <img src="{{ $item['image_url'] }}" alt="" class="h-16 w-24 rounded-md object-cover">
                        @endif
                    </td>
                    <td class="px-4 py-4 align-top">
                        @if ($item['url'])
                            <a
                                href="{{ $item['url'] }}"
                                @if ($item['new_tab'] ?? false) target="_blank" rel="noopener" @endif
                                class="inline-block rounded-md bg-brand-500 px-4 py-2 text-xs font-semibold text-white transition hover:bg-brand-600"
                            >
                                {{ $actionLabel ?? __('Click Here') }}
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
