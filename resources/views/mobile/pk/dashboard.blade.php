@extends('mobile.layout')
@section('title', 'Dashboard — Pengurus Kandang')

@section('content')

<!-- TopAppBar -->
<header class="bg-[#FAFAF7] sticky top-0 z-50 border-b border-gray-200 flex items-center justify-between px-4 h-14">
  <div class="flex items-center gap-2.5">
    <div class="relative">
      <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center font-bold text-amber-700 text-sm border border-amber-200">
        {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name ?? 'PK', 0, 2)) }}
      </div>
      <div class="absolute -bottom-0.5 -right-0.5 bg-amber-500 text-white text-[8px] font-bold px-1 rounded-full border border-white leading-3 py-0.5">PK</div>
    </div>
    <div>
      <p class="text-[11px] text-[#6B7280] font-medium">Pengurus Kandang</p>
      <p class="text-sm font-bold text-[#1F2937] leading-none">{{ auth()->user()->nama ?? auth()->user()->name }}</p>
    </div>
  </div>
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="p-2 text-[#6B7280]">
      <span class="material-symbols-outlined text-2xl">logout</span>
    </button>
  </form>
</header>

<!-- Flash Message -->
@if(session('success'))
<div class="mx-4 mt-3 bg-green-50 border border-green-200 rounded-xl p-3 text-sm text-green-800 font-medium">
  {{ session('success') }}
</div>
@endif

<!-- Greeting Banner -->
<section class="bg-[#2E7D32] px-5 pt-5 pb-6">
  <div class="flex justify-between items-start">
    <div>
      <span class="inline-block border border-white/40 text-white/90 text-[10px] font-bold px-2.5 py-0.5 rounded-full mb-2">Pengurus Kandang</span>
      <h2 class="text-white text-xl font-bold">Selamat Pagi, {{ explode(' ', auth()->user()->nama ?? auth()->user()->name)[0] }} 👋</h2>
      <p class="text-white/70 text-xs mt-0.5">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</p>
    </div>
    <span class="material-symbols-outlined text-white/20 text-6xl">agriculture</span>
  </div>
</section>

<!-- Task Progress Card -->
<div class="mx-4 -mt-4 bg-white rounded-xl shadow-sm border border-gray-200 p-4">
  <div class="flex justify-between items-start mb-3">
    <div>
      <p class="text-xs font-bold text-[#6B7280] uppercase tracking-wider">Tugas Hari Ini</p>
      <p class="text-[#1F2937] font-bold text-base mt-0.5">
        @if($summary['persen'] >= 100) Semua Selesai! 🎉
        @elseif($summary['persen'] >= 50) Hampir Selesai!
        @else Ayo Semangat!
        @endif
      </p>
    </div>
    <span class="text-[#2E7D32] font-bold text-xl">{{ $summary['persen'] }}%</span>
  </div>
  <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
    <div class="bg-[#2E7D32] h-2 rounded-full" style="width:{{ $summary['persen'] }}%"></div>
  </div>
  <p class="text-[11px] text-[#6B7280]">{{ $summary['selesai'] }} dari {{ $summary['total'] }} tugas selesai</p>
</div>

<!-- Quick Actions -->
<section class="px-4 mt-5">
  <h3 class="text-sm font-bold text-[#1F2937] mb-3">Aksi Cepat</h3>
  <div class="grid grid-cols-2 gap-3">
    <a href="{{ route('pk.timbangan') }}"
      class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col items-center gap-2 shadow-sm active:scale-95 transition-transform">
      <div class="w-11 h-11 rounded-full bg-green-50 flex items-center justify-center">
        <span class="material-symbols-outlined text-[#2E7D32] text-2xl">scale</span>
      </div>
      <span class="text-xs font-bold text-[#1F2937] text-center">Input Timbangan</span>
    </a>
    <a href="{{ route('pk.tugas') }}"
      class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col items-center gap-2 shadow-sm active:scale-95 transition-transform">
      <div class="w-11 h-11 rounded-full bg-green-50 flex items-center justify-center">
        <span class="material-symbols-outlined text-[#2E7D32] text-2xl">assignment_turned_in</span>
      </div>
      <span class="text-xs font-bold text-[#1F2937] text-center">Daily Task</span>
    </a>
    <a href="{{ route('pk.kesehatan') }}"
      class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col items-center gap-2 shadow-sm active:scale-95 transition-transform">
      <div class="w-11 h-11 rounded-full bg-red-50 flex items-center justify-center">
        <span class="material-symbols-outlined text-[#B14B6F] text-2xl">medical_services</span>
      </div>
      <span class="text-xs font-bold text-[#1F2937] text-center">Lapor Sakit</span>
    </a>
    <a href="{{ route('pk.kelahiran') }}"
      class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col items-center gap-2 shadow-sm active:scale-95 transition-transform">
      <div class="w-11 h-11 rounded-full bg-green-50 flex items-center justify-center">
        <span class="material-symbols-outlined text-[#2E7D32] text-2xl">child_care</span>
      </div>
      <span class="text-xs font-bold text-[#1F2937] text-center">Catat Kelahiran</span>
    </a>
  </div>
</section>

<!-- Sheep Count Card -->
<section class="px-4 mt-5">
  <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
    <p class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider mb-3">Populasi Ternak</p>
    <div class="flex items-center justify-between">
      <div class="text-center">
        <p class="text-2xl font-bold text-[#2E7D32]">{{ $totalDomba }}</p>
        <p class="text-[10px] text-[#6B7280] mt-0.5">Total</p>
      </div>
      <div class="w-px h-10 bg-gray-200"></div>
      <div class="text-center">
        <p class="text-2xl font-bold text-[#1F2937]">{{ $pejantan }}</p>
        <p class="text-[10px] text-[#6B7280] mt-0.5">Pejantan</p>
      </div>
      <div class="w-px h-10 bg-gray-200"></div>
      <div class="text-center">
        <p class="text-2xl font-bold text-[#1F2937]">{{ $betina }}</p>
        <p class="text-[10px] text-[#6B7280] mt-0.5">Betina</p>
      </div>
      <div class="w-px h-10 bg-gray-200"></div>
      <div class="text-center">
        <p class="text-2xl font-bold text-[#B14B6F]">{{ $perhatian }}</p>
        <p class="text-[10px] text-[#6B7280] mt-0.5">Perhatian</p>
      </div>
    </div>
  </div>
</section>

<!-- Recent Activity -->
<section class="px-4 mt-5 mb-6">
  <h3 class="text-sm font-bold text-[#1F2937] mb-3">Aktivitas Saya Hari Ini</h3>
  @if($recentTugas->isNotEmpty())
  <div class="bg-white border border-gray-200 rounded-xl divide-y divide-gray-100 shadow-sm">
    @foreach($recentTugas as $t)
    @php
      $dotColor = match($t->status) {
        'selesai'      => 'bg-green-500',
        'dalam_proses' => 'bg-amber-500',
        'dilewati'     => 'bg-[#B14B6F]',
        default        => 'bg-gray-400',
      };
    @endphp
    <div class="flex items-center gap-3 p-3.5">
      <div class="w-2.5 h-2.5 rounded-full {{ $dotColor }} shrink-0"></div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-[#1F2937] truncate">{{ $t->judul }}</p>
        <p class="text-xs text-[#6B7280] capitalize">{{ $t->status }}</p>
      </div>
      @if($t->waktu_mulai)
      <span class="text-[11px] text-[#6B7280] shrink-0">{{ \Carbon\Carbon::parse($t->waktu_mulai)->format('H:i') }}</span>
      @endif
    </div>
    @endforeach
  </div>
  @else
  <div class="bg-white border border-gray-200 rounded-xl p-4 text-center text-sm text-[#6B7280]">
    Belum ada aktivitas hari ini.
  </div>
  @endif
</section>

<!-- Bottom Nav -->
<nav class="fixed bottom-0 left-0 w-full max-w-[390px] bg-white border-t border-gray-200 flex justify-around items-center h-16 z-50 shadow-[0_-2px_8px_rgba(0,0,0,0.06)] left-1/2 -translate-x-1/2">
  <a href="{{ route('pk.dashboard') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 bg-[#2E7D32] text-white py-2 mx-1 rounded-xl">
    <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">dashboard</span>
    <span class="text-[10px] font-bold">Beranda</span>
  </a>
  <a href="{{ route('pk.tugas') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">assignment_turned_in</span>
    <span class="text-[10px] font-medium">Tugas</span>
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
