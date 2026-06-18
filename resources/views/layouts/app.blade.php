<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('page-title', 'Dashboard') · {{ config('app.name', 'Gumolong Farm') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
    <style>
        [x-cloak]{display:none!important}
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-[#F6F6F3] text-gray-900">

@php
    $notifCount    = $notifCount ?? 0;
    $user          = auth()->user();
    $userNama      = $user?->nama ?? $user?->name ?? 'User';
    $userRole      = $user?->role ?? '';
    $userRoleLabel = $userRole ? ucwords(str_replace('_', ' ', $userRole)) : '';
    $userInitial   = mb_strtoupper(mb_substr($userNama, 0, 1));

    $ALL_WEB   = ['super_admin', 'admin', 'kepala_kandang'];
    $MGMT_ONLY = ['super_admin', 'admin'];

    // ── Icon paths (Heroicons v2 outline 24px) ──────────────────────────────
    $navIcons = [
        'dashboard'       => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
        'domba'           => 'M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L9.568 3z M6 6h.008v.008H6V6z',
        'pakan'           => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z',
        'obat'            => 'M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.278 0-4.513-.334-6.135-.964-1.718-.293-2.3-2.379-1.067-3.61L5 14.5',
        'pertumbuhan'     => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
        'kesehatan'       => 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z',
        'fcr'             => 'M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V13.5zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V18zm2.498-6.75h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V13.5zm0 2.25h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V18zm2.504-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V18zm2.498-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zM8.25 6h7.5v2.25h-7.5V6zM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0012 2.25z',
        'reproduksi'      => 'M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3',
        'silsilah'        => 'M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5',
        'daily-task'      => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z',
        'notifikasi'      => 'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0',
        'users'           => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
        'kandang'         => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z',
    ];

    // ── Nav groups ──────────────────────────────────────────────────────────
    $navGroups = [
        'MENU UTAMA' => [
            ['label' => 'Dashboard',  'route' => 'dashboard',   'active' => 'dashboard',   'roles' => $ALL_WEB, 'icon' => 'dashboard'],
            ['label' => 'Data Domba', 'route' => 'domba.index', 'active' => 'domba.*',     'roles' => $ALL_WEB, 'icon' => 'domba'],
        ],
        'INVENTARIS' => [
            ['label' => 'Stok Pakan',    'route' => 'stok-pakan.index',  'active' => 'stok-pakan.*',  'roles' => $ALL_WEB, 'icon' => 'pakan'],
            ['label' => 'Obat & Vaksin', 'route' => 'obat-vaksin.index', 'active' => 'obat-vaksin.*', 'roles' => $ALL_WEB, 'icon' => 'obat'],
        ],
        'MONITORING' => [
            // Point the sidebar to the tracking-pertumbuhan routes (feature-rich tracking page)
            ['label' => 'Pertumbuhan',   'route' => 'tracking-pertumbuhan.index',      'active' => 'tracking-pertumbuhan.*',      'roles' => $ALL_WEB, 'icon' => 'pertumbuhan'],
            ['label' => 'Kesehatan',     'route' => 'kesehatan.index',        'active' => 'kesehatan.*',        'roles' => $ALL_WEB, 'icon' => 'kesehatan'],
            ['label' => 'Pakan (FCR)',   'route' => 'pakan-individual.index', 'active' => 'pakan-individual.*', 'roles' => $ALL_WEB, 'icon' => 'fcr'],
        ],
        'REPRODUKSI' => [
            ['label' => 'Reproduksi', 'route' => 'reproduksi.index', 'active' => 'reproduksi.*', 'roles' => $ALL_WEB, 'icon' => 'reproduksi'],
            ['label' => 'Silsilah',   'route' => 'silsilah.index',   'active' => 'silsilah.*',   'roles' => $ALL_WEB, 'icon' => 'silsilah'],
        ],
        'OPERASIONAL' => [
            ['label' => 'Daily Task', 'route' => 'tugas-harian.index', 'active' => 'tugas-harian.*', 'roles' => $ALL_WEB, 'icon' => 'daily-task'],
            ['label' => 'Notifikasi', 'route' => 'notifikasi.index',   'active' => 'notifikasi.*',   'roles' => $ALL_WEB, 'icon' => 'notifikasi',
             'badge' => $notifCount > 0 ? (string) $notifCount : null],
        ],
        'ADMIN' => [
            ['label' => 'Manajemen User',    'route' => 'users.index',   'active' => 'users.*',   'roles' => $MGMT_ONLY, 'icon' => 'users'],
            ['label' => 'Manajemen Kandang', 'route' => 'kandang.index', 'active' => 'kandang.*', 'roles' => $MGMT_ONLY, 'icon' => 'kandang'],
        ],
    ];
@endphp

{{-- ════════════════════════════════════════════════════════════════════
     SIDEBAR
════════════════════════════════════════════════════════════════════ --}}
<aside class="fixed inset-y-0 left-0 z-20 w-[220px] bg-white border-r border-gray-100 flex flex-col"
       style="box-shadow: 1px 0 8px 0 rgba(0,0,0,.04)">

    {{-- ── Brand ──────────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100 flex-shrink-0">
        {{-- Logo mark --}}
        <div class="w-9 h-9 rounded-xl bg-[#2E7D32] flex items-center justify-center flex-shrink-0 shadow-sm">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-[13px] font-bold text-gray-900 leading-tight tracking-tight">Gumolong Farm</p>
            <p class="text-[10px] text-gray-400 mt-0.5 font-medium">Management System</p>
        </div>
    </div>

    {{-- ── Navigation ──────────────────────────────────────────────────── --}}
    <nav class="flex-1 overflow-y-auto py-3" style="scrollbar-width:thin;scrollbar-color:#e5e7eb transparent">
        @foreach ($navGroups as $groupName => $items)
            @php
                $visibleItems = collect($items)->filter(fn($item) =>
                    !($item['roles'] ?? null) || in_array($userRole, $item['roles'])
                );
            @endphp

            @if($visibleItems->isNotEmpty())
            <div class="mb-4">
                {{-- Group label --}}
                <p class="px-4 mb-1 text-[9.5px] font-bold uppercase tracking-[0.1em] text-gray-400 select-none">
                    {{ $groupName }}
                </p>

                <ul>
                    @foreach ($visibleItems as $item)
                        @php
                            $activePattern = $item['active'] ?? $item['route'];
                            $isActive      = request()->routeIs($activePattern);
                            $iconPath      = $navIcons[$item['icon'] ?? ''] ?? '';
                        @endphp
                        <li>
                            <a href="{{ route($item['route']) }}"
                               @class([
                                   'group flex items-center gap-2.5 pl-4 pr-3 py-[7px] text-[13px] transition-all duration-150 border-l-[2.5px]',
                                   'border-[#2E7D32] bg-[#2E7D32]/[0.07] text-[#2E7D32] font-semibold'          => $isActive,
                                   'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' => !$isActive,
                               ])
                               @if($isActive) aria-current="page" @endif>

                                {{-- Icon --}}
                                <svg class="w-[15px] h-[15px] flex-shrink-0 transition-colors
                                            {{ $isActive ? 'text-[#2E7D32]' : 'text-gray-400 group-hover:text-gray-600' }}"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}"/>
                                </svg>

                                <span class="flex-1 truncate">{{ $item['label'] }}</span>

                                {{-- Notification badge --}}
                                @if(!empty($item['badge']))
                                <span class="ml-auto min-w-[18px] h-[17px] px-1.5 rounded-full text-[10px]
                                             font-bold flex items-center justify-center leading-none
                                             {{ $isActive ? 'bg-[#2E7D32] text-white' : 'bg-[#B14B6F] text-white' }}">
                                    {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
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

    {{-- ── User footer ─────────────────────────────────────────────────── --}}
    <div class="flex-shrink-0 border-t border-gray-100 px-3 pt-3 pb-4">
        {{-- User card --}}
        <div class="flex items-center gap-2.5 px-1 mb-2">
            <div class="w-8 h-8 rounded-full bg-[#2E7D32]/15 flex items-center justify-center
                        text-[13px] font-bold text-[#2E7D32] flex-shrink-0 ring-2 ring-[#2E7D32]/20">
                {{ $userInitial }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-800 truncate leading-tight">{{ $userNama }}</p>
                <span class="inline-block text-[10px] px-1.5 py-0.5 rounded-md bg-gray-100
                             text-gray-500 font-medium mt-0.5 leading-tight">
                    {{ $userRoleLabel }}
                </span>
            </div>
        </div>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-2 px-2.5 py-2 rounded-lg text-xs font-medium
                           text-gray-500 hover:bg-red-50 hover:text-red-600 transition-colors group">
                <svg class="w-3.5 h-3.5 flex-shrink-0 text-gray-400 group-hover:text-red-500 transition-colors"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                </svg>
                Keluar dari Sistem
            </button>
        </form>
    </div>
</aside>

{{-- ════════════════════════════════════════════════════════════════════
     TOPBAR
════════════════════════════════════════════════════════════════════ --}}
<header class="fixed top-0 right-0 left-[220px] z-10 h-14 bg-white border-b border-gray-100"
        style="box-shadow:0 1px 4px 0 rgba(0,0,0,.05)">
    <div class="h-full flex items-center justify-between px-6">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="flex items-center gap-2 text-sm">
            <span class="text-gray-400 font-medium">Gumolong Farm</span>
            <svg class="w-3 h-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
            </svg>
            <span class="text-gray-800 font-semibold">@yield('page-title', 'Dashboard')</span>
        </nav>

        {{-- Right: bell + user --}}
        <div class="flex items-center gap-3">
            {{-- Bell --}}
            <a href="{{ route('notifikasi.index') }}"
               class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
               aria-label="Notifikasi">
                <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                </svg>
                @if($notifCount > 0)
                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#B14B6F]
                             ring-2 ring-white"></span>
                @endif
            </a>

            {{-- Divider --}}
            <div class="h-5 w-px bg-gray-200"></div>

            {{-- User chip --}}
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-full bg-[#2E7D32]/15 flex items-center justify-center
                            text-xs font-bold text-[#2E7D32]">
                    {{ $userInitial }}
                </div>
                <div class="leading-tight hidden sm:block">
                    <p class="text-[13px] font-semibold text-gray-800">{{ $userNama }}</p>
                    <p class="text-[10px] text-gray-400">{{ $userRoleLabel }}</p>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- ════════════════════════════════════════════════════════════════════
     MAIN CONTENT
════════════════════════════════════════════════════════════════════ --}}
<main class="ml-[220px] pt-14 min-h-screen bg-[#F6F6F3]">
    <div class="p-8">
        @yield('content')
    </div>
</main>

@stack('scripts')

{{-- ═══ GLOBAL TOAST SYSTEM ═══ --}}
<div id="gf-toast-container"
     class="fixed top-4 right-4 z-[99999] flex flex-col gap-2 pointer-events-none"
     style="min-width:280px;max-width:380px">
</div>

<script>
window.showToast = function(message, type) {
    type = type || 'success';
    var container = document.getElementById('gf-toast-container');
    if (!container) return;
    var isSuccess = type === 'success';
    var toast = document.createElement('div');
    toast.style.cssText = 'opacity:0;transform:translateX(100%);transition:opacity .25s,transform .25s;';
    toast.className = 'pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg shadow-xl text-sm font-medium text-white '
        + (isSuccess ? 'bg-[#2E7D32]' : 'bg-[#B14B6F]');
    toast.innerHTML = '<svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">'
        + (isSuccess
            ? '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>'
            : '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>')
        + '</svg><span class="flex-1">' + message + '</span>'
        + '<button onclick="this.parentElement.remove()" class="ml-1 opacity-70 hover:opacity-100 flex-shrink-0">'
        + '<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>'
        + '</button>';
    container.appendChild(toast);
    requestAnimationFrame(function() {
        requestAnimationFrame(function() {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        });
    });
    setTimeout(function() {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(function() { if (toast.parentElement) toast.remove(); }, 280);
    }, 3800);
};

document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        window.showToast(@json(session('success')), 'success');
    @endif
    @if(session('error'))
        window.showToast(@json(session('error')), 'error');
    @endif
    @if($errors->any())
        window.showToast(@json($errors->first()), 'error');
    @endif
});
</script>
</body>
</html>
