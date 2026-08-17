<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <x-seo-meta
            :title="$title ?? null"
            :description="$description ?? null"
            :image="$image ?? null"
            :url="$canonical ?? null"
            :robots="$robots ?? null"
        />

        @if (setting('favicon'))
            <link rel="icon" href="{{ media_url(setting('favicon')) }}">
        @endif

        @php
            // Theme customization: one brand color + two font pickers in
            // Settings > Branding drive every client site's look, without a
            // per-client Tailwind rebuild. See config/fonts.php and
            // app/CMS/Helpers/theme.php for how these resolve.
            $headingFont = config('fonts.heading.'.setting('heading_font', 'merriweather'), config('fonts.heading.merriweather'));
            $bodyFont = config('fonts.body.'.setting('body_font', 'inter'), config('fonts.body.inter'));
            $brandShades = theme_color_shades(setting('brand_color', '#f96d00'));
        @endphp

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family={{ $headingFont['google'] }}&family={{ $bodyFont['google'] }}&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Overrides Tailwind's compiled --font-*/--color-brand-* custom
             properties at request time - must come after @vite above so it
             wins the cascade. Raw (unescaped) output is intentional: this is
             all server-controlled data (config/fonts.php entries and hex
             codes computed by theme_color_shades()), never user input, and
             Blade's default {{ }} HTML-escaping would corrupt the quotes in
             a font-family value sitting inside a <style> block. --}}
        <style>
            :root {
                --font-display: {!! $headingFont['family'] !!};
                --font-body: {!! $bodyFont['family'] !!};
                @foreach ($brandShades as $shade => $color)
                    --color-brand-{{ $shade }}: {!! $color !!};
                @endforeach
            }
        </style>
    </head>
    <body class="font-body text-ink-900 antialiased" x-data="{ mobileMenuOpen: false }">
        <header class="fixed top-0 inset-x-0 z-50 bg-ink-900/95 backdrop-blur supports-backdrop-filter:bg-ink-900/80 shadow-lg shadow-black/10">
            <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-20 items-center justify-between">
                    <a href="{{ url('/') }}" class="flex shrink-0 items-center gap-3">
                        @if (setting('logo'))
                            <img
                                src="{{ media_url(setting('logo')) }}"
                                alt="{{ setting('site_name') }}"
                                class="h-12 w-12 rounded-full object-cover ring-2 ring-brand-500"
                            >
                        @else
                            <span class="font-display text-lg font-bold leading-tight text-white">{{ setting('site_name', config('app.name', 'AfraaCMS')) }}</span>
                        @endif
                    </a>

                    <x-menu slug="header" class="hidden items-center gap-1 lg:flex" />

                    <button type="button" class="p-2 text-white lg:hidden" @click="mobileMenuOpen = ! mobileMenuOpen" aria-label="{{ __('Toggle navigation') }}" :aria-expanded="mobileMenuOpen">
                        <span class="sr-only">{{ __('Menu') }}</span>
                        <x-icon name="bars-3" class="h-7 w-7" />
                    </button>
                </div>

                <div x-show="mobileMenuOpen" x-transition class="space-y-1 pb-4 lg:hidden" style="display: none;">
                    @php($headerMenu = menu('header'))
                    @if ($headerMenu && ! empty($headerMenu['tree']))
                        @include('components._menu-items', ['items' => $headerMenu['tree'], 'level' => 1, 'dark' => true])
                    @endif
                </div>
            </nav>
        </header>

        {{-- The homepage's own Hero section is full-height and meant to sit
             behind the transparent-ish fixed header; every other page's
             first section (the page banner) is much shorter and needs the
             header's height offset so it isn't hidden underneath it. --}}
        <main class="{{ $isHome ? '' : 'pt-20' }}">
            {{ $slot }}
        </main>

        <x-banner type="cta" />

        <footer class="bg-black text-white/70">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:grid-cols-2 sm:px-6 lg:grid-cols-4 lg:px-8">
                <div>
                    <div class="mb-4 flex items-center gap-3">
                        @if (setting('logo'))
                            <img src="{{ media_url(setting('logo')) }}" alt="{{ setting('site_name') }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-brand-500">
                        @endif
                        <span class="font-display font-bold text-white">{{ setting('site_name', config('app.name', 'AfraaCMS')) }}</span>
                    </div>

                    @if (setting('tagline'))
                        <p class="text-sm leading-relaxed">{{ setting('tagline') }}</p>
                    @endif

                    <div class="mt-5 flex gap-3">
                        @foreach (['facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube', 'instagram' => 'Instagram', 'twitter' => 'Twitter / X'] as $network => $label)
                            @if (setting($network))
                                <a
                                    href="{{ setting($network) }}"
                                    target="_blank"
                                    rel="noopener"
                                    aria-label="{{ $label }}"
                                    class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 transition hover:bg-brand-500"
                                >
                                    <x-frontend.social-icon :name="$network" class="h-4 w-4" />
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                @php($footerMenu = menu('footer'))
                @if ($footerMenu && ! empty($footerMenu['tree']))
                    <div>
                        <h4 class="mb-4 font-semibold text-white">{{ __('Quick Links') }}</h4>
                        <ul class="space-y-2 text-sm">
                            @foreach (array_slice($footerMenu['tree'], 0, (int) setting('footer_links_limit', 6)) as $item)
                                <li><a href="{{ $item['resolved_url'] }}" target="{{ $item['target'] }}" class="transition hover:text-brand-400">{{ $item['label'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @php($footerProjects = array_slice(app(\App\CMS\Services\ProjectService::class)->all(), 0, (int) setting('footer_projects_limit', 5)))
                @if (! empty($footerProjects))
                    <div>
                        <h4 class="mb-4 font-semibold text-white">{{ __('Our Projects') }}</h4>
                        <ul class="space-y-2 text-sm">
                            @foreach ($footerProjects as $project)
                                <li><a href="{{ route('projects.show', $project['slug']) }}" class="transition hover:text-brand-400">{{ $project['title'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (setting('contact_address') || setting('contact_phone') || setting('contact_email'))
                    <div>
                        <h4 class="mb-4 font-semibold text-white">{{ __('Contact') }}</h4>
                        <ul class="space-y-3 text-sm">
                            @if (setting('contact_address'))
                                <li>{{ setting('contact_address') }}</li>
                            @endif
                            @if (setting('contact_phone'))
                                <li><a href="tel:{{ preg_replace('/[^0-9+]/', '', setting('contact_phone')) }}" class="transition hover:text-brand-400">{{ setting('contact_phone') }}</a></li>
                            @endif
                            @if (setting('contact_email'))
                                <li><a href="mailto:{{ setting('contact_email') }}" class="transition hover:text-brand-400">{{ setting('contact_email') }}</a></li>
                            @endif
                        </ul>
                    </div>
                @endif
            </div>

            <div class="border-t border-white/10">
                <div class="mx-auto flex max-w-7xl flex-col justify-between gap-2 px-4 py-6 text-xs text-white/50 sm:flex-row sm:px-6 lg:px-8">
                    <p>{{ setting('copyright', '© '.date('Y').' '.config('app.name', 'AfraaCMS').'. All rights reserved.') }}</p>

                    @if (setting('footer_text'))
                        <p>{{ setting('footer_text') }}</p>
                    @endif

                    @if (setting('developer_credit_text'))
                        <p>
                            @if (setting('developer_credit_url'))
                                <a href="{{ setting('developer_credit_url') }}" target="_blank" rel="noopener" class="hover:text-white">{{ setting('developer_credit_text') }}</a>
                            @else
                                {{ setting('developer_credit_text') }}
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </footer>

        <x-banner type="popup" />

        @stack('scripts')
    </body>
</html>
