<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gumolong Farm') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-surface text-gray-900">
    <div class="min-h-screen flex flex-col items-center justify-center p-6">

        {{-- Brand --}}
        <div class="mb-6 text-center">
            <div class="inline-block border border-dashed border-gray-300 rounded-md px-4 py-2">
                <span class="text-xs uppercase tracking-widest text-gray-500 font-semibold">
                    LOGO · GUMOLONG FARM
                </span>
            </div>
            <p class="mt-3 text-sm text-gray-500">Sistem Manajemen Peternakan Domba</p>
        </div>

        {{-- Card --}}
        <div class="w-full max-w-md bg-white border border-gray-100 rounded-md shadow-sm px-6 py-6">
            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
