@extends('mobile.layout')
@section('title', 'Input Timbangan — Pengurus Kandang')

@section('content')

<!-- TopAppBar -->
<header class="bg-[#FAFAF7] sticky top-0 z-50 border-b border-gray-200 flex items-center justify-between px-4 h-14">
  <div class="flex items-center gap-3">
    <a href="{{ route('pk.dashboard') }}" class="text-[#1F2937]">
      <span class="text-2xl material-symbols-outlined">arrow_back</span>
    </a>
    <h1 class="text-base font-semibold text-[#2E7D32]">Input Timbangan</h1>
  </div>
</header>

<!-- Blind Weighing Notice -->
<div class="flex items-start gap-3 px-4 py-3 border-b border-blue-100 bg-blue-50">
  <span class="material-symbols-outlined text-blue-600 text-lg mt-0.5">info</span>
  <p class="text-xs leading-relaxed text-blue-800">
    <strong>Metode Blind Weighing aktif.</strong> Berat sebelumnya tidak ditampilkan. ADG dihitung otomatis setelah validasi Kepala Kandang.
  </p>
</div>

<!-- Flash Message -->
@if(session('success'))
<div class="p-3 mx-4 mt-3 text-sm font-medium text-green-800 border border-green-200 bg-green-50 rounded-xl">
  {{ session('success') }}
</div>
@endif

<!-- Form -->
<form method="POST" action="{{ route('pk.timbangan.store') }}">
@csrf

<main class="px-4 pt-4 space-y-4">

  <!-- Domba Selector -->
  <div class="p-4 bg-white border border-gray-200 shadow-sm rounded-xl"
    x-data="{
        open: false,
        search: '',
        selectedId: '{{ old('ear_tag_id', '') }}',
        options: [
            @foreach($dombaList as $domba)
            {
                id: '{{ $domba->ear_tag_id }}',
                text: '{{ $domba->ear_tag_id }}{{ $domba->nama ? ' · ' . $domba->nama : '' }}'
            },
            @endforeach
        ],
        get filteredOptions() {
            if (this.search === '') {
                return this.options;
            }
            return this.options.filter(opt => opt.text.toLowerCase().includes(this.search.toLowerCase()));
        },
        init() {
            if (this.selectedId) {
                let selectedOpt = this.options.find(opt => opt.id === this.selectedId);
                if (selectedOpt) {
                    this.search = selectedOpt.text;
                }
            }
        },
        selectOption(opt) {
            this.selectedId = opt.id;
            this.search = opt.text;
            this.open = false;
        }
    }">

    <label class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider block mb-2">Pilih Domba</label>

    @error('ear_tag_id')
    <p class="mb-2 text-xs text-red-600">{{ $message }}</p>
    @enderror

    <div class="relative">
        <input type="hidden" name="ear_tag_id" :value="selectedId" required>

        <div class="relative">
            <svg class="absolute w-4 h-4 text-gray-400 -translate-y-1/2 left-3 top-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text"
                   x-model="search"
                   @input="open = true; selectedId = ''"
                   @focus="open = true"
                   @click.away="open = false"
                   placeholder="-- Pilih Ear Tag Domba --"
                   class="w-full pl-9 pr-4 py-3 border border-gray-300 rounded-xl text-sm text-[#1F2937] focus:outline-none focus:ring-2 focus:ring-[#2E7D32] bg-white"
                   autocomplete="off">
        </div>

        <div x-show="open && filteredOptions.length > 0"
             x-transition
             class="absolute z-10 w-full mt-2 overflow-hidden bg-white border border-gray-200 shadow-lg rounded-xl"
             style="display: none;">

            <ul class="p-1 overflow-y-auto max-h-56">
                <template x-for="opt in filteredOptions" :key="opt.id">
                    <li @click="selectOption(opt)"
                        class="px-3 py-2 text-sm transition-colors rounded-lg cursor-pointer"
                        :class="selectedId === opt.id ? 'bg-[#2E7D32]/10 text-[#2E7D32] font-bold' : 'text-[#1F2937] hover:bg-gray-100'">
                        <span x-text="opt.text"></span>
                    </li>
                </template>
            </ul>

        </div>

        <div x-show="open && filteredOptions.length === 0 && search !== ''"
             class="absolute z-10 w-full p-4 mt-2 text-center bg-white border border-gray-200 shadow-lg rounded-xl"
             style="display: none;">
            <span class="text-sm italic text-gray-400">Domba tidak ditemukan</span>
        </div>
    </div>

    <div class="mt-3 p-2.5 bg-gray-50 rounded-lg flex justify-between items-center border border-gray-100">
      <p class="text-xs text-[#6B7280]">Berat Sebelumnya</p>
      <div class="flex items-center gap-1.5 text-gray-400">
        <span class="text-sm material-symbols-outlined" style="font-variation-settings:'FILL' 1">lock</span>
        <span class="text-xs italic">Tersembunyi (Blind Weighing)</span>
      </div>
    </div>
</div>

  <!-- Weight Input -->
  <div class="flex flex-col items-center gap-4 p-5 bg-white border border-green-200 shadow-md rounded-xl">
    <label class="text-[10px] font-bold text-[#6B7280] uppercase tracking-widest">Input Berat Badan (Kg)</label>
    @error('berat_kg')
    <p class="text-xs text-red-600">{{ $message }}</p>
    @enderror
    <div class="flex items-center gap-4">
      <button type="button" onclick="adjustWeight(-0.1)"
        class="w-14 h-14 rounded-full border-2 border-[#2E7D32] text-[#2E7D32] flex items-center justify-center active:scale-90 transition-transform shrink-0">
        <span class="text-2xl material-symbols-outlined">remove</span>
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
        <span class="text-2xl material-symbols-outlined">add</span>
      </button>
    </div>
    <p class="text-[11px] text-[#6B7280] text-center">Ketik angka langsung atau gunakan tombol +/−</p>
  </div>

  <!-- Date -->
  <div class="p-4 bg-white border border-gray-200 shadow-sm rounded-xl">
    <label class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider block mb-2">Tanggal Penimbangan</label>
    @error('tanggal_timbang')
    <p class="mb-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
    <div class="relative">
      <span class="absolute text-xl text-gray-400 -translate-y-1/2 material-symbols-outlined left-3 top-1/2">calendar_today</span>
      <input type="date" name="tanggal_timbang" value="{{ old('tanggal_timbang', today()->toDateString()) }}" required
        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2E7D32]"/>
    </div>
  </div>

  <!-- Notes -->
  <div class="p-4 bg-white border border-gray-200 shadow-sm rounded-xl">
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
<nav class="fixed bottom-0 left-1/2 w-full max-w-[390px] bg-white border-t border-gray-200 flex justify-around items-center h-16 z-50 shadow-[0_-2px_8px_rgba(0,0,0,0.06)] -translate-x-1/2">
  <a href="{{ route('pk.dashboard') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="text-2xl material-symbols-outlined">dashboard</span>
    <span class="text-[10px] font-medium">Beranda</span>
  </a>
  <a href="{{ route('pk.tugas') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="text-2xl material-symbols-outlined">assignment_turned_in</span>
    <span class="text-[10px] font-medium">Tugas</span>
  </a>
  <a href="{{ route('pk.timbangan') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 {{ request()->routeIs('pk.timbangan') ? 'bg-[#2E7D32] text-white mx-1 rounded-xl' : 'text-gray-500' }} py-2">
    <span class="text-2xl material-symbols-outlined" {!! request()->routeIs('pk.timbangan') ? 'style="font-variation-settings:\'FILL\' 1"' : '' !!}>scale</span>
    <span class="text-[10px] font-medium">Timbangan</span>
  </a>
  <a href="{{ route('pk.kesehatan') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 {{ request()->routeIs('pk.kesehatan') ? 'bg-[#2E7D32] text-white mx-1 rounded-xl' : 'text-gray-500' }} py-2">
    <span class="text-2xl material-symbols-outlined" {!! request()->routeIs('pk.kesehatan') ? 'style="font-variation-settings:\'FILL\' 1"' : '' !!}>medical_services</span>
    <span class="text-[10px] font-medium">Kesehatan</span>
  </a>
  <a href="{{ route('pk.kelahiran') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 {{ request()->routeIs('pk.kelahiran') ? 'bg-[#2E7D32] text-white mx-1 rounded-xl' : 'text-gray-500' }} py-2">
    <span class="text-2xl material-symbols-outlined" {!! request()->routeIs('pk.kelahiran') ? 'style="font-variation-settings:\'FILL\' 1"' : '' !!}>child_care</span>
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
