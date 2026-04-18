<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gumolong Farm') }}</title>

    <!-- Inter font -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-surface">

    {{-- Root Alpine scope: controls sidebar drawer on mobile --}}
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">

        {{-- ── Sidebar ── --}}
        <x-sidebar />

        {{-- ── Main content area ── --}}
        <div class="lg:ml-64 flex flex-col min-h-screen">

            {{-- Top bar (mobile hamburger + page header) --}}
            <header class="sticky top-0 z-10 bg-white border-b border-gray-200">
                <div class="flex items-center gap-3 px-4 sm:px-6 h-16">

                    {{-- Hamburger: only visible on mobile --}}
                    <button
                        type="button"
                        @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden inline-flex items-center justify-center p-2 rounded-md
                               text-gray-500 hover:text-gray-700 hover:bg-gray-100
                               focus:outline-none focus:ring-2 focus:ring-primary transition-colors duration-150"
                        :aria-expanded="sidebarOpen.toString()"
                        aria-label="Toggle navigation"
                    >
                        {{-- Hamburger icon --}}
                        <svg x-show="!sidebarOpen" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        {{-- X icon --}}
                        <svg x-show="sidebarOpen" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    {{-- Page heading --}}
                    @isset($header)
                        <div class="flex-1">
                            {{ $header }}
                        </div>
                    @endisset

                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 px-4 sm:px-6 py-6">
                {{ $slot }}
            </main>

        </div>{{-- end .lg:ml-64 --}}

    </div>{{-- end x-data --}}

</body>
</html>
