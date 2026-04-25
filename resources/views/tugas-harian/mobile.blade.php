<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tugas Hari Ini — Gumolong Farm</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: #FAFAF7; font-family: 'Inter', sans-serif; }
        .safe-top    { padding-top: env(safe-area-inset-top, 16px); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom, 24px); }
        .task-card { transition: all 0.2s ease; user-select: none; }
        .task-card:active { transform: scale(0.98); }
        .task-belum        { border-left: 5px solid #9CA3AF; }
        .task-dalam-proses { border-left: 5px solid #D97706; }
        .task-selesai      { border-left: 5px solid #2E7D32; }
        .task-dilewati     { border-left: 5px solid #B14B6F; }
        .btn-action {
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            padding: 10px 16px;
            transition: all 0.15s;
            border: none;
            cursor: pointer;
        }
        .btn-action:active { transform: scale(0.95); }
    </style>
</head>
<body>

<div x-data="{ loading: false }">

{{-- ══════════════════════════════
     TOP BAR MOBILE
══════════════════════════════ --}}
<div class="safe-top bg-[#2E7D32] text-white px-5 pt-2 pb-5">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center">
                <span class="text-sm font-black">🐑</span>
            </div>
            <div>
                <p class="text-xs font-bold text-white/70 uppercase tracking-wider">Gumolong Farm</p>
                <p class="text-sm font-bold leading-none">
                    {{ auth()->user()->nama ?? auth()->user()->name ?? 'Barn Keeper' }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            {{-- Web Admin shortcut — hidden for Pengurus Kandang (no web access) --}}
            @if(auth()->user()->role !== 'pengurus_kandang')
                <a href="{{ route('tugas-harian.index') }}"
                   class="text-white/70 hover:text-white text-xs font-bold transition-colors">
                    ← Web
                </a>
            @endif

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-1.5 text-white/70 hover:text-white
                               text-xs font-bold transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </div>

    <h1 class="text-2xl font-black tracking-tight">Tugas Hari Ini</h1>
    <p class="text-white/70 text-sm mt-0.5">
        {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
    </p>

    {{-- Progress Ring --}}
    <div class="mt-4 flex items-center gap-4">
        <div class="relative w-16 h-16 flex-shrink-0">
            <svg class="w-16 h-16 -rotate-90" viewBox="0 0 64 64">
                <circle cx="32" cy="32" r="28" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="6"/>
                <circle cx="32" cy="32" r="28" fill="none" stroke="white" stroke-width="6"
                        stroke-dasharray="{{ round($summary['persen'] * 1.759) }} 175.9"
                        stroke-linecap="round"/>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-white font-black text-sm">{{ $summary['persen'] }}%</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-x-6 gap-y-1">
            <div>
                <span class="font-black text-xl">{{ $summary['selesai'] }}</span>
                <span class="text-white/70 text-xs ml-1">selesai</span>
            </div>
            <div>
                <span class="font-black text-xl">{{ $summary['belum'] }}</span>
                <span class="text-white/70 text-xs ml-1">sisa</span>
            </div>
            <div>
                <span class="font-black text-xl">{{ $summary['total'] }}</span>
                <span class="text-white/70 text-xs ml-1">total</span>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════
     DAFTAR TUGAS
══════════════════════════════ --}}
<div class="px-4 py-5 space-y-3 safe-bottom">

    @forelse($tugas as $t)
    @php
        $borderClass = match($t->status) {
            'selesai'      => 'task-selesai',
            'dalam_proses' => 'task-dalam-proses',
            'dilewati'     => 'task-dilewati',
            default        => 'task-belum',
        };
        $bgClass = match($t->status) {
            'selesai'  => 'bg-green-50',
            'dilewati' => 'bg-red-50 opacity-60',
            default    => 'bg-white',
        };
        $prioritasBadge = [
            'tinggi' => 'bg-red-100 text-red-700',
            'sedang' => 'bg-amber-100 text-amber-700',
            'rendah' => 'bg-green-100 text-green-700',
        ][$t->prioritas] ?? 'bg-gray-100 text-gray-600';
        $prioritasLabel = ['tinggi' => 'Tinggi', 'sedang' => 'Sedang', 'rendah' => 'Rendah'][$t->prioritas] ?? $t->prioritas;
    @endphp

    <div class="task-card {{ $borderClass }} {{ $bgClass }} rounded-xl shadow-sm overflow-hidden">
        <div class="p-4">
            <div class="flex items-start justify-between gap-3 mb-2">
                <div class="flex-1 min-w-0">
                    <span class="inline-block px-2 py-0.5 text-xs font-bold rounded-full mb-2 {{ $prioritasBadge }}">
                        {{ $prioritasLabel }}
                    </span>

                    <h3 class="font-bold text-gray-900 text-base leading-snug
                               {{ $t->status === 'selesai' ? 'line-through text-gray-400' : '' }}">
                        {{ $t->judul }}
                    </h3>

                    @if($t->deskripsi)
                    <p class="text-sm text-gray-500 mt-1 leading-relaxed">{{ $t->deskripsi }}</p>
                    @endif

                    <div class="flex items-center gap-3 mt-2 flex-wrap">
                        <span class="text-xs font-bold text-[#2E7D32]">
                            📍 {{ $t->kandang?->nama_kandang ?? '-' }}
                        </span>
                        @if($t->waktu_mulai)
                        <span class="text-xs text-gray-400">
                            🕐 {{ \Carbon\Carbon::parse($t->waktu_mulai)->format('H:i') }}
                            @if($t->waktu_selesai)
                            → {{ \Carbon\Carbon::parse($t->waktu_selesai)->format('H:i') }}
                            @if($t->durasi) ({{ $t->durasi }}) @endif
                            @endif
                        </span>
                        @endif
                    </div>

                    @if($t->catatan_penyelesaian)
                    <div class="mt-2 p-2 bg-gray-100 rounded-lg">
                        <p class="text-xs text-gray-600 italic">"{{ $t->catatan_penyelesaian }}"</p>
                    </div>
                    @endif
                </div>

                {{-- Status Icon --}}
                <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center
                    {{ $t->status === 'selesai' ? 'bg-green-100' : ($t->status === 'dalam_proses' ? 'bg-amber-100' : ($t->status === 'dilewati' ? 'bg-red-100' : 'bg-gray-100')) }}">
                    @if($t->status === 'selesai')
                        <svg class="w-7 h-7 text-[#2E7D32]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    @elseif($t->status === 'dalam_proses')
                        <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @elseif($t->status === 'dilewati')
                        <svg class="w-7 h-7 text-[#B14B6F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    @else
                        <div class="w-7 h-7 rounded-full border-2 border-gray-300"></div>
                    @endif
                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            @if($t->status === 'belum')
            <div class="flex gap-2 mt-3">
                <button onclick="updateStatusMobile({{ $t->id }}, 'dalam_proses')"
                        class="btn-action flex-1 bg-amber-50 border-2 border-amber-200 text-amber-700">
                    Mulai Kerjakan
                </button>
                <button onclick="updateStatusMobile({{ $t->id }}, 'dilewati')"
                        class="btn-action px-4 bg-red-50 border-2 border-red-200 text-red-600">
                    Lewati
                </button>
            </div>

            @elseif($t->status === 'dalam_proses')
            <div class="flex gap-2 mt-3">
                <button onclick="toggleCatatan({{ $t->id }})"
                        class="btn-action flex-1 bg-[#2E7D32] text-white text-base">
                    Tandai Selesai
                </button>
                <button onclick="updateStatusMobile({{ $t->id }}, 'dilewati')"
                        class="btn-action px-4 bg-red-50 border-2 border-red-200 text-red-600">
                    Lewati
                </button>
            </div>

            @elseif($t->status === 'selesai')
            <div class="mt-3 py-2 px-3 bg-green-100 rounded-lg text-center">
                <p class="text-xs font-bold text-green-700">Tugas Selesai Dikerjakan</p>
            </div>

            @elseif($t->status === 'dilewati')
            <div class="mt-3">
                <button onclick="updateStatusMobile({{ $t->id }}, 'belum')"
                        class="btn-action w-full bg-gray-100 border border-gray-300 text-gray-600 text-sm">
                    Batalkan Lewati
                </button>
            </div>
            @endif
        </div>

        {{-- CATATAN PANEL (inline, hidden by default) --}}
        @if($t->status === 'dalam_proses')
        <div id="catatan-panel-{{ $t->id }}" class="hidden border-t border-gray-100 bg-gray-50 p-4">
            <p class="text-sm font-bold text-gray-700 mb-2">Tambahkan catatan (opsional):</p>
            <textarea id="catatan-text-{{ $t->id }}" rows="2"
                      placeholder="Kondisi selesai, ada temuan, dll..."
                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                             focus:ring-2 focus:ring-[#2E7D32] outline-none resize-none mb-3"></textarea>
            <div class="flex gap-2">
                <button onclick="toggleCatatan({{ $t->id }})"
                        class="btn-action flex-1 bg-gray-200 text-gray-700">
                    Batal
                </button>
                <button onclick="selesaikanDenganCatatan({{ $t->id }})"
                        class="btn-action flex-1 bg-[#2E7D32] text-white">
                    Konfirmasi Selesai
                </button>
            </div>
        </div>
        @endif
    </div>
    @empty
    <div class="text-center py-16">
        <p class="text-4xl mb-4">🎉</p>
        <p class="font-bold text-gray-700 text-lg">Tidak ada tugas hari ini!</p>
        <p class="text-gray-400 text-sm mt-1">Semua tugas sudah selesai atau belum ada yang di-assign.</p>
    </div>
    @endforelse

    <div class="text-center pt-4 pb-2">
        <p class="text-xs text-gray-400">
            Gumolong Farm · Daily Task M-03 · {{ now()->format('H:i') }} WIB
        </p>
    </div>
</div>

{{-- LOADING OVERLAY --}}
<div id="loading-overlay"
     class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-8 flex flex-col items-center gap-4 shadow-2xl">
        <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-[#2E7D32]"></div>
        <p class="font-bold text-gray-700">Memperbarui status...</p>
    </div>
</div>

</div>{{-- /x-data --}}

<script>
function toggleCatatan(id) {
    const panel = document.getElementById('catatan-panel-' + id);
    panel.classList.toggle('hidden');
}

async function selesaikanDenganCatatan(id) {
    const catatan = document.getElementById('catatan-text-' + id).value;
    await updateStatusMobile(id, 'selesai', catatan);
}

async function updateStatusMobile(id, status, catatan = '') {
    const overlay = document.getElementById('loading-overlay');
    overlay.classList.remove('hidden');

    try {
        const res = await fetch('/tugas-harian/' + id + '/status', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status, catatan_penyelesaian: catatan })
        });

        if (res.ok) {
            window.location.reload();
        } else {
            overlay.classList.add('hidden');
            alert('Gagal update. Coba lagi.');
        }
    } catch (err) {
        overlay.classList.add('hidden');
        alert('Gagal update. Cek koneksi internet.');
    }
}
</script>
</body>
</html>
