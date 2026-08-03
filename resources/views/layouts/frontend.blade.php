<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <x-seo-meta :title="$title ?? null" />

        @if (setting('favicon'))
            <link rel="icon" href="{{ media_url(setting('favicon')) }}">
        @endif

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900" x-data="{ mobileMenuOpen: false }">
        <header class="border-b border-gray-100 bg-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    @if (setting('logo'))
                        <img
                            src="{{ media_url(setting('logo')) }}"
                            alt="{{ setting('site_name') }}"
                            class="h-9 w-auto"
                        >
                    @else
                        <span class="text-lg font-semibold">{{ setting('site_name', config('app.name', 'AfraaCMS')) }}</span>
                    @endif
                </a>

                <x-menu slug="header" class="hidden sm:flex" />

                <button type="button" class="text-gray-500 sm:hidden" @click="mobileMenuOpen = ! mobileMenuOpen">
                    <span class="sr-only">{{ __('Menu') }}</span>
                    <x-icon name="bars-3" class="h-6 w-6" />
                </button>
            </div>

            <div x-show="mobileMenuOpen" x-transition class="border-t border-gray-100 px-4 py-3 sm:hidden" style="display: none;">
                @php($headerMenu = menu('header'))
                @if ($headerMenu && ! empty($headerMenu['tree']))
                    @include('components._menu-items', ['items' => $headerMenu['tree'], 'level' => 1])
                @endif
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        <x-banner type="cta" />

        <footer class="mt-12 border-t border-gray-100 bg-gray-50">
            <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <p class="text-sm text-gray-500">
                        {{ setting('copyright', '© '.date('Y').' '.config('app.name', 'AfraaCMS').'. All rights reserved.') }}
                    </p>

                    <div class="flex items-center gap-4">
                        @foreach (['facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube', 'instagram' => 'Instagram', 'twitter' => 'Twitter / X'] as $network => $label)
                            @if (setting($network))
                                <a href="{{ setting($network) }}" target="_blank" rel="noopener" class="text-sm text-gray-500 hover:text-gray-700">
                                    {{ $label }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                @if (setting('footer_text'))
                    <p class="mt-4 text-center text-xs text-gray-400">{{ setting('footer_text') }}</p>
                @endif

                @if (setting('contact_email') || setting('contact_phone'))
                    <p class="mt-2 text-center text-xs text-gray-400">
                        {{ collect([setting('contact_email'), setting('contact_phone')])->filter()->implode(' · ') }}
                    </p>
                @endif
            </div>
        </footer>

        <x-banner type="popup" />
    </body>
</html>
