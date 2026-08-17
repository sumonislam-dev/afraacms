<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>AfraaCMS</title>

        {{-- Backend chrome (login/register/password screens) always shows
             AfraaCMS's own branding, never a client's - see
             layouts/admin.blade.php for why this is hardcoded rather than
             reading config('app.name') or any setting()/theme value. --}}
        <link rel="icon" type="image/png" href="{{ asset('backend/favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-screen overflow-hidden font-sans text-gray-900 antialiased">
        <div class="flex h-full overflow-hidden">
            {{-- AfraaWorld brand panel: fixed identity, identical on every
                 client install. Background is #fdfdfd - the exact color
                 sampled from logo-afraaworld.png's own canvas (verified via
                 corner-pixel sampling), so logo-mark.png's transparent-cut
                 edges sit on a background that matches it exactly, with no
                 visible seam. Kept deliberately compact (h-screen, no
                 scroll) - see guest layout redesign notes. Hidden below lg:
                 on a phone this panel would push the sign-in form below the
                 fold, so only the form shows there. --}}
            <div class="relative hidden w-full max-w-md flex-col overflow-hidden border-r border-gray-100 p-8 lg:flex xl:max-w-lg" style="background-color: #fdfdfd">
                <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-[#00a8ab]/6 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-24 -right-16 h-80 w-80 rounded-full bg-[#0057ef]/6 blur-3xl"></div>

                <div class="relative flex flex-1 flex-col items-start justify-center">
                    <img src="{{ asset('backend/logo-mark.png') }}" alt="" class="h-14 w-auto shrink-0">
                    <p class="mt-5 w-full text-3xl font-bold tracking-tight text-[#000023]">AfraaWorld</p>
                    <p class="mt-1.5 w-full text-sm font-semibold uppercase tracking-widest text-[#00a8ab]">{{ __('Building Smart Digital Solutions') }}</p>

                    <ul class="mt-7 w-full space-y-4">
                        <li class="flex items-center gap-3 text-base text-gray-700">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#0057ef]/10 text-[#0057ef]">
                                <x-admin.icon name="squares-2x2" class="h-5 w-5" />
                            </span>
                            {{ __('All your content in one place') }}
                        </li>
                        <li class="flex items-center gap-3 text-base text-gray-700">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#00a8ab]/10 text-[#00a8ab]">
                                <x-admin.icon name="shield-check" class="h-5 w-5" />
                            </span>
                            {{ __('Secure, role-based access') }}
                        </li>
                        <li class="flex items-center gap-3 text-base text-gray-700">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#000023]/5 text-[#000023]">
                                <x-admin.icon name="globe-alt" class="h-5 w-5" />
                            </span>
                            {{ __('Built for every client site') }}
                        </li>
                    </ul>
                </div>

                <p class="relative text-sm text-gray-400">&copy; {{ date('Y') }} AfraaWorld. {{ __('All rights reserved.') }}</p>
            </div>

            {{-- Client-specific sign-in panel: the client's own logo and name
                 lead here, large and prominent, so an admin managing several
                 client backends can tell at a glance which one they're on. --}}
            <div class="flex w-full flex-1 flex-col items-center justify-center overflow-y-auto bg-gray-50 px-6 py-3 sm:px-12">
                <div class="w-full max-w-sm">
                    <div class="flex justify-end">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                            <x-admin.icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
                            {{ __('Back to website') }}
                        </a>
                    </div>

                    <div class="mt-3 flex flex-col items-center text-center">
                        <a href="{{ url('/') }}">
                            @if (setting('logo'))
                                <img src="{{ media_url(setting('logo')) }}" alt="{{ setting('site_name') }}" class="h-14 w-14 rounded-full object-cover ring-1 ring-gray-200">
                            @else
                                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-xl font-semibold text-indigo-600">
                                    {{ \Illuminate\Support\Str::substr(setting('site_name', config('app.name', 'AfraaCMS')), 0, 1) }}
                                </span>
                            @endif
                        </a>
                        <p class="mt-2 text-xl font-bold text-gray-900">{{ setting('site_name', config('app.name', 'AfraaCMS')) }}</p>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Sign in to manage your site') }}</p>
                    </div>

                    <div class="mt-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-900/5 sm:p-5">
                        {{ $slot }}
                    </div>

                    <p class="mt-4 text-center text-xs text-gray-400">
                        {{ __('Powered by') }}
                        <a href="https://afraaworld.com" target="_blank" rel="noopener" class="font-medium text-gray-500 hover:text-gray-700">AfraaWorld</a>
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
