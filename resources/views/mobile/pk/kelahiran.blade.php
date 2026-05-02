@extends('mobile.layout')
@section('title', 'Catat Kelahiran — Pengurus Kandang')

@section('content')

<!-- TopAppBar -->
<header class="bg-[#FAFAF7] sticky top-0 z-50 border-b border-gray-200 flex items-center justify-between px-4 h-14">
  <div class="flex items-center gap-3">
    <a href="{{ route('pk.dashboard') }}" class="text-[#1F2937]">
      <span class="material-symbols-outlined text-2xl">arrow_back</span>
    </a>
    <h1 class="text-base font-semibold text-[#2E7D32]">Catat Kelahiran</h1>
  </div>
  <div class="bg-amber-100 text-amber-700 px-2.5 py-0.5 rounded-full text-[11px] font-bold">PK</div>
</header>

<!-- Role Notice -->
<div class="bg-amber-50 border-l-4 border-amber-500 mx-4 mt-4 p-3 rounded-r-xl flex gap-2.5">
  <span class="material-symbols-outlined text-amber-600 text-lg mt-0.5">warning</span>
  <p class="text-xs text-amber-800 leading-relaxed">PK hanya dapat mencatat kelahiran. Ear tag permanen akan diberikan setelah verifikasi Kepala Kandang.</p>
</div>

@if($errors->any())
<div class="mx-4 mt-3 bg-red-50 border border-red-200 rounded-xl p-3">
  @foreach($errors->all() as $e)
  <p class="text-xs text-red-700">{{ $e }}</p>
  @endforeach
</div>
@endif

<form method="POST" action="{{ route('pk.kelahiran.store') }}">
@csrf

<main class="px-4 mt-4 flex flex-col gap-4">

  <!-- Induk Selector -->
  <section class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
    <h2 class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider mb-3">Pilih Domba Induk</h2>
    <select name="indukan_id" required
      class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm text-[#1F2937] focus:outline-none focus:ring-2 focus:ring-[#2E7D32]">
      <option value="">-- Pilih Induk (Betina Aktif) --</option>
      @foreach($induks as $domba)
      <option value="{{ $domba->ear_tag_id }}" {{ old('indukan_id') === $domba->ear_tag_id ? 'selected' : '' }}>
        {{ $domba->ear_tag_id }}{{ $domba->nama ? ' · ' . $domba->nama : '' }}
      </option>
      @endforeach
    </select>
  </section>

  <!-- Date & Time -->
  <section class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
    <h2 class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider mb-3">Tanggal Kelahiran</h2>
    <div class="relative">
      <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">calendar_today</span>
      <input type="date" name="tanggal_kelahiran" value="{{ old('tanggal_kelahiran', today()->toDateString()) }}" required
        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2E7D32]"/>
    </div>
  </section>

  <!-- Lamb Count -->
  <section class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex flex-col items-center gap-4">
    <h2 class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider w-full">Jumlah Anak Lahir</h2>
    <div class="grid grid-cols-2 gap-4 w-full">
      <div>
        <label class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider block mb-2 text-center">Hidup</label>
        <div class="flex items-center justify-center gap-3">
          <button type="button" onclick="adjustCount('hidup', -1)"
            class="w-10 h-10 rounded-full border-2 border-gray-200 text-[#6B7280] flex items-center justify-center">
            <span class="material-symbols-outlined text-xl">remove</span>
          </button>
          <span id="hidupDisplay" class="text-3xl font-black text-[#1F2937]">{{ old('jml_anak_hidup', 1) }}</span>
          <button type="button" onclick="adjustCount('hidup', 1)"
            class="w-10 h-10 rounded-full border-2 border-[#2E7D32] text-[#2E7D32] flex items-center justify-center">
            <span class="material-symbols-outlined text-xl">add</span>
          </button>
        </div>
        <input type="hidden" name="jml_anak_hidup" id="hidupInput" value="{{ old('jml_anak_hidup', 1) }}"/>
      </div>
      <div>
        <label class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider block mb-2 text-center">Lahir Mati</label>
        <div class="flex items-center justify-center gap-3">
          <button type="button" onclick="adjustCount('mati', -1)"
            class="w-10 h-10 rounded-full border-2 border-gray-200 text-[#6B7280] flex items-center justify-center">
            <span class="material-symbols-outlined text-xl">remove</span>
          </button>
          <span id="matiDisplay" class="text-3xl font-black text-[#1F2937]">{{ old('jml_anak_mati', 0) }}</span>
          <button type="button" onclick="adjustCount('mati', 1)"
            class="w-10 h-10 rounded-full border-2 border-[#B14B6F] text-[#B14B6F] flex items-center justify-center">
            <span class="material-symbols-outlined text-xl">add</span>
          </button>
        </div>
        <input type="hidden" name="jml_anak_mati" id="matiInput" value="{{ old('jml_anak_mati', 0) }}"/>
      </div>
    </div>
  </section>

  <!-- Notes -->
  <section class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
    <h2 class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider mb-3">Catatan Proses Kelahiran</h2>
    <textarea name="catatan" rows="3" placeholder="Catatan proses kelahiran, kondisi khusus, dll..."
      class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm placeholder-gray-400 resize-none focus:outline-none focus:ring-2 focus:ring-[#2E7D32]">{{ old('catatan') }}</textarea>
  </section>

  <!-- Temp Tag Info -->
  <div class="bg-green-50 border border-green-200 rounded-xl p-3 flex gap-2.5">
    <span class="material-symbols-outlined text-[#2E7D32] text-lg mt-0.5">info</span>
    <p class="text-xs text-[#2E7D32]/80 leading-relaxed">Ear tag sementara (TEMP-xxx) diberikan otomatis oleh sistem. Kepala Kandang akan melakukan verifikasi dan assign tag permanen.</p>
  </div>

  <button type="submit"
    class="w-full py-4 bg-[#2E7D32] text-white font-bold text-base rounded-xl shadow-lg shadow-green-900/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2 mb-4">
    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">save</span>
    Simpan Data Kelahiran
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
  <a href="{{ route('pk.timbangan') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">scale</span>
    <span class="text-[10px] font-medium">Timbangan</span>
  </a>
  <a href="{{ route('pk.kesehatan') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">medical_services</span>
    <span class="text-[10px] font-medium">Kesehatan</span>
  </a>
  <a href="{{ route('pk.kelahiran') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 bg-[#2E7D32] text-white py-2 mx-1 rounded-xl">
    <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">child_care</span>
    <span class="text-[10px] font-bold">Kelahiran</span>
  </a>
</nav>

@endsection

@push('scripts')
<script>
let hidup = {{ old('jml_anak_hidup', 1) }};
let mati  = {{ old('jml_anak_mati', 0) }};

function adjustCount(type, delta) {
  if (type === 'hidup') {
    hidup = Math.max(0, Math.min(6, hidup + delta));
    document.getElementById('hidupDisplay').textContent = hidup;
    document.getElementById('hidupInput').value = hidup;
  } else {
    mati = Math.max(0, Math.min(6, mati + delta));
    document.getElementById('matiDisplay').textContent = mati;
    document.getElementById('matiInput').value = mati;
  }
}
</script>
@endpush
