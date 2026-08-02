<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' - ' : '' }}{{ config('app.name', 'AfraaCMS') }} Admin</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen bg-gray-100">
            <x-admin.sidebar />

            <!-- Mobile sidebar overlay -->
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"
                @click="sidebarOpen = false"
                style="display: none;"
            ></div>

            <div class="flex min-h-screen flex-col lg:pl-64">
                <x-admin.topnav :title="$title ?? null" />

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    @isset($breadcrumbs)
                        <x-admin.breadcrumb :items="$breadcrumbs" />
                    @endisset

                    <x-admin.flash />

                    @isset($header)
                        <div class="mb-6">
                            {{ $header }}
                        </div>
                    @endisset

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
