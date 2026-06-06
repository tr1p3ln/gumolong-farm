@extends('mobile.layout')
@section('title', 'Input Timbangan — Pengurus Kandang')

@section('content')

<!-- TopAppBar -->
<header class="bg-[#FAFAF7] sticky top-0 z-50 border-b border-gray-200 flex items-center justify-between px-4 h-14">
  <div class="flex items-center gap-3">
    <a href="{{ route('pk.dashboard') }}" class="text-[#1F2937]">
      <span class="material-symbols-outlined text-2xl">arrow_back</span>
    </a>
    <h1 class="text-base font-semibold text-[#2E7D32]">Input Timbangan</h1>
  </div>
</header>

<!-- Blind Weighing Notice -->
<div class="bg-blue-50 px-4 py-3 flex gap-3 items-start border-b border-blue-100">
  <span class="material-symbols-outlined text-blue-600 text-lg mt-0.5">info</span>
  <p class="text-xs text-blue-800 leading-relaxed">
    <strong>Metode Blind Weighing aktif.</strong> Berat sebelumnya tidak ditampilkan. ADG dihitung otomatis setelah validasi Kepala Kandang.
  </p>
</div>

<!-- Flash Message -->
@if(session('success'))
<div class="mx-4 mt-3 bg-green-50 border border-green-200 rounded-xl p-3 text-sm text-green-800 font-medium">
  {{ session('success') }}
</div>
@endif

<!-- Form -->
<form method="POST" action="{{ route('pk.timbangan.store') }}">
@csrf

<main class="px-4 pt-4 space-y-4">

  <!-- Domba Selector -->
  <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
    <label class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider block mb-2">Pilih Domba</label>
    @error('ear_tag_id')
    <p class="text-xs text-red-600 mb-2">{{ $message }}</p>
    @enderror
    <select name="ear_tag_id" required
      class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm text-[#1F2937] focus:outline-none focus:ring-2 focus:ring-[#2E7D32] focus:border-transparent">
      <option value="">-- Pilih Ear Tag Domba --</option>
      @foreach($dombaList as $domba)
      <option value="{{ $domba->ear_tag_id }}" {{ old('ear_tag_id') === $domba->ear_tag_id ? 'selected' : '' }}>
        {{ $domba->ear_tag_id }}{{ $domba->nama ? ' · ' . $domba->nama : '' }}
      </option>
      @endforeach
    </select>
    <div class="mt-3 p-2.5 bg-gray-50 rounded-lg flex justify-between items-center border border-gray-100">
      <p class="text-xs text-[#6B7280]">Berat Sebelumnya</p>
      <div class="flex items-center gap-1.5 text-gray-400">
        <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1">lock</span>
        <span class="text-xs italic">Tersembunyi (Blind Weighing)</span>
      </div>
    </div>
  </div>

  <!-- Weight Input -->
  <div class="bg-white border border-green-200 rounded-xl p-5 shadow-md flex flex-col items-center gap-4">
    <label class="text-[10px] font-bold text-[#6B7280] uppercase tracking-widest">Input Berat Badan (Kg)</label>
    @error('berat_kg')
    <p class="text-xs text-red-600">{{ $message }}</p>
    @enderror
    <div class="flex items-center gap-4">
      <button type="button" onclick="adjustWeight(-0.1)"
        class="w-14 h-14 rounded-full border-2 border-[#2E7D32] text-[#2E7D32] flex items-center justify-center active:scale-90 transition-transform shrink-0">
        <span class="material-symbols-outlined text-2xl">remove</span>
      </button>
      <div class="text-center">
        <input type="number" id="beratInput" name="berat_kg" step="0.1" min="0" max="999"
          value="{{ old('berat_kg', '0.0') }}"
          oninput="syncDisplay(this.value)"
          class="w-28 text-center text-3xl font-bold text-[#1F2937] tabular-nums bg-transparent border-b-2 border-[#2E7D32] focus:outline-none focus:border-[#2E7D32] appearance-none"/>
        <p class="text-sm text-[#6B7280] mt-2">kilogram</p>
      </div>
      <button type="button" onclick="adjustWeight(0.1)"
        class="w-14 h-14 rounded-full bg-[#2E7D32] text-white flex items-center justify-center shadow-lg active:scale-90 transition-transform shrink-0">
        <span class="material-symbols-outlined text-2xl">add</span>
      </button>
    </div>
    <p class="text-[11px] text-[#6B7280] text-center">Ketik angka langsung atau gunakan tombol +/−</p>
  </div>

  <!-- Date -->
  <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
    <label class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider block mb-2">Tanggal Penimbangan</label>
    @error('tanggal_timbang')
    <p class="text-xs text-red-600 mb-1">{{ $message }}</p>
    @enderror
    <div class="relative">
      <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl">calendar_today</span>
      <input type="date" name="tanggal_timbang" value="{{ old('tanggal_timbang', today()->toDateString()) }}" required
        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2E7D32]"/>
    </div>
  </div>

  <!-- Notes -->
  <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
    <label class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider block mb-2">Catatan (Opsional)</label>
    <textarea name="catatan" rows="3" placeholder="Tambahkan catatan jika diperlukan..."
      class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm text-[#1F2937] placeholder-gray-400 resize-none focus:outline-none focus:ring-2 focus:ring-[#2E7D32]">{{ old('catatan') }}</textarea>
  </div>

  <p class="text-xs text-[#6B7280] text-center italic px-2">
    Data akan diverifikasi oleh Kepala Kandang sebelum masuk ke laporan performa.
  </p>

  <button type="submit"
    class="w-full py-4 bg-[#2E7D32] text-white font-bold text-base rounded-xl shadow-lg shadow-green-900/20 active:scale-[0.98] transition-all mb-4">
    Simpan Penimbangan
  </button>

</main>
</form>

<!-- Bottom Nav -->
<nav class="fixed bottom-0 left-0 w-full max-w-[390px] bg-white border-t border-gray-200 flex justify-around items-center h-16 z-50 shadow-[0_-2px_8px_rgba(0,0,0,0.06)] left-1/2 -translate-x-1/2">
  <a href="{{ route('pk.dashboard') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">dashboard</span>
    <span class="text-[10px] font-medium">Beranda</span>
  </a>
  <a href="{{ route('pk.tugas') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">assignment_turned_in</span>
    <span class="text-[10px] font-medium">Tugas</span>
  </a>
  <a href="{{ route('pk.timbangan') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 bg-[#2E7D32] text-white py-2 mx-1 rounded-xl">
    <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">scale</span>
    <span class="text-[10px] font-bold">Timbangan</span>
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
  function adjustWeight(delta) {
    let current = parseFloat(document.getElementById('beratInput').value) || 0;
    current = Math.max(0, Math.round((current + delta) * 10) / 10);
    document.getElementById('beratInput').value = current.toFixed(1);
  }
  function syncDisplay(val) {}
</script>
@endpush
