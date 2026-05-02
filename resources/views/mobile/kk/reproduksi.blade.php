@extends('mobile.layout')
@section('title', 'Reproduksi — Kepala Kandang')

@push('styles')
<style>
  .tab-active { border-bottom: 2px solid #2E7D32; color: #2E7D32; font-weight: 700; }
</style>
@endpush

@section('content')

<!-- TopAppBar -->
<header class="bg-[#FAFAF7] sticky top-0 z-50 border-b border-gray-200 flex items-center justify-between px-4 h-14">
  <div class="flex items-center gap-3">
    <a href="{{ route('kk.dashboard') }}" class="text-[#1F2937]">
      <span class="material-symbols-outlined text-2xl">arrow_back</span>
    </a>
    <h1 class="text-base font-semibold text-[#2E7D32]">Reproduksi</h1>
  </div>
  <span class="bg-green-100 text-[#2E7D32] px-2.5 py-0.5 rounded-full text-[10px] font-bold">Kepala Kandang</span>
</header>

<!-- Tab Nav -->
<nav class="flex w-full bg-white border-b border-gray-200 sticky top-14 z-40">
  <button class="flex-1 py-3.5 text-xs tab-active" onclick="switchTab('kelahiran')">Kelahiran</button>
  <button class="flex-1 py-3.5 text-xs font-medium text-[#6B7280]" onclick="switchTab('perkawinan')">Perkawinan</button>
  <button class="flex-1 py-3.5 text-xs font-medium text-[#6B7280]" onclick="switchTab('kebuntingan')">Kebuntingan</button>
</nav>

@if(session('success'))
<div class="mx-4 mt-3 bg-green-50 border border-green-200 rounded-xl p-3 text-sm text-green-800 font-medium">
  {{ session('success') }}
</div>
@endif

<!-- Tab: Kelahiran -->
<div id="tab-kelahiran" class="px-4 py-4 space-y-4">

  @forelse($kelahiran as $k)
  <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
    <div class="flex justify-between items-start mb-2">
      <div>
        <h4 class="font-bold text-[#2E7D32] text-sm">{{ $k->indukan_tag }}{{ $k->nama ? ' · ' . $k->nama : '' }}</h4>
        <p class="text-xs text-[#6B7280] mt-0.5">{{ \Carbon\Carbon::parse($k->tanggal_kelahiran)->format('d M Y') }}</p>
      </div>
      <span class="text-[10px] font-bold text-[#2E7D32] bg-green-50 px-2 py-0.5 rounded-full">Tercatat</span>
    </div>
    <div class="grid grid-cols-2 gap-3 py-3 border-y border-gray-100">
      <div>
        <p class="text-[10px] text-[#6B7280] uppercase font-bold">Anak Hidup</p>
        <p class="text-sm font-semibold text-[#1F2937] mt-0.5">{{ $k->jml_anak_hidup }} ekor</p>
      </div>
      <div>
        <p class="text-[10px] text-[#6B7280] uppercase font-bold">Lahir Mati</p>
        <p class="text-sm font-semibold text-[#1F2937] mt-0.5">{{ $k->jml_anak_mati }} ekor</p>
      </div>
    </div>
    @if($k->catatan)
    <p class="text-xs text-[#6B7280] italic mt-2">"{{ $k->catatan }}"</p>
    @endif
  </div>
  @empty
  <div class="text-center py-10 text-[#6B7280]">
    <span class="material-symbols-outlined text-4xl text-gray-300">child_care</span>
    <p class="text-sm mt-2">Belum ada data kelahiran</p>
  </div>
  @endforelse

</div>

<!-- Tab: Perkawinan -->
<div id="tab-perkawinan" class="px-4 py-4 space-y-4 hidden">

  @forelse($perkawinan as $p)
  @php
    $statusColor = match($p->status) {
      'bunting'             => 'text-[#2E7D32] bg-green-50',
      'lahir'               => 'text-blue-700 bg-blue-50',
      'tidak_bunting'       => 'text-[#B14B6F] bg-red-50',
      'gagal'               => 'text-gray-600 bg-gray-100',
      default               => 'text-amber-700 bg-amber-50',
    };
  @endphp
  <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
    <div class="flex justify-between items-start mb-2">
      <p class="text-sm font-bold text-[#1F2937]">
        {{ $p->pejantan_id }}{{ $p->nama_jantan ? ' ' . $p->nama_jantan : '' }} × {{ $p->indukan_id }}{{ $p->nama_induk ? ' ' . $p->nama_induk : '' }}
      </p>
      <span class="text-[10px] font-bold {{ $statusColor }} px-2 py-0.5 rounded-full capitalize">{{ str_replace('_', ' ', $p->status) }}</span>
    </div>
    <p class="text-xs text-[#6B7280]">{{ \Carbon\Carbon::parse($p->tanggal_perkawinan)->format('d M Y') }} · {{ ucfirst($p->metode) }}</p>
    @if($p->estimasi_lahir)
    <p class="text-xs text-[#6B7280] mt-0.5">HPL: {{ \Carbon\Carbon::parse($p->estimasi_lahir)->format('d M Y') }}</p>
    @endif
  </div>
  @empty
  <div class="text-center py-10 text-[#6B7280]">
    <span class="material-symbols-outlined text-4xl text-gray-300">family_history</span>
    <p class="text-sm mt-2">Belum ada data perkawinan</p>
  </div>
  @endforelse

</div>

<!-- Tab: Kebuntingan -->
<div id="tab-kebuntingan" class="px-4 py-4 space-y-4 hidden">

  <!-- Info -->
  <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 flex gap-2.5">
    <span class="material-symbols-outlined text-blue-500 text-lg mt-0.5">info</span>
    <p class="text-xs text-blue-700 leading-relaxed">Status kebuntingan dikonfirmasi setelah 40–50 hari perkawinan via USG atau observasi fisik.</p>
  </div>

  @forelse($kebuntingan as $kb)
  <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
    <div class="p-4 flex justify-between items-center bg-gray-50/50">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center font-bold text-[#2E7D32] text-xs shadow-sm border border-gray-100">
          {{ $kb->indukan_tag }}
        </div>
        <div>
          <h4 class="text-sm font-bold text-[#1F2937]">{{ $kb->indukan_tag }}{{ $kb->nama ? ' · ' . $kb->nama : '' }}</h4>
          <p class="text-[10px] text-[#6B7280] font-bold uppercase">Kawin: {{ \Carbon\Carbon::parse($kb->tanggal_perkawinan)->format('d M') }} · {{ $kb->hari_sejak_kawin }} Hari</p>
        </div>
      </div>
      <span class="text-amber-600 text-xs font-bold bg-amber-50 px-2 py-1 rounded-lg">Menunggu Konfirmasi</span>
    </div>
    <div class="p-3 grid grid-cols-2 gap-3">
      <form method="POST" action="{{ route('kk.reproduksi.konfirmasi', $kb->kawin_id) }}">
        @csrf
        <input type="hidden" name="status" value="tidak_bunting"/>
        <button type="submit"
          class="w-full py-2.5 rounded-xl border border-[#B14B6F] text-[#B14B6F] text-sm font-bold flex items-center justify-center gap-1.5">
          <span class="material-symbols-outlined text-base">close</span>Tidak Bunting
        </button>
      </form>
      <form method="POST" action="{{ route('kk.reproduksi.konfirmasi', $kb->kawin_id) }}">
        @csrf
        <input type="hidden" name="status" value="bunting"/>
        <button type="submit"
          class="w-full py-2.5 rounded-xl bg-[#2E7D32] text-white text-sm font-bold flex items-center justify-center gap-1.5 shadow-sm">
          <span class="material-symbols-outlined text-base">check_circle</span>Konfirmasi Bunting
        </button>
      </form>
    </div>
  </div>
  @empty
  <div class="text-center py-10 text-[#6B7280]">
    <span class="material-symbols-outlined text-4xl text-gray-300">pregnant_woman</span>
    <p class="text-sm mt-2">Tidak ada perkawinan menunggu konfirmasi</p>
  </div>
  @endforelse

</div>

<!-- Bottom Nav -->
<nav class="fixed bottom-0 left-0 w-full max-w-[390px] bg-white border-t border-gray-200 flex justify-around items-center h-16 z-50 shadow-[0_-2px_8px_rgba(0,0,0,0.06)] left-1/2 -translate-x-1/2">
  <a href="{{ route('kk.dashboard') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">dashboard</span>
    <span class="text-[10px] font-medium">Beranda</span>
  </a>
  <a href="{{ route('kk.monitor-tugas') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">assignment_turned_in</span>
    <span class="text-[10px] font-medium">Monitor</span>
  </a>
  <a href="{{ route('kk.kesehatan') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">medical_services</span>
    <span class="text-[10px] font-medium">Kesehatan</span>
  </a>
  <a href="{{ route('kk.reproduksi') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 bg-[#2E7D32] text-white py-2 mx-1 rounded-xl">
    <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">family_history</span>
    <span class="text-[10px] font-bold">Reproduksi</span>
  </a>
  <a href="{{ route('kk.validasi-timbangan') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">fact_check</span>
    <span class="text-[10px] font-medium">Validasi</span>
  </a>
</nav>

@endsection

@push('scripts')
<script>
function switchTab(tab) {
  ['kelahiran','perkawinan','kebuntingan'].forEach(t => {
    document.getElementById('tab-' + t).classList.toggle('hidden', t !== tab);
  });
  document.querySelectorAll('nav.flex.w-full button').forEach((b, i) => {
    const tabs = ['kelahiran','perkawinan','kebuntingan'];
    const active = tabs[i] === tab;
    b.className = active
      ? 'flex-1 py-3.5 text-xs tab-active'
      : 'flex-1 py-3.5 text-xs font-medium text-[#6B7280]';
  });
}
</script>
@endpush
