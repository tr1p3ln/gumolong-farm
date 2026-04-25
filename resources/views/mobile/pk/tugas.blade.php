@extends('mobile.layout')
@section('title', 'Tugas Hari Ini — Pengurus Kandang')

@section('content')

<!-- TopAppBar -->
<header class="bg-[#FAFAF7] sticky top-0 z-50 border-b border-gray-200 flex items-center justify-between px-4 h-14">
  <div class="flex items-center gap-3">
    <a href="{{ route('pk.dashboard') }}" class="text-[#1F2937]">
      <span class="material-symbols-outlined text-2xl">arrow_back</span>
    </a>
    <h1 class="text-base font-semibold text-[#2E7D32]">Tugas Hari Ini</h1>
  </div>
  <span class="text-[11px] text-[#6B7280]">{{ \Carbon\Carbon::parse($tanggal)->format('d M') }}</span>
</header>

<!-- Progress Banner -->
<div class="bg-[#2E7D32] px-5 pt-4 pb-6">
  <div class="flex items-center justify-between mb-3">
    <div>
      <p class="text-white/70 text-xs">Progress Hari Ini</p>
      <p class="text-white text-2xl font-black">{{ $summary['persen'] }}%</p>
    </div>
    <div class="flex gap-4 text-center">
      <div>
        <p class="text-white font-bold text-lg">{{ $summary['selesai'] }}</p>
        <p class="text-white/60 text-[10px]">Selesai</p>
      </div>
      <div>
        <p class="text-white font-bold text-lg">{{ $summary['belum'] }}</p>
        <p class="text-white/60 text-[10px]">Sisa</p>
      </div>
      <div>
        <p class="text-white font-bold text-lg">{{ $summary['total'] }}</p>
        <p class="text-white/60 text-[10px]">Total</p>
      </div>
    </div>
  </div>
  <div class="w-full bg-white/20 rounded-full h-2">
    <div class="bg-white h-2 rounded-full" style="width:{{ $summary['persen'] }}%"></div>
  </div>
</div>

<!-- Task List -->
<div class="px-4 py-4 space-y-3 -mt-2">

  @forelse($tugas as $t)
  @php
    $borderColor = match($t->status) {
      'selesai'      => 'border-l-[#2E7D32]',
      'dalam_proses' => 'border-l-amber-500',
      'dilewati'     => 'border-l-[#B14B6F]',
      default        => 'border-l-gray-300',
    };
    $bgColor = match($t->status) {
      'selesai'  => 'bg-green-50',
      'dilewati' => 'bg-gray-50 opacity-70',
      default    => 'bg-white',
    };
    $badgeClass = match($t->prioritas ?? 'sedang') {
      'tinggi' => 'bg-red-100 text-red-700',
      'rendah' => 'bg-green-100 text-green-700',
      default  => 'bg-amber-100 text-amber-700',
    };
  @endphp
  <div class="{{ $bgColor }} border border-gray-200 border-l-4 {{ $borderColor }} rounded-r-xl shadow-sm overflow-hidden">
    <div class="p-4">
      <div class="flex items-start justify-between gap-3">
        <div class="flex-1 min-w-0">
          <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full mb-1.5 {{ $badgeClass }}">
            {{ ucfirst($t->prioritas ?? 'sedang') }}
          </span>
          <h3 class="text-sm font-bold text-[#1F2937] {{ $t->status === 'selesai' ? 'line-through text-gray-400' : '' }}">
            {{ $t->judul }}
          </h3>
          @if($t->deskripsi)
          <p class="text-xs text-[#6B7280] mt-0.5">{{ $t->deskripsi }}</p>
          @endif
          @if($t->kandang)
          <p class="text-xs font-bold text-[#2E7D32] mt-1">📍 {{ $t->kandang->nama_kandang }}</p>
          @endif
        </div>
        <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center
          {{ $t->status === 'selesai' ? 'bg-green-100' : ($t->status === 'dalam_proses' ? 'bg-amber-100' : ($t->status === 'dilewati' ? 'bg-red-100' : 'bg-gray-100')) }}">
          @if($t->status === 'selesai')
            <span class="material-symbols-outlined text-[#2E7D32] text-xl" style="font-variation-settings:'FILL' 1">check_circle</span>
          @elseif($t->status === 'dalam_proses')
            <span class="material-symbols-outlined text-amber-600 text-xl">pending</span>
          @elseif($t->status === 'dilewati')
            <span class="material-symbols-outlined text-[#B14B6F] text-xl">cancel</span>
          @else
            <div class="w-6 h-6 rounded-full border-2 border-gray-300"></div>
          @endif
        </div>
      </div>

      <!-- Action Buttons -->
      @if($t->status === 'belum')
      <div class="flex gap-2 mt-3">
        <button onclick="updateStatus({{ $t->id }}, 'dalam_proses')"
          class="flex-1 py-2.5 bg-amber-50 border-2 border-amber-200 text-amber-700 text-sm font-bold rounded-xl active:scale-95 transition-transform">
          Mulai
        </button>
        <button onclick="updateStatus({{ $t->id }}, 'dilewati')"
          class="px-4 py-2.5 bg-red-50 border-2 border-red-200 text-red-600 text-sm font-bold rounded-xl active:scale-95 transition-transform">
          Lewati
        </button>
      </div>
      @elseif($t->status === 'dalam_proses')
      <div class="flex gap-2 mt-3">
        <button onclick="updateStatus({{ $t->id }}, 'selesai')"
          class="flex-1 py-2.5 bg-[#2E7D32] text-white text-sm font-bold rounded-xl active:scale-95 transition-transform">
          Tandai Selesai ✓
        </button>
        <button onclick="updateStatus({{ $t->id }}, 'dilewati')"
          class="px-4 py-2.5 bg-red-50 border-2 border-red-200 text-red-600 text-sm font-bold rounded-xl active:scale-95 transition-transform">
          Lewati
        </button>
      </div>
      @elseif($t->status === 'dilewati')
      <button onclick="updateStatus({{ $t->id }}, 'belum')"
        class="w-full mt-3 py-2.5 bg-gray-100 border border-gray-300 text-gray-600 text-sm font-bold rounded-xl active:scale-95 transition-transform">
        Batalkan Lewati
      </button>
      @endif
    </div>
  </div>
  @empty
  <div class="text-center py-16">
    <p class="text-4xl mb-4">🎉</p>
    <p class="font-bold text-gray-700 text-lg">Tidak ada tugas hari ini!</p>
    <p class="text-gray-400 text-sm mt-1">Semua tugas sudah selesai atau belum ada yang di-assign.</p>
  </div>
  @endforelse

</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center">
  <div class="bg-white rounded-2xl p-8 flex flex-col items-center gap-4 shadow-2xl">
    <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-[#2E7D32]"></div>
    <p class="font-bold text-gray-700">Memperbarui...</p>
  </div>
</div>

<!-- Bottom Nav -->
<nav class="fixed bottom-0 left-0 w-full max-w-[390px] bg-white border-t border-gray-200 flex justify-around items-center h-16 z-50 shadow-[0_-2px_8px_rgba(0,0,0,0.06)] left-1/2 -translate-x-1/2">
  <a href="{{ route('pk.dashboard') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">dashboard</span>
    <span class="text-[10px] font-medium">Beranda</span>
  </a>
  <a href="{{ route('pk.tugas') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 bg-[#2E7D32] text-white py-2 mx-1 rounded-xl">
    <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">assignment_turned_in</span>
    <span class="text-[10px] font-bold">Tugas</span>
  </a>
  <a href="{{ route('pk.timbangan') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">scale</span>
    <span class="text-[10px] font-medium">Timbangan</span>
  </a>
  <a href="{{ route('pk.kesehatan') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">medical_services</span>
    <span class="text-[10px] font-medium">Kesehatan</span>
  </a>
  <a href="{{ route('pk.kelahiran') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">child_care</span>
    <span class="text-[10px] font-medium">Kelahiran</span>
  </a>
</nav>

@endsection

@push('scripts')
<script>
async function updateStatus(id, status) {
  document.getElementById('loadingOverlay').classList.remove('hidden');
  try {
    const res = await fetch('/tugas-harian/' + id + '/status', {
      method: 'PATCH',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ status })
    });
    if (res.ok) { window.location.reload(); }
    else { document.getElementById('loadingOverlay').classList.add('hidden'); alert('Gagal update. Coba lagi.'); }
  } catch (e) {
    document.getElementById('loadingOverlay').classList.add('hidden');
    alert('Gagal update. Cek koneksi internet.');
  }
}
</script>
@endpush
