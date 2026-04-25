@extends('mobile.layout')
@section('title', 'Validasi Timbangan — Kepala Kandang')

@section('content')

<!-- TopAppBar -->
<header class="bg-[#FAFAF7] sticky top-0 z-50 border-b border-gray-200 flex items-center justify-between px-4 h-14">
  <div class="flex items-center gap-3">
    <a href="{{ route('kk.dashboard') }}" class="text-[#1F2937]">
      <span class="material-symbols-outlined text-2xl">arrow_back</span>
    </a>
    <h1 class="text-base font-semibold text-[#2E7D32]">Validasi Timbangan</h1>
  </div>
</header>

@if(session('success'))
<div class="mx-4 mt-3 bg-green-50 border border-green-200 rounded-xl p-3 text-sm text-green-800 font-medium">
  {{ session('success') }}
</div>
@endif

<main class="px-4 pt-4 space-y-4">

  <!-- Summary Banner -->
  <section class="bg-green-50 rounded-xl p-4 border border-green-200 flex flex-col gap-3">
    <div class="flex items-center gap-2">
      <span class="material-symbols-outlined text-[#2E7D32] text-xl">analytics</span>
      <p class="text-sm font-medium text-[#1F2937]">{{ $summary['total'] }} data timbangan hari ini</p>
    </div>
    <div class="flex gap-2">
      <span class="px-3 py-1 bg-white text-[#2E7D32] rounded-full text-[10px] font-bold border border-green-200 flex items-center gap-1">
        <span class="w-2 h-2 rounded-full bg-[#2E7D32]"></span>{{ $summary['valid'] }} Tervalidasi
      </span>
      <span class="px-3 py-1 bg-white text-amber-700 rounded-full text-[10px] font-bold border border-amber-200 flex items-center gap-1">
        <span class="w-2 h-2 rounded-full bg-amber-500"></span>{{ $summary['pending'] }} Pending
      </span>
    </div>
  </section>

  <!-- Pending -->
  @if($pending->isNotEmpty())
  <div class="flex items-center justify-between">
    <h2 class="text-sm font-bold text-[#1F2937]">Menunggu Validasi</h2>
    <span class="text-[10px] text-[#6B7280]">{{ $pending->count() }} item</span>
  </div>

  @foreach($pending as $t)
  @php
    $adg = null;
    if ($t->berat_sebelumnya) {
      $selisih = $t->berat_kg - $t->berat_sebelumnya;
      $hari    = 30; // approx
      $adg     = round(($selisih / $hari) * 1000); // g/hari
    }
  @endphp
  <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm space-y-4">
    <div class="flex justify-between items-start">
      <div class="flex gap-3">
        <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center">
          <span class="material-symbols-outlined text-gray-400 text-2xl">pets</span>
        </div>
        <div>
          <h3 class="text-sm font-bold text-[#1F2937]">{{ $t->ear_tag_id }}{{ $t->nama ? ' · ' . $t->nama : '' }}</h3>
          <p class="text-[11px] text-[#6B7280] mt-0.5">{{ \Carbon\Carbon::parse($t->tanggal_timbang)->format('d M Y') }}</p>
        </div>
      </div>
      <span class="px-2 py-0.5 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-bold border border-amber-100">Pending</span>
    </div>

    <!-- Weight Comparison -->
    <div class="grid grid-cols-2 gap-4 py-3 border-y border-gray-50">
      <div>
        <p class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider">Berat Sekarang</p>
        <p class="text-2xl font-bold text-[#2E7D32] mt-1">{{ number_format($t->berat_kg, 1) }}<span class="text-sm font-normal text-[#6B7280]"> kg</span></p>
      </div>
      <div class="border-l border-gray-100 pl-4">
        <p class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider">Sebelumnya</p>
        @if($t->berat_sebelumnya)
        <p class="text-lg font-semibold text-[#6B7280] mt-1">{{ number_format($t->berat_sebelumnya, 1) }}<span class="text-sm font-normal"> kg</span></p>
        @else
        <p class="text-sm text-[#6B7280] mt-1 italic">Tidak ada data</p>
        @endif
      </div>
    </div>

    <!-- ADG -->
    @if($adg !== null)
    <div class="flex items-center gap-2">
      @if($adg >= 50)
      <span class="material-symbols-outlined text-[#2E7D32] text-lg">trending_up</span>
      <span class="px-2 py-0.5 bg-green-50 text-[#2E7D32] rounded-lg text-[10px] font-bold border border-green-100">+{{ $adg }} g/hari (Normal)</span>
      @else
      <span class="material-symbols-outlined text-amber-500 text-lg">warning</span>
      <span class="px-2 py-0.5 bg-amber-50 text-amber-700 rounded-lg text-[10px] font-bold border border-amber-100">{{ $adg }} g/hari (RENDAH)</span>
      @endif
    </div>
    @endif

    @if($t->catatan)
    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
      <p class="text-xs text-[#6B7280] italic">"{{ $t->catatan }}"</p>
    </div>
    @endif

    <!-- Actions -->
    <div class="flex gap-3">
      <form method="POST" action="{{ route('kk.validasi-timbangan.process', $t->timbangan_id) }}" class="flex-1">
        @csrf
        <input type="hidden" name="action" value="ditolak"/>
        <button type="submit" class="w-full py-3 border border-[#B14B6F] text-[#B14B6F] text-sm font-bold rounded-xl active:scale-95 transition-transform">
          Tolak
        </button>
      </form>
      <form method="POST" action="{{ route('kk.validasi-timbangan.process', $t->timbangan_id) }}" class="flex-1">
        @csrf
        <input type="hidden" name="action" value="valid"/>
        <button type="submit" class="w-full py-3 bg-[#2E7D32] text-white text-sm font-bold rounded-xl active:scale-95 transition-transform shadow-md shadow-green-900/10">
          Validasi ✓
        </button>
      </form>
    </div>
  </div>
  @endforeach
  @else
  <div class="text-center py-10 text-[#6B7280]">
    <span class="material-symbols-outlined text-4xl text-gray-300">fact_check</span>
    <p class="text-sm mt-2 font-medium">Tidak ada data timbangan yang perlu divalidasi</p>
  </div>
  @endif

  <!-- Validated -->
  @if($tervalidasi->isNotEmpty())
  <div>
    <button onclick="document.getElementById('validatedList').classList.toggle('hidden')"
      class="w-full flex items-center justify-between p-4 bg-gray-100 rounded-xl">
      <span class="text-sm font-semibold text-[#6B7280]">Sudah Divalidasi ({{ $tervalidasi->count() }})</span>
      <span class="material-symbols-outlined text-gray-400 text-xl">expand_more</span>
    </button>
    <div id="validatedList" class="mt-3 space-y-2 hidden">
      @foreach($tervalidasi as $tv)
      <div class="flex items-center justify-between p-3 bg-white border border-gray-100 rounded-xl">
        <div class="flex items-center gap-3">
          <span class="material-symbols-outlined text-[#2E7D32] text-lg" style="font-variation-settings:'FILL' 1">check_circle</span>
          <span class="text-sm font-semibold text-[#1F2937]">{{ $tv->ear_tag_id }}</span>
        </div>
        <div class="flex gap-4 text-[11px] text-[#6B7280]">
          <span>{{ number_format($tv->berat_kg, 1) }} kg</span>
          <span>{{ \Carbon\Carbon::parse($tv->tanggal_timbang)->format('d M') }}</span>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

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
  <a href="{{ route('kk.kesehatan') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">medical_services</span>
    <span class="text-[10px] font-medium">Kesehatan</span>
  </a>
  <a href="{{ route('kk.reproduksi') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">family_history</span>
    <span class="text-[10px] font-medium">Reproduksi</span>
  </a>
  <a href="{{ route('kk.validasi-timbangan') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 bg-[#2E7D32] text-white py-2 mx-1 rounded-xl">
    <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">fact_check</span>
    <span class="text-[10px] font-bold">Validasi</span>
  </a>
</nav>

@endsection
