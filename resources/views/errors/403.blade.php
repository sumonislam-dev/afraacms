<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ __('Access Denied') }} - {{ config('app.name', 'AfraaCMS') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="flex min-h-screen items-center justify-center bg-gray-100 px-4">
            <div class="w-full max-w-md rounded-lg bg-white p-8 text-center shadow">
                <p class="text-sm font-semibold uppercase tracking-wide text-red-500">{{ __('403') }}</p>
                <h1 class="mt-2 text-2xl font-semibold text-gray-900">{{ __('Access Denied') }}</h1>
                <p class="mt-2 text-sm text-gray-600">
                    {{ __('You do not have permission to access this page.') }}
                </p>

                <div class="mt-6 flex items-center justify-center gap-3">
                    @auth
                        @if (Route::has('admin.dashboard') && Auth::user()->can('dashboard.view'))
                            <x-primary-button type="button" onclick="window.location='{{ route('admin.dashboard') }}'">
                                {{ __('Go to Dashboard') }}
                            </x-primary-button>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-secondary-button>{{ __('Log Out') }}</x-secondary-button>
                        </form>
                    @else
                        <x-primary-button type="button" onclick="window.location='{{ route('login') }}'">
                            {{ __('Log In') }}
                        </x-primary-button>
                    @endauth
                </div>
            </div>
        </div>
    </body>
</html>
