<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('page-title', 'Dashboard') · {{ config('app.name', 'Gumolong Farm') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>[x-cloak]{display:none!important}</style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-surface text-gray-900">

@php
    $notifCount  = $notifCount ?? 0;
    $user        = auth()->user();
    $userNama    = $user?->nama ?? $user?->name ?? 'User';
    $userRole    = $user?->role ?? '';
    $userRoleLabel = $userRole ? ucwords(str_replace('_', ' ', $userRole)) : '';
    $userInitial = mb_strtoupper(mb_substr($userNama, 0, 1));

    // Role shorthand sets
    $ALL_WEB   = ['super_admin', 'admin', 'kepala_kandang'];          // no pengurus
    $MGMT_ONLY = ['super_admin', 'admin'];
    $ALL_ROLES = ['super_admin', 'admin', 'kepala_kandang', 'pengurus_kandang'];

    $navGroups = [
        'MENU UTAMA' => [
            ['label' => 'Dashboard',  'route' => 'dashboard',    'roles' => $ALL_WEB],
            ['label' => 'Data Domba', 'route' => 'domba.index',  'active' => 'domba.*', 'roles' => $ALL_WEB],
        ],
        'INVENTARIS' => [
            ['label' => 'Stok Pakan',    'route' => 'stok-pakan.index',  'active' => 'stok-pakan.*',  'roles' => $ALL_WEB],
            ['label' => 'Obat & Vaksin', 'route' => 'obat-vaksin.index', 'active' => 'obat-vaksin.*', 'roles' => $ALL_WEB],
        ],
        'MONITORING' => [
            ['label' => 'Tracking Pertumbuhan', 'route' => 'pertumbuhan.index',      'active' => 'pertumbuhan.*',      'roles' => $ALL_WEB],
            ['label' => 'Kesehatan Ternak',     'route' => 'kesehatan.index',        'active' => 'kesehatan.*',        'roles' => $ALL_WEB],
            ['label' => 'Pakan Individual',     'route' => 'pakan-individual.index', 'active' => 'pakan-individual.*', 'roles' => $ALL_WEB],
        ],
        'REPRODUKSI' => [
            ['label' => 'Reproduksi', 'route' => 'reproduksi.index', 'active' => 'reproduksi.*', 'roles' => $ALL_WEB],
            // Silsilah: No Access for Pengurus Kandang
            ['label' => 'Silsilah',   'route' => 'silsilah.index',   'active' => 'silsilah.*',   'roles' => $ALL_WEB],
        ],
        'OPERASIONAL' => [
            ['label' => 'Daily Task', 'route' => 'tugas-harian.index', 'active' => 'tugas-harian.*', 'roles' => $ALL_WEB],
            ['label' => 'Notifikasi', 'route' => 'notifikasi.index',   'active' => 'notifikasi.*',
             'badge' => $notifCount > 0 ? (string) $notifCount : null, 'roles' => $ALL_WEB],
        ],
        'ADMIN' => [
            // Account Management: Super Admin & Admin only
            ['label' => 'Manajemen User', 'route' => 'users.index', 'active' => 'users.*', 'roles' => $MGMT_ONLY],
        ],
    ];
@endphp

{{-- ============ SIDEBAR (fixed, 200px) ============ --}}
<aside class="fixed inset-y-0 left-0 z-20 w-[200px] bg-white border-r border-gray-200 flex flex-col">

    {{-- Logo area --}}
    <div class="px-4 pt-4 pb-3 border-b border-gray-100 flex-shrink-0">
        <div class="border border-dashed border-gray-300 rounded-md px-3 py-2 text-center">
            <span class="block text-[10px] uppercase tracking-wider text-gray-500 font-semibold leading-tight">
                LOGO · GUMOLONG FARM
            </span>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-4">
        @foreach ($navGroups as $groupName => $items)
            @php
                // Filter items the current user is allowed to see
                $visibleItems = collect($items)->filter(function ($item) use ($userRole) {
                    $allowed = $item['roles'] ?? null;
                    return !$allowed || in_array($userRole, $allowed);
                });
            @endphp

            @if($visibleItems->isNotEmpty())
                <div>
                    <p class="px-2 mb-1 text-[10px] font-semibold uppercase tracking-wider text-gray-400 select-none">
                        {{ $groupName }}
                    </p>
                    <ul class="space-y-0.5">
                        @foreach ($visibleItems as $item)
                            @php
                                $activePattern = $item['active'] ?? $item['route'];
                                $isActive      = request()->routeIs($activePattern);
                            @endphp
                            <li>
                                <a href="{{ route($item['route']) }}"
                                   @class([
                                       'flex items-center gap-2.5 px-2.5 py-2 rounded-md text-sm transition-colors duration-150',
                                       'bg-primary text-white font-semibold shadow-sm' => $isActive,
                                       'text-gray-600 hover:bg-gray-100 font-medium'   => !$isActive,
                                   ])
                                   @if($isActive) aria-current="page" @endif>

                                    <span @class([
                                        'w-1.5 h-1.5 rounded-full flex-shrink-0',
                                        'bg-white'               => $isActive,
                                        'border border-gray-400' => !$isActive,
                                    ])></span>

                                    <span class="flex-1 truncate">{{ $item['label'] }}</span>

                                    @if(!empty($item['badge']))
                                        <span class="ml-auto text-[10px] font-semibold px-1.5 py-0.5 rounded-full
                                                     {{ $isActive ? 'bg-white text-primary' : 'bg-accent text-white' }}">
                                            {{ $item['badge'] }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    </nav>

    {{-- Bottom user area --}}
    <div class="flex-shrink-0 border-t border-gray-100 px-3 py-3">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-md bg-gray-200 flex items-center justify-center text-sm font-semibold text-gray-600">
                {{ $userInitial }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-800 truncate">{{ $userNama }}</p>
                <p class="text-[10px] text-gray-500 truncate">{{ $userRoleLabel }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit"
                    class="w-full text-left text-[11px] text-gray-500 hover:text-accent transition-colors px-1 py-1">
                Logout
            </button>
        </form>
    </div>
</aside>

{{-- ============ TOPBAR (fixed) ============ --}}
<header class="fixed top-0 right-0 left-[200px] z-10 h-16 bg-white border-b border-gray-200">
    <div class="h-full flex items-center justify-between px-6">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="text-sm">
            <span class="text-gray-500">Gumolong Farm</span>
            <span class="mx-2 text-gray-300">/</span>
            <span class="text-gray-900 font-semibold">@yield('page-title', 'Dashboard')</span>
        </nav>

        {{-- Right cluster: bell + avatar --}}
        <div class="flex items-center gap-4">

            {{-- Bell --}}
            <a href="{{ route('notifikasi.index') }}"
               class="relative p-2 rounded-md hover:bg-gray-100 transition-colors"
               aria-label="Notifikasi">
                <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                </svg>
                @if($notifCount > 0)
                    <span class="absolute top-1 right-1 min-w-[16px] h-4 px-1 rounded-full
                                 bg-amber-500 text-white text-[10px] font-semibold
                                 flex items-center justify-center">
                        {{ $notifCount > 99 ? '99+' : $notifCount }}
                    </span>
                @endif
            </a>

            {{-- Avatar + name + role --}}
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-md bg-gray-200 flex items-center justify-center text-sm font-semibold text-gray-600">
                    {{ $userInitial }}
                </div>
                <div class="leading-tight">
                    <p class="text-sm font-semibold text-gray-900">{{ $userNama }}</p>
                    <p class="text-[11px] text-gray-500">{{ $userRoleLabel }}</p>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- ============ MAIN CONTENT ============ --}}
<main class="ml-[200px] pt-16 min-h-screen bg-surface">
    <div class="p-8">
        @yield('content')
    </div>
</main>

@stack('scripts')
</body>
</html>
