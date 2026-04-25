@extends('mobile.layout')
@section('title', 'Dashboard — Kepala Kandang')

@section('content')

<!-- TopAppBar -->
<header class="bg-[#FAFAF7] sticky top-0 z-50 border-b border-gray-200 flex items-center justify-between px-4 h-14">
  <div class="flex items-center gap-2.5">
    <div class="relative">
      <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center font-bold text-[#2E7D32] text-sm border border-green-200">
        {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name ?? 'KK', 0, 2)) }}
      </div>
      <div class="absolute -bottom-0.5 -right-0.5 bg-[#2E7D32] text-white text-[8px] font-bold px-1 rounded-full border border-white leading-3 py-0.5">KK</div>
    </div>
    <div>
      <p class="text-[11px] text-[#6B7280] font-medium">Kepala Kandang</p>
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

<!-- Flash -->
@if(session('success'))
<div class="mx-4 mt-3 bg-green-50 border border-green-200 rounded-xl p-3 text-sm text-green-800 font-medium">
  {{ session('success') }}
</div>
@endif

<!-- Greeting Banner -->
<section class="bg-[#2E7D32] px-5 pt-5 pb-8">
  <div class="flex justify-between items-start">
    <div>
      <span class="inline-block border border-white/40 text-white/90 text-[10px] font-bold px-2.5 py-0.5 rounded-full mb-2">Kepala Kandang</span>
      <h2 class="text-white text-xl font-bold">Selamat Pagi, {{ explode(' ', auth()->user()->nama ?? auth()->user()->name)[0] }} 👋</h2>
      <p class="text-white/70 text-xs mt-0.5">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
    </div>
    <span class="material-symbols-outlined text-white/20 text-6xl">agriculture</span>
  </div>
</section>

<!-- Validation Alert -->
@if($pendingValidasi > 0)
<div class="mx-4 -mt-4 bg-amber-50 border border-amber-200 border-l-4 border-l-amber-500 rounded-xl p-3.5 flex gap-3 items-start">
  <span class="material-symbols-outlined text-amber-600 text-xl mt-0.5">warning</span>
  <div class="flex-1">
    <p class="text-sm font-bold text-amber-800">{{ $pendingValidasi }} timbangan menunggu validasi Anda</p>
    <a href="{{ route('kk.validasi-timbangan') }}" class="inline-block mt-1.5 text-xs font-bold text-[#2E7D32]">Tinjau Sekarang →</a>
  </div>
</div>
@else
<div class="mx-4 -mt-4 bg-green-50 border border-green-200 rounded-xl p-3.5 flex gap-3 items-center">
  <span class="material-symbols-outlined text-[#2E7D32] text-xl">check_circle</span>
  <p class="text-sm font-semibold text-green-800">Semua timbangan sudah divalidasi</p>
</div>
@endif

<!-- Population Stats -->
<section class="px-4 mt-4">
  <p class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider mb-2">Populasi Ternak</p>
  <div class="grid grid-cols-4 gap-2">
    <div class="bg-white border border-gray-200 rounded-xl p-3 text-center shadow-sm">
      <p class="text-xl font-bold text-[#2E7D32]">{{ $totalAktif }}</p>
      <p class="text-[9px] text-[#6B7280] mt-0.5 leading-tight">Total Aktif</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-3 text-center shadow-sm">
      <p class="text-xl font-bold text-[#1F2937]">{{ $pejantan }}</p>
      <p class="text-[9px] text-[#6B7280] mt-0.5 leading-tight">Pejantan</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-3 text-center shadow-sm">
      <p class="text-xl font-bold text-[#1F2937]">{{ $indukan }}</p>
      <p class="text-[9px] text-[#6B7280] mt-0.5 leading-tight">Indukan</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-3 text-center shadow-sm">
      <p class="text-xl font-bold text-[#1F2937]">{{ $anakan }}</p>
      <p class="text-[9px] text-[#6B7280] mt-0.5 leading-tight">Anakan</p>
    </div>
  </div>
</section>

<!-- Health Summary -->
<section class="px-4 mt-3">
  <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
    <p class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider mb-1">Perhatian Kesehatan</p>
    <p class="text-2xl font-bold text-[#B14B6F]">{{ $healthAlerts }}</p>
    <p class="text-xs text-[#6B7280] mt-0.5">Domba sakit / dalam perawatan</p>
  </div>
</section>

<!-- Weight Trend Chart -->
<section class="px-4 mt-4">
  <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
    <p class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider mb-3">Tren Berat Rata-rata (6 Bulan)</p>
    <div class="h-36">
      <canvas id="weightChart"></canvas>
    </div>
  </div>
</section>

<!-- Reproduction Chart -->
<section class="px-4 mt-3">
  <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
    <p class="text-[10px] font-bold text-[#6B7280] uppercase tracking-wider mb-3">Reproduksi — Kelahiran 6 Bulan</p>
    <div class="h-36">
      <canvas id="reproChart"></canvas>
    </div>
  </div>
</section>

<!-- HPL Mendatang -->
@if($hplMendatang->isNotEmpty())
<section class="px-4 mt-4">
  <h3 class="text-sm font-bold text-[#1F2937] mb-3">HPL Mendatang</h3>
  <div class="bg-white border border-gray-200 rounded-xl divide-y divide-gray-100 shadow-sm">
    @foreach($hplMendatang as $hpl)
    <div class="flex items-center gap-3 p-3.5">
      <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center font-bold text-[#2E7D32] text-xs border border-green-200">
        {{ $hpl->indukan_tag }}
      </div>
      <div class="flex-1">
        <p class="text-sm font-bold text-[#1F2937]">{{ $hpl->indukan_tag }}{{ $hpl->nama ? ' · ' . $hpl->nama : '' }}</p>
        <p class="text-xs text-[#6B7280]">{{ \Carbon\Carbon::parse($hpl->tanggal_perkawinan)->diffInDays(today()) }} hari sejak kawin</p>
      </div>
      <div class="text-right">
        <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-lg">HPL: {{ \Carbon\Carbon::parse($hpl->estimasi_lahir)->format('d M') }}</span>
      </div>
    </div>
    @endforeach
  </div>
</section>
@endif

<!-- Supervisor Quick Actions -->
<section class="px-4 mt-4 mb-8">
  <h3 class="text-sm font-bold text-[#1F2937] mb-3">Aksi Supervisor</h3>
  <div class="grid grid-cols-2 gap-3">
    <a href="{{ route('kk.validasi-timbangan') }}" class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col items-center gap-2 shadow-sm active:scale-95 transition-transform">
      <div class="w-11 h-11 rounded-full bg-green-50 flex items-center justify-center">
        <span class="material-symbols-outlined text-[#2E7D32] text-2xl">fact_check</span>
      </div>
      <span class="text-xs font-bold text-[#1F2937] text-center">Validasi Timbangan</span>
    </a>
    <a href="{{ route('kk.monitor-tugas') }}" class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col items-center gap-2 shadow-sm active:scale-95 transition-transform">
      <div class="w-11 h-11 rounded-full bg-green-50 flex items-center justify-center">
        <span class="material-symbols-outlined text-[#2E7D32] text-2xl">monitoring</span>
      </div>
      <span class="text-xs font-bold text-[#1F2937] text-center">Monitor Tugas Tim</span>
    </a>
    <a href="{{ route('kk.kesehatan') }}" class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col items-center gap-2 shadow-sm active:scale-95 transition-transform">
      <div class="w-11 h-11 rounded-full bg-red-50 flex items-center justify-center">
        <span class="material-symbols-outlined text-[#B14B6F] text-2xl">medical_services</span>
      </div>
      <span class="text-xs font-bold text-[#1F2937] text-center">Laporan Kesehatan</span>
    </a>
    <a href="{{ route('kk.reproduksi') }}" class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col items-center gap-2 shadow-sm active:scale-95 transition-transform">
      <div class="w-11 h-11 rounded-full bg-green-50 flex items-center justify-center">
        <span class="material-symbols-outlined text-[#2E7D32] text-2xl">family_history</span>
      </div>
      <span class="text-xs font-bold text-[#1F2937] text-center">Data Reproduksi</span>
    </a>
  </div>
</section>

<!-- Bottom Nav -->
<nav class="fixed bottom-0 left-0 w-full max-w-[390px] bg-white border-t border-gray-200 flex justify-around items-center h-16 z-50 shadow-[0_-2px_8px_rgba(0,0,0,0.06)] left-1/2 -translate-x-1/2">
  <a href="{{ route('kk.dashboard') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 bg-[#2E7D32] text-white py-2 mx-1 rounded-xl">
    <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1">dashboard</span>
    <span class="text-[10px] font-bold">Beranda</span>
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
  <a href="{{ route('kk.validasi-timbangan') }}" class="flex flex-col items-center justify-center flex-1 gap-0.5 text-gray-500 py-2">
    <span class="material-symbols-outlined text-2xl">fact_check</span>
    <span class="text-[10px] font-medium">Validasi</span>
  </a>
</nav>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
  const weightData = @json($weightTrend);
  const reproData  = @json($reproTrend);

  // Fill 6-month labels
  const labels = [];
  for (let i = 5; i >= 0; i--) {
    const d = new Date();
    d.setMonth(d.getMonth() - i);
    labels.push(d.toLocaleString('id-ID', { month: 'short' }));
  }

  const getMonthKey = (offset) => {
    const d = new Date();
    d.setMonth(d.getMonth() - offset);
    return d.toISOString().slice(0, 7);
  };

  const wValues = [];
  const rValues = [];
  for (let i = 5; i >= 0; i--) {
    const key = getMonthKey(i);
    wValues.push(weightData[key] ?? null);
    rValues.push(reproData[key] ?? 0);
  }

  const chartOpts = {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { display: false }, ticks: { font: { size: 9 } } },
      y: { grid: { color: '#F3F4F6' }, ticks: { font: { size: 9 } } }
    }
  };

  new Chart(document.getElementById('weightChart'), {
    type: 'line',
    data: {
      labels,
      datasets: [{ data: wValues, borderColor: '#2E7D32', backgroundColor: 'rgba(46,125,50,0.08)', tension: 0.4, pointRadius: 3, fill: true, spanGaps: true }]
    },
    options: chartOpts
  });

  new Chart(document.getElementById('reproChart'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{ data: rValues, backgroundColor: '#2E7D32', borderRadius: 4 }]
    },
    options: chartOpts
  });
})();
</script>
@endpush
