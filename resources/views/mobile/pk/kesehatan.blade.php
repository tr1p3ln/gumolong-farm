@extends('mobile.layout')
@section('title', 'Lapor Kesehatan — Pengurus Kandang')

@push('styles')
<style>
  .severity-btn.active { outline: 2px solid; }
</style>
@endpush

@section('content')

<!-- TopAppBar -->
<header class="bg-[#FAFAF7] sticky top-0 z-50 border-b border-gray-200 flex items-center justify-between px-4 h-14">
  <div class="flex items-center gap-3">
    <a href="{{ route('pk.dashboard') }}" class="text-[#1F2937]">
      <span class="material-symbols-outlined text-2xl">arrow_back</span>
    </a>
    <h1 class="text-base font-semibold text-[#2E7D32]">Lapor Kesehatan</h1>
  </div>
  <div class="bg-amber-100 text-amber-700 px-2.5 py-0.5 rounded-full text-[11px] font-bold">PK</div>
</header>

<form method="POST" action="{{ route('pk.kesehatan.store') }}">
@csrf

<main class="px-4 py-4 flex flex-col gap-4">

  <!-- Sheep Selector -->
  <section class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
    <h2 class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider mb-3">Pilih Domba yang Akan Dilaporkan</h2>
    @error('ear_tag_id')
    <p class="text-xs text-red-600 mb-2">{{ $message }}</p>
    @enderror
    <select name="ear_tag_id" required
      class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm text-[#1F2937] focus:outline-none focus:ring-2 focus:ring-[#2E7D32]">
      <option value="">-- Pilih Ear Tag Domba --</option>
      @foreach($dombaList as $domba)
      <option value="{{ $domba->ear_tag_id }}" {{ old('ear_tag_id') === $domba->ear_tag_id ? 'selected' : '' }}>
        {{ $domba->ear_tag_id }}{{ $domba->nama ? ' · ' . $domba->nama : '' }}
      </option>
      @endforeach
    </select>
  </section>

  <!-- Severity -->
  <section class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
    <h2 class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider mb-3">Tingkat Keparahan</h2>
    @error('tingkat_keparahan')
    <p class="text-xs text-red-600 mb-2">{{ $message }}</p>
    @enderror
    <div class="flex gap-2">
      <label class="flex-1 cursor-pointer">
        <input type="radio" name="tingkat_keparahan" value="ringan" class="sr-only" {{ old('tingkat_keparahan') === 'ringan' ? 'checked' : '' }}/>
        <div class="severity-btn h-11 flex items-center justify-center gap-1.5 border-2 border-gray-200 rounded-xl text-xs font-semibold text-[#6B7280] ringan-btn">
          <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>Ringan
        </div>
      </label>
      <label class="flex-1 cursor-pointer">
        <input type="radio" name="tingkat_keparahan" value="sedang" class="sr-only" {{ old('tingkat_keparahan', 'sedang') === 'sedang' ? 'checked' : '' }}/>
        <div class="severity-btn h-11 flex items-center justify-center gap-1.5 border-2 border-orange-400 rounded-xl text-xs font-bold text-orange-700 bg-orange-50 sedang-btn">
          <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>Sedang
        </div>
      </label>
      <label class="flex-1 cursor-pointer">
        <input type="radio" name="tingkat_keparahan" value="parah" class="sr-only" {{ old('tingkat_keparahan') === 'parah' ? 'checked' : '' }}/>
        <div class="severity-btn h-11 flex items-center justify-center gap-1.5 border-2 border-gray-200 rounded-xl text-xs font-semibold text-[#6B7280] parah-btn">
          <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>Parah
        </div>
      </label>
    </div>
  </section>

  <!-- Symptoms -->
  <section class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
    <h2 class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider mb-4">Gejala yang Terlihat</h2>
    @error('gejala')
    <p class="text-xs text-red-600 mb-2">{{ $message }}</p>
    @enderror
    <div class="grid grid-cols-2 gap-3">
      @foreach([
        'lemas_tidak_aktif' => 'Lemas / tidak aktif',
        'kembung_perut'     => 'Kembung perut',
        'tidak_mau_makan'   => 'Tidak mau makan',
        'mata_berair'       => 'Mata berair',
        'diare'             => 'Diare / mencret',
        'bulu_rontok'       => 'Bulu rontok',
        'batuk_bersin'      => 'Batuk / bersin',
        'pincang'           => 'Pincang',
        'luka_benjolan'     => 'Luka / benjolan',
      ] as $key => $label)
      <label class="flex items-center gap-2.5 cursor-pointer">
        <input type="checkbox" name="gejala_list[]" value="{{ $label }}"
          class="w-4 h-4 rounded border-gray-300 text-[#2E7D32] focus:ring-[#2E7D32]"/>
        <span class="text-sm text-[#1F2937]">{{ $label }}</span>
      </label>
      @endforeach
    </div>
    <!-- Hidden field to combine gejala for submission -->
    <input type="hidden" name="gejala" id="gejalaCombined" value="{{ old('gejala') }}"/>
  </section>

  <!-- Notes -->
  <section class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
    <h2 class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider mb-3">Catatan Tambahan</h2>
    <textarea name="catatan" rows="4" placeholder="Jelaskan lebih detail kondisi domba..." maxlength="500"
      class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm text-[#1F2937] placeholder-gray-400 resize-none focus:outline-none focus:ring-2 focus:ring-[#2E7D32]">{{ old('catatan') }}</textarea>
  </section>

  <button type="submit" onclick="combineGejala()"
    class="w-full py-4 bg-[#B14B6F] text-white font-bold text-base rounded-xl shadow-lg active:scale-[0.98] transition-all flex items-center justify-center gap-2 mb-2">
    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">send</span>
    Kirim Laporan
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
  <a href="{{ route('pk.kesehatan') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 bg-[#2E7D32] text-white py-2 mx-1 rounded-xl">
    <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">medical_services</span>
    <span class="text-[10px] font-bold">Kesehatan</span>
  </a>
  <a href="{{ route('pk.kelahiran') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">child_care</span>
    <span class="text-[10px] font-medium">Kelahiran</span>
  </a>
</nav>

@endsection

@push('scripts')
<script>
function combineGejala() {
  const checked = [...document.querySelectorAll('input[name="gejala_list[]"]:checked')]
    .map(c => c.value);
  document.getElementById('gejalaCombined').value = checked.length > 0
    ? checked.join(', ')
    : 'Tidak ada gejala spesifik yang dipilih';
}

// Severity radio toggle styling
document.querySelectorAll('input[name="tingkat_keparahan"]').forEach(radio => {
  radio.addEventListener('change', () => {
    document.querySelectorAll('.severity-btn').forEach(btn => {
      btn.className = 'severity-btn h-11 flex items-center justify-center gap-1.5 border-2 border-gray-200 rounded-xl text-xs font-semibold text-[#6B7280]';
      const dot = btn.querySelector('span');
      if (dot) dot.className = 'w-2.5 h-2.5 rounded-full ' + dot.className.split(' ').filter(c => c.startsWith('bg-')).join(' ');
    });
    const level = radio.value;
    const btn = radio.nextElementSibling;
    if (level === 'ringan') btn.className = 'severity-btn h-11 flex items-center justify-center gap-1.5 border-2 border-yellow-400 rounded-xl text-xs font-bold text-yellow-700 bg-yellow-50';
    if (level === 'sedang') btn.className = 'severity-btn h-11 flex items-center justify-center gap-1.5 border-2 border-orange-400 rounded-xl text-xs font-bold text-orange-700 bg-orange-50';
    if (level === 'parah')  btn.className = 'severity-btn h-11 flex items-center justify-center gap-1.5 border-2 border-red-500 rounded-xl text-xs font-bold text-red-700 bg-red-50';
  });
});
</script>
@endpush
