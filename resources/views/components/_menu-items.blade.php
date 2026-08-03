@php
    $level = $level ?? 0;
@endphp

@if ($level === 0)
    <ul class="flex items-center gap-6">
        @foreach ($items as $item)
            <li class="group relative">
                <a
                    href="{{ $item['resolved_url'] }}"
                    target="{{ $item['target'] }}"
                    @if ($item['type'] === 'external') rel="noopener" @endif
                    class="flex items-center gap-1.5 text-sm font-medium text-gray-700 hover:text-gray-900"
                >
                    @if ($item['icon'])
                        <x-icon :name="$item['icon']" class="h-4 w-4" />
                    @endif
                    {{ $item['label'] }}
                </a>

                @if (! empty($item['children']))
                    <div class="invisible absolute left-0 top-full z-20 mt-1 min-w-48 rounded-md bg-white py-2 opacity-0 shadow-lg ring-1 ring-black/5 transition-opacity group-hover:visible group-hover:opacity-100">
                        @include('components._menu-items', ['items' => $item['children'], 'level' => 1])
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
@else
    <ul class="space-y-0.5 px-2">
        @foreach ($items as $item)
            <li>
                <a
                    href="{{ $item['resolved_url'] }}"
                    target="{{ $item['target'] }}"
                    @if ($item['type'] === 'external') rel="noopener" @endif
                    class="flex items-center gap-1.5 rounded-sm px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-50"
                    style="padding-left: {{ ($level - 1) * 12 + 8 }}px;"
                >
                    @if ($item['icon'])
                        <x-icon :name="$item['icon']" class="h-4 w-4" />
                    @endif
                    {{ $item['label'] }}
                </a>

                @if (! empty($item['children']))
                    @include('components._menu-items', ['items' => $item['children'], 'level' => $level + 1])
                @endif
            </li>
        @endforeach
    </ul>
@endif
