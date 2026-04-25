@extends('mobile.layout')
@section('title', 'Laporan Kesehatan — Kepala Kandang')

@section('content')

<!-- TopAppBar -->
<header class="bg-white sticky top-0 z-50 border-b border-gray-200 flex items-center justify-between px-4 h-14">
  <div class="flex items-center gap-3">
    <a href="{{ route('kk.dashboard') }}" class="text-[#1F2937]">
      <span class="material-symbols-outlined text-2xl">arrow_back</span>
    </a>
    <h1 class="text-base font-semibold text-[#2E7D32]">Laporan Kesehatan</h1>
  </div>
  <span class="bg-green-100 text-[#2E7D32] px-2.5 py-0.5 rounded-full text-[10px] font-bold">Kepala Kandang</span>
</header>

@if(session('success'))
<div class="mx-4 mt-3 bg-green-50 border border-green-200 rounded-xl p-3 text-sm text-green-800 font-medium">
  {{ session('success') }}
</div>
@endif

<main class="px-4 pt-4 space-y-4">

  <!-- Summary -->
  @php
    $parah   = $laporan->filter(fn($l) => str_contains($l->gejala, '[PARAH]'))->count();
    $sedang  = $laporan->filter(fn($l) => str_contains($l->gejala, '[SEDANG]'))->count();
    $ringan  = $laporan->filter(fn($l) => str_contains($l->gejala, '[RINGAN]'))->count();
  @endphp
  <div class="flex gap-2 flex-wrap">
    @if($parah > 0)
    <div class="inline-flex items-center gap-1 bg-red-50 px-2.5 py-1 rounded-lg border border-red-100">
      <span class="text-[10px]">🔴</span>
      <span class="text-[10px] font-bold text-red-800">{{ $parah }} Parah</span>
    </div>
    @endif
    @if($sedang > 0)
    <div class="inline-flex items-center gap-1 bg-orange-50 px-2.5 py-1 rounded-lg border border-orange-100">
      <span class="text-[10px]">🟠</span>
      <span class="text-[10px] font-bold text-orange-800">{{ $sedang }} Sedang</span>
    </div>
    @endif
    @if($ringan > 0)
    <div class="inline-flex items-center gap-1 bg-yellow-50 px-2.5 py-1 rounded-lg border border-yellow-100">
      <span class="text-[10px]">🟡</span>
      <span class="text-[10px] font-bold text-yellow-800">{{ $ringan }} Ringan</span>
    </div>
    @endif
    @if($laporan->isEmpty())
    <div class="inline-flex items-center gap-1 bg-green-50 px-2.5 py-1 rounded-lg border border-green-100">
      <span class="text-[10px]">✅</span>
      <span class="text-[10px] font-bold text-green-800">Semua Sehat</span>
    </div>
    @endif
  </div>

  <!-- Active Reports -->
  <div class="flex flex-col gap-3">

    @forelse($laporan as $lap)
    @php
      $isParah  = str_contains($lap->gejala, '[PARAH]');
      $isSedang = str_contains($lap->gejala, '[SEDANG]');
      $borderColor = $isParah ? 'bg-red-600' : ($isSedang ? 'bg-orange-500' : 'bg-yellow-500');
      $badgeClass  = $isParah ? 'bg-red-100 text-red-800' : ($isSedang ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800');
      $badgeLabel  = $isParah ? '🔴 PARAH' : ($isSedang ? '🟠 SEDANG' : '🟡 RINGAN');
      $gejalaBersih = preg_replace('/\[(PARAH|SEDANG|RINGAN)\]\s*/', '', $lap->gejala);
    @endphp
    <article class="bg-white rounded-xl border border-gray-200 overflow-hidden relative shadow-sm">
      <div class="absolute left-0 top-0 bottom-0 w-1 {{ $borderColor }}"></div>
      <div class="p-4 pl-5">
        <div class="flex justify-between items-start mb-3">
          <div>
            <h3 class="text-sm font-bold text-[#1F2937]">{{ $lap->ear_tag_id }}{{ $lap->nama ? ' · ' . $lap->nama : '' }}</h3>
            <p class="text-xs text-[#6B7280] mt-0.5">{{ \Carbon\Carbon::parse($lap->tanggal_sakit)->format('d M Y') }}</p>
          </div>
          <div class="{{ $badgeClass }} px-2 py-0.5 rounded text-[10px] font-bold">{{ $badgeLabel }}</div>
        </div>
        <p class="text-xs text-[#6B7280] italic mb-4">"{{ Str::limit($gejalaBersih, 100) }}"</p>
        <div class="flex flex-col gap-2">
          <form method="POST" action="{{ route('kk.kesehatan.konfirmasi', $lap->rekam_id) }}">
            @csrf
            <input type="hidden" name="action" value="sembuh"/>
            <button type="submit"
              class="w-full bg-[#2E7D32] text-white text-sm font-bold py-2.5 rounded-xl flex items-center justify-center gap-2 active:scale-95 transition-transform">
              <span class="material-symbols-outlined text-lg">check</span>
              Konfirmasi Sudah Ditangani
            </button>
          </form>
          <form method="POST" action="{{ route('kk.kesehatan.konfirmasi', $lap->rekam_id) }}">
            @csrf
            <input type="hidden" name="action" value="dalam_perawatan"/>
            <button type="submit"
              class="w-full border border-[#2E7D32] text-[#2E7D32] text-sm font-medium py-2.5 rounded-xl active:opacity-70">
              Tandai Dalam Perawatan
            </button>
          </form>
        </div>
      </div>
    </article>
    @empty
    <div class="text-center py-10 text-[#6B7280]">
      <span class="material-symbols-outlined text-4xl text-gray-300">health_and_safety</span>
      <p class="text-sm mt-2 font-medium">Tidak ada laporan kesehatan aktif</p>
    </div>
    @endforelse

    <!-- Ditangani -->
    @if($ditangani->isNotEmpty())
    <button onclick="document.getElementById('ditanganiList').classList.toggle('hidden')"
      class="w-full flex items-center justify-between p-4 bg-gray-100 rounded-xl">
      <span class="text-sm font-semibold text-[#6B7280]">Sudah Ditangani ({{ $ditangani->count() }})</span>
      <span class="material-symbols-outlined text-gray-400 text-xl">expand_more</span>
    </button>
    <div id="ditanganiList" class="hidden space-y-2">
      @foreach($ditangani as $d)
      <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden relative opacity-70">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gray-400"></div>
        <div class="p-4 pl-5 flex justify-between items-center">
          <div>
            <h3 class="text-sm font-semibold text-[#6B7280]">{{ $d->ear_tag_id }}{{ $d->nama ? ' · ' . $d->nama : '' }}</h3>
            <span class="text-xs text-[#6B7280] italic">Sembuh {{ $d->tanggal_sembuh ? \Carbon\Carbon::parse($d->tanggal_sembuh)->format('d M') : '' }}</span>
          </div>
          <div class="bg-gray-200 text-gray-600 px-2 py-0.5 rounded text-[10px] font-bold">✅ DITANGANI</div>
        </div>
      </div>
      @endforeach
    </div>
    @endif

  </div>

</main>

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
  <a href="{{ route('kk.kesehatan') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 bg-[#2E7D32] text-white py-2 mx-1 rounded-xl">
    <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">medical_services</span>
    <span class="text-[10px] font-bold">Kesehatan</span>
  </a>
  <a href="{{ route('kk.reproduksi') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">family_history</span>
    <span class="text-[10px] font-medium">Reproduksi</span>
  </a>
  <a href="{{ route('kk.validasi-timbangan') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">fact_check</span>
    <span class="text-[10px] font-medium">Validasi</span>
  </a>
</nav>

@endsection
