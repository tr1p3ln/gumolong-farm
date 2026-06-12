@extends('mobile.layout')
@section('title', 'Catat Kelahiran — Pengurus Kandang')

@section('content')

<!-- TopAppBar -->
<header class="bg-[#FAFAF7] sticky top-0 z-50 border-b border-gray-200 flex items-center justify-between px-4 h-14">
  <div class="flex items-center gap-3">
    <a href="{{ route('pk.dashboard') }}" class="text-[#1F2937]">
      <span class="text-2xl material-symbols-outlined">arrow_back</span>
    </a>
    <h1 class="text-base font-semibold text-[#2E7D32]">Catat Kelahiran</h1>
  </div>
  <div class="bg-amber-100 text-amber-700 px-2.5 py-0.5 rounded-full text-[11px] font-bold">PK</div>
</header>

<!-- Role Notice -->
<div class="bg-amber-50 border-l-4 border-amber-500 mx-4 mt-4 p-3 rounded-r-xl flex gap-2.5">
  <span class="material-symbols-outlined text-amber-600 text-lg mt-0.5">warning</span>
  <p class="text-xs leading-relaxed text-amber-800">PK hanya dapat mencatat kelahiran. Ear tag permanen akan diberikan setelah verifikasi Kepala Kandang.</p>
</div>

@if($errors->any())
<div class="p-3 mx-4 mt-3 border border-red-200 bg-red-50 rounded-xl">
  @foreach($errors->all() as $e)
  <p class="text-xs text-red-700">{{ $e }}</p>
  @endforeach
</div>
@endif

<form method="POST" action="{{ route('pk.kelahiran.store') }}">
@csrf

<main class="flex flex-col gap-4 px-4 mt-4">

  <!-- Induk Selector -->
  <section class="p-4 bg-white border border-gray-200 shadow-sm rounded-xl"
    x-data="{
        open: false,
        search: '',
        selectedId: '{{ old('indukan_id', '') }}',
        options: [
            @foreach($induks as $domba)
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

    <h2 class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider mb-3">Pilih Domba Induk</h2>

    <div class="relative">
        <input type="hidden" name="indukan_id" :value="selectedId" required>

        <div class="relative">
            <svg class="absolute w-4 h-4 text-gray-400 -translate-y-1/2 left-3 top-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text"
                   x-model="search"
                   @input="open = true; selectedId = ''"
                   @focus="open = true"
                   @click.away="open = false"
                   placeholder="Cari ear tag atau nama..."
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
</section>

  <!-- Date & Time -->
  <section class="p-4 bg-white border border-gray-200 shadow-sm rounded-xl">
    <h2 class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider mb-3">Tanggal Kelahiran</h2>
    <div class="relative">
      <span class="absolute text-lg text-gray-400 -translate-y-1/2 material-symbols-outlined left-3 top-1/2">calendar_today</span>
      <input type="date" name="tanggal_kelahiran" value="{{ old('tanggal_kelahiran', today()->toDateString()) }}" required
        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#2E7D32]"/>
    </div>
  </section>

  <!-- Lamb Count -->
  <section class="flex flex-col items-center gap-4 p-4 bg-white border border-gray-200 shadow-sm rounded-xl">
    <h2 class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider w-full">Jumlah Anak Lahir</h2>
    <div class="grid w-full grid-cols-2 gap-4">
      <div>
        <label class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider block mb-2 text-center">Hidup</label>
        <div class="flex items-center justify-center gap-3">
          <button type="button" onclick="adjustCount('hidup', -1)"
            class="w-10 h-10 rounded-full border-2 border-gray-200 text-[#6B7280] flex items-center justify-center">
            <span class="text-xl material-symbols-outlined">remove</span>
          </button>
          <span id="hidupDisplay" class="text-3xl font-black text-[#1F2937]">{{ old('jml_anak_hidup', 1) }}</span>
          <button type="button" onclick="adjustCount('hidup', 1)"
            class="w-10 h-10 rounded-full border-2 border-[#2E7D32] text-[#2E7D32] flex items-center justify-center">
            <span class="text-xl material-symbols-outlined">add</span>
          </button>
        </div>
        <input type="hidden" name="jml_anak_hidup" id="hidupInput" value="{{ old('jml_anak_hidup', 1) }}"/>
      </div>
      <div>
        <label class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider block mb-2 text-center">Lahir Mati</label>
        <div class="flex items-center justify-center gap-3">
          <button type="button" onclick="adjustCount('mati', -1)"
            class="w-10 h-10 rounded-full border-2 border-gray-200 text-[#6B7280] flex items-center justify-center">
            <span class="text-xl material-symbols-outlined">remove</span>
          </button>
          <span id="matiDisplay" class="text-3xl font-black text-[#1F2937]">{{ old('jml_anak_mati', 0) }}</span>
          <button type="button" onclick="adjustCount('mati', 1)"
            class="w-10 h-10 rounded-full border-2 border-[#B14B6F] text-[#B14B6F] flex items-center justify-center">
            <span class="text-xl material-symbols-outlined">add</span>
          </button>
        </div>
        <input type="hidden" name="jml_anak_mati" id="matiInput" value="{{ old('jml_anak_mati', 0) }}"/>
      </div>
    </div>
  </section>

  <!-- Notes -->
  <section class="p-4 bg-white border border-gray-200 shadow-sm rounded-xl">
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
