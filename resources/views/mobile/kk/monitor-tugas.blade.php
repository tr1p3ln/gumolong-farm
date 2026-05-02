@extends('mobile.layout')
@section('title', 'Monitor Tugas Tim — Kepala Kandang')

@section('content')

<!-- TopAppBar -->
<header class="bg-[#FAFAF7] sticky top-0 z-50 border-b border-gray-200 flex items-center justify-between px-4 h-14">
  <div class="flex items-center gap-3">
    <a href="{{ route('kk.dashboard') }}" class="text-[#1F2937]">
      <span class="material-symbols-outlined text-2xl">arrow_back</span>
    </a>
    <h1 class="text-base font-semibold text-[#2E7D32]">Monitor Tugas Tim</h1>
  </div>
  <div class="flex items-center gap-1.5 border border-[#2E7D32] rounded-full px-2.5 py-1">
    <span class="material-symbols-outlined text-[#2E7D32] text-sm">calendar_today</span>
    <span class="text-[11px] font-bold text-[#2E7D32]">{{ \Carbon\Carbon::parse($tanggal)->format('d M') }}</span>
  </div>
</header>

<main class="px-4 py-4 space-y-4">

  <!-- Progress Card -->
  <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-1 bg-gray-100">
      <div class="h-full bg-[#2E7D32]" style="width:{{ $summary['persen'] }}%"></div>
    </div>
    <div class="flex justify-between items-end mt-1">
      <div>
        <p class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider">Progress Tim · {{ \Carbon\Carbon::parse($tanggal)->format('d M') }}</p>
        <p class="text-2xl font-bold text-[#2E7D32] mt-0.5">{{ $summary['persen'] }}%</p>
      </div>
      <div class="text-right flex flex-col gap-1">
        <span class="text-[10px] font-bold bg-green-100 text-[#2E7D32] px-2 py-0.5 rounded">{{ $summary['selesai'] }} Selesai</span>
        <span class="text-[10px] font-bold bg-amber-50 text-amber-700 px-2 py-0.5 rounded border border-amber-200">{{ $summary['dalam_proses'] }} Berjalan</span>
        <span class="text-[10px] font-bold bg-gray-100 text-gray-500 px-2 py-0.5 rounded">{{ $summary['belum'] }} Pending</span>
      </div>
    </div>
  </div>

  <!-- Completed Tasks -->
  @if($perluValidasi->isNotEmpty())
  <div class="space-y-3">
    <h3 class="text-[10px] font-bold text-[#2E7D32] uppercase tracking-widest">Selesai Hari Ini</h3>
    @foreach($perluValidasi as $t)
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm space-y-2">
      <div class="flex justify-between items-start">
        <div>
          <h4 class="text-sm font-bold text-[#1F2937]">{{ $t->judul }}</h4>
          @if($t->kandang)
          <p class="text-xs text-[#6B7280] mt-0.5">📍 {{ $t->kandang->nama_kandang }}</p>
          @endif
          @if($t->waktu_selesai)
          <p class="text-xs text-[#6B7280]">Selesai: {{ \Carbon\Carbon::parse($t->waktu_selesai)->format('H:i') }}</p>
          @endif
        </div>
        <span class="text-[10px] font-bold text-[#2E7D32] bg-green-50 px-2 py-0.5 rounded-lg border border-green-100">Selesai</span>
      </div>
      @if($t->catatan_penyelesaian)
      <p class="text-xs text-[#6B7280] italic bg-gray-50 p-2 rounded-lg">"{{ $t->catatan_penyelesaian }}"</p>
      @endif
    </div>
    @endforeach
  </div>
  @endif

  <!-- Semua Tugas -->
  @if($semua->isNotEmpty())
  <div class="space-y-2">
    <h3 class="text-[10px] font-bold text-[#6B7280] uppercase tracking-widest">Semua Tugas Hari Ini</h3>
    <div class="bg-white rounded-xl overflow-hidden border border-gray-200 shadow-sm divide-y divide-gray-100">
      @foreach($semua as $t)
      @php
        $iconColor = match($t->status) {
          'selesai'      => 'text-[#2E7D32]',
          'dalam_proses' => 'text-amber-500',
          'dilewati'     => 'text-[#B14B6F]',
          default        => 'text-gray-300',
        };
        $icon = match($t->status) {
          'selesai'      => 'check_circle',
          'dalam_proses' => 'pending',
          'dilewati'     => 'cancel',
          default        => 'schedule',
        };
        $fill = in_array($t->status, ['selesai']) ? "font-variation-settings:'FILL' 1" : '';
      @endphp
      <div class="flex items-center gap-3 p-3">
        <span class="material-symbols-outlined {{ $iconColor }} text-xl" style="{{ $fill }}">{{ $icon }}</span>
        <div class="flex-1 min-w-0">
          <p class="text-sm {{ $t->status === 'selesai' ? 'text-gray-400 line-through' : 'text-[#1F2937]' }} truncate">{{ $t->judul }}</p>
          @if($t->kandang)
          <p class="text-[10px] text-[#6B7280]">{{ $t->kandang->nama_kandang }}</p>
          @endif
        </div>
        <span class="text-[10px] text-[#6B7280] shrink-0 capitalize">{{ $t->status }}</span>
      </div>
      @endforeach
    </div>
  </div>
  @else
  <div class="text-center py-10 text-[#6B7280]">
    <span class="material-symbols-outlined text-4xl text-gray-300">task_alt</span>
    <p class="text-sm mt-2">Belum ada tugas hari ini</p>
  </div>
  @endif

</main>

<!-- Bottom Nav -->
<nav class="fixed bottom-0 left-0 w-full max-w-[390px] bg-white border-t border-gray-200 flex justify-around items-center h-16 z-50 shadow-[0_-2px_8px_rgba(0,0,0,0.06)] left-1/2 -translate-x-1/2">
  <a href="{{ route('kk.dashboard') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">dashboard</span>
    <span class="text-[10px] font-medium">Beranda</span>
  </a>
  <a href="{{ route('kk.monitor-tugas') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 bg-[#2E7D32] text-white py-2 mx-1 rounded-xl">
    <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">assignment_turned_in</span>
    <span class="text-[10px] font-bold">Monitor</span>
  </a>
  <a href="{{ route('kk.kesehatan') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">medical_services</span>
    <span class="text-[10px] font-medium">Kesehatan</span>
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
