@php
    $level = $level ?? 0;
    // "dark" styles light text for panels that sit directly on the dark
    // navbar (the mobile menu); the desktop dropdown is a white panel and
    // always uses dark text regardless of this flag.
    $dark = $dark ?? false;

    $currentUrl = rtrim(request()->url(), '/');
    $isActiveUrl = fn (?string $url): bool => $url && rtrim($url, '/') === $currentUrl;
    // A dropdown parent is also marked active when the page you're on is
    // one of its children, so e.g. "Programs" stays highlighted while
    // viewing an individual program page under it.
    $hasActiveDescendant = function (array $item) use (&$hasActiveDescendant, $isActiveUrl) {
        if ($isActiveUrl($item['resolved_url'] ?? null)) {
            return true;
        }

        foreach ($item['children'] ?? [] as $child) {
            if ($hasActiveDescendant($child)) {
                return true;
            }
        }

        return false;
    };
@endphp

@if ($level === 0)
    <ul class="flex items-center gap-1">
        @foreach ($items as $item)
            @php($isActive = $hasActiveDescendant($item))
            <li class="group relative">
                <a
                    href="{{ $item['resolved_url'] }}"
                    target="{{ $item['target'] }}"
                    @if ($item['type'] === 'external') rel="noopener" @endif
                    @if ($isActive) aria-current="page" @endif
                    @class([
                        'relative flex items-center gap-1.5 px-4 py-2 text-sm font-medium transition',
                        'text-white' => $isActive,
                        'text-white/80 hover:text-white' => ! $isActive,
                    ])
                >
                    @if ($item['icon'])
                        <x-icon :name="$item['icon']" class="h-4 w-4" />
                    @endif
                    {{ $item['label'] }}

                    @if (! empty($item['children']))
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                    @endif

                    @if ($isActive)
                        <span class="absolute inset-x-4 -bottom-0.5 h-0.5 rounded-full bg-brand-500"></span>
                    @endif
                </a>

                @if (! empty($item['children']))
                    <div class="invisible absolute left-0 top-full z-20 w-56 pt-2 opacity-0 transition group-hover:visible group-hover:opacity-100">
                        <div class="rounded-xl bg-white py-2 shadow-xl ring-1 ring-black/5">
                            @include('components._menu-items', ['items' => $item['children'], 'level' => 1, 'dark' => false])
                        </div>
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
@else
    <ul class="space-y-0.5 px-2">
        @foreach ($items as $item)
            @php($isActive = $hasActiveDescendant($item))
            <li>
                <a
                    href="{{ $item['resolved_url'] }}"
                    target="{{ $item['target'] }}"
                    @if ($item['type'] === 'external') rel="noopener" @endif
                    @if ($isActive) aria-current="page" @endif
                    @class([
                        'flex items-center gap-1.5 rounded-md px-4 py-2 text-sm transition',
                        'bg-white/10 text-white' => $isActive && $dark,
                        'text-white/80 hover:bg-white/10 hover:text-white' => ! $isActive && $dark,
                        'bg-brand-50 text-brand-600' => $isActive && ! $dark,
                        'text-ink-800 hover:bg-brand-50 hover:text-brand-600' => ! $isActive && ! $dark,
                    ])
                    style="padding-left: {{ ($level - 1) * 12 + 16 }}px;"
                >
                    @if ($item['icon'])
                        <x-icon :name="$item['icon']" class="h-4 w-4" />
                    @endif
                    {{ $item['label'] }}
                </a>

                @if (! empty($item['children']))
                    @include('components._menu-items', ['items' => $item['children'], 'level' => $level + 1, 'dark' => $dark])
                @endif
            </li>
        @endforeach
    </ul>
@endif
