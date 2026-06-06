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
            <img src="{{ asset('logo.png') }}" alt="Gumolong Farm" class="h-24 w-auto mx-auto object-contain">
            <p class="mt-2 text-sm text-gray-500">Sistem Manajemen Peternakan Domba</p>
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
