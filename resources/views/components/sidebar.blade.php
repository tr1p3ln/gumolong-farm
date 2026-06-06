@php
$navGroups = [
    'OPERASIONAL' => [
        ['label' => 'Dashboard',      'route' => 'dashboard'],
        ['label' => 'Data Domba',     'route' => 'domba.index'],
        ['label' => 'Stok Pakan',     'route' => 'stok-pakan.index'],
        ['label' => 'Obat & Vaksin',  'route' => 'obat-vaksin.index'],
    ],
    'MONITORING' => [
        ['label' => 'Tracking Pertumbuhan',  'route' => 'pertumbuhan.index'],
        ['label' => 'Kesehatan Ternak',      'route' => 'kesehatan.index'],
        ['label' => 'Pakan Individual (FCR)', 'route' => 'pakan-individual.index'],
    ],
    'REPRODUKSI' => [
        ['label' => 'Reproduksi', 'route' => 'reproduksi.index'],
        ['label' => 'Silsilah',   'route' => 'silsilah.index'],
    ],
    'HARIAN' => [
        ['label' => 'Daily Task',  'route' => 'daily-task.index'],
        ['label' => 'Notifikasi',  'route' => 'notifikasi.index'],
    ],
    'ADMIN' => [
        ['label' => 'User Management', 'route' => 'users.index'],
    ],
];
@endphp

{{-- Mobile backdrop --}}
<div
    x-show="sidebarOpen"
    x-cloak
    x-transition:enter="transition-opacity ease-linear duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"
    aria-hidden="true"
></div>

{{-- Sidebar --}}
<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-30 w-64 flex flex-col bg-surface border-r border-gray-200
           transform transition-transform duration-200 ease-in-out lg:translate-x-0"
>
    {{-- Logo --}}
    <div class="flex items-center h-16 px-6 border-b border-gray-200 flex-shrink-0">
        <span class="font-bold text-xl text-primary font-sans tracking-tight">
            Gumolong Farm
        </span>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-5">
        @foreach ($navGroups as $groupName => $items)
            <div>
                <p class="px-3 mb-1 text-xs font-semibold uppercase tracking-wider text-gray-500 select-none">
                    {{ $groupName }}
                </p>
                <ul class="space-y-0.5">
                    @foreach ($items as $item)
                        @php $active = request()->routeIs($item['route']); @endphp
                        <li>
                            <a
                                href="{{ route($item['route']) }}"
                                @class([
                                    'flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150',
                                    'bg-primary text-white shadow-sm'       =>  $active,
                                    'text-gray-700 hover:bg-gray-100'       => !$active,
                                ])
                                @if($active) aria-current="page" @endif
                            >
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    {{-- Footer: logged-in user --}}
    <div class="flex-shrink-0 border-t border-gray-200 px-4 py-3">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-primary font-semibold text-sm">
                {{ mb_strtoupper(mb_substr(Auth::user()->name ?? '?', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 truncate">{{ Auth::user()->name ?? '' }}</p>
                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email ?? '' }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit"
                class="w-full text-left text-xs text-gray-500 hover:text-red-600 transition-colors duration-150 px-1 py-1">
                Keluar
            </button>
        </form>
    </div>
</aside>
