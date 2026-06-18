@extends('layouts.app')

@section('page-title', 'Notifikasi')

@section('content')

{{-- ══ PAGE HEADER ══ --}}
<div class="flex items-center justify-between mb-5">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Notifikasi</h1>
    </div>
    <div class="flex items-center gap-3">
        @if($unreadCount > 0)
        <span class="text-sm text-gray-500 font-medium">{{ $unreadCount }} belum dibaca</span>
        <button id="btnMarkAllRead"
            class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold rounded-lg transition">
            Tandai Semua Dibaca
        </button>
        @endif
    </div>
</div>

{{-- ══ FILTER TABS ══ --}}
<div class="flex items-center gap-2 mb-5 flex-wrap">
    @php
        $tabs = [
        ''             => ['label' => 'Semua',        'count' => $counts['semua']],
        'stok_menipis' => ['label' => 'Stok Menipis', 'count' => $counts['stok_menipis']],
        'expired'      => ['label' => 'Expired',      'count' => $counts['expired']],
        'hpl'          => ['label' => 'HPL',          'count' => $counts['hpl']],
        'vaksin'       => ['label' => 'Vaksin',       'count' => $counts['vaksin']],
        'adg_rendah'   => ['label' => 'ADG Rendah',   'count' => $counts['adg_rendah']],
    ];
    @endphp

    @foreach($tabs as $tabTipe => $tab)
        @if($tab['count'] > 0 || $tabTipe === '')
        <a href="{{ route('notifikasi.index', $tabTipe ? ['tipe' => $tabTipe] : []) }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold border transition
                {{ $tipe === ($tabTipe ?: null) || ($tabTipe === '' && !$tipe)
                    ? 'bg-gray-900 text-white border-gray-900'
                    : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400' }}">
            {{ $tab['label'] }}
            @if($tab['count'] > 0)
            <span class="text-xs {{ $tipe === ($tabTipe ?: null) || ($tabTipe === '' && !$tipe) ? 'text-white/80' : 'text-gray-400' }}">
                {{ $tab['count'] }}
            </span>
            @endif
        </a>
        @endif
    @endforeach
</div>

{{-- ══ NOTIFIKASI LIST ══ --}}
<div class="space-y-2">
    @forelse($notifikasi as $notif)
        @php
            $tipeConfig = match($notif->tipe) {
        'stok_menipis' => ['label' => 'STOK MENIPIS', 'bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'dot' => 'bg-orange-500', 'badge' => 'STK'],
        'expired'      => ['label' => 'EXPIRED',      'bg' => 'bg-red-100',    'text' => 'text-red-700',    'dot' => 'bg-red-500',    'badge' => 'EXP'],
        'hpl'          => ['label' => 'HPL',          'bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'dot' => 'bg-purple-500', 'badge' => 'HPL'],
        'vaksin'       => ['label' => 'VAKSIN',       'bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'dot' => 'bg-blue-500',   'badge' => 'VAK'],
        'adg_rendah'   => ['label' => 'ADG RENDAH',   'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'dot' => 'bg-yellow-500', 'badge' => 'ADG'],
        default        => ['label' => 'INFO',         'bg' => 'bg-gray-100',   'text' => 'text-gray-600',   'dot' => 'bg-gray-400',   'badge' => 'INF'],
    };
        @endphp

        <div class="relative bg-white border border-gray-200 rounded-lg px-5 py-4 flex items-start gap-4
            {{ !$notif->sudah_dibaca ? 'border-l-4 border-l-gray-800' : '' }}"
            data-id="{{ $notif->notifikasi_id }}">

            {{-- Dot belum dibaca --}}
            @if(!$notif->sudah_dibaca)
            <div class="absolute left-[-6px] top-1/2 -translate-y-1/2 w-2.5 h-2.5 rounded-full {{ $tipeConfig['dot'] }} ring-2 ring-white"></div>
            @endif

            {{-- Badge tipe --}}
            <div class="flex-shrink-0 w-10 h-10 rounded-lg {{ $tipeConfig['bg'] }} flex items-center justify-center">
                <span class="text-[10px] font-bold {{ $tipeConfig['text'] }}">{{ $tipeConfig['badge'] }}</span>
            </div>

            {{-- Konten --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $tipeConfig['text'] }}">
                        [ {{ $tipeConfig['label'] }} ]
                    </span>
                </div>
                <p class="text-sm font-semibold text-gray-900 mb-0.5">
                    {{ \Illuminate\Support\Str::before($notif->pesan, ' — ') ?: $notif->pesan }}
                </p>
                <p class="text-sm text-gray-600 leading-relaxed">
                    {{ $notif->pesan }}
                </p>
                <div class="flex items-center gap-3 mt-2">
                    <span class="text-xs text-gray-400">
                        {{ \Carbon\Carbon::parse($notif->tanggal_notifikasi)->diffForHumans() }}
                        — {{ \Carbon\Carbon::parse($notif->tanggal_notifikasi)->format('d M Y H:i') }}
                    </span>
                    @if(!$notif->sudah_dibaca)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-800 text-white">
                        Belum Dibaca
                    </span>
                    @endif
                </div>
            </div>

            {{-- Tombol aksi --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                @if(!$notif->sudah_dibaca)
                <button onclick="markRead({{ $notif->notifikasi_id }}, this)"
                    class="text-xs text-gray-500 hover:text-gray-800 border border-gray-200 hover:border-gray-400 px-3 py-1.5 rounded-lg transition font-medium">
                    Tandai Dibaca
                </button>
                @endif
                <button onclick="hapusNotif({{ $notif->notifikasi_id }}, this)"
                    class="text-xs text-gray-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

    @empty
        <div class="bg-white border border-gray-200 rounded-lg px-6 py-16 text-center">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
            </svg>
            <p class="text-gray-400 text-sm font-medium">Tidak ada notifikasi</p>
            <p class="text-gray-300 text-xs mt-1">Semua notifikasi akan muncul di sini</p>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($notifikasi->hasPages())
<div class="mt-4">{{ $notifikasi->links() }}</div>
@endif

<script>
function markRead(id, btn) {
    fetch(`/notifikasi/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const card = btn.closest('[data-id]');
            card.classList.remove('border-l-4', 'border-l-gray-800');
            btn.remove();
            // Update badge lonceng
            updateBadge();
        }
    });
}

function hapusNotif(id, btn) {
    fetch(`/notifikasi/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.closest('[data-id]').remove();
        }
    });
}

// Tandai semua dibaca
document.getElementById('btnMarkAllRead')?.addEventListener('click', function() {
    fetch('/notifikasi/read-all', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) window.location.reload();
    });
});

// Update badge lonceng di navbar via polling
function updateBadge() {
    fetch('/notifikasi/unread-count')
        .then(r => r.json())
        .then(data => {
            const dot = document.querySelector('header .relative .absolute');
            if (data.count === 0 && dot) dot.remove();
        });
}

// Polling setiap 30 detik
setInterval(updateBadge, 30000);
</script>

@endsection