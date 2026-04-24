@extends('layouts.app')
@section('page-title', 'Dashboard')

@section('content')

{{-- ═══════════════════════════════════════════════════════════
     WELCOME BAR
═══════════════════════════════════════════════════════════ --}}
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-[#1F2937] tracking-tight">
            Selamat datang, {{ auth()->user()->nama ?? 'Admin' }}
        </h1>
        <p class="text-sm text-gray-500 mt-0.5">
            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} &mdash; Ringkasan kondisi peternakan hari ini.
        </p>
    </div>
    <div class="hidden md:flex items-center gap-2 text-xs text-gray-400 bg-white border border-gray-200 rounded-lg px-4 py-2.5">
        <svg class="w-4 h-4 text-[#2E7D32]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Data diperbarui: {{ now()->format('H:i') }} WIB</span>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     ROW 1 — STAT CARDS
═══════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

    {{-- Total Populasi --}}
    <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-[#2E7D32]/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-[#2E7D32]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <span class="text-[10px] font-semibold uppercase tracking-wider text-[#2E7D32] bg-[#2E7D32]/10 px-2 py-1 rounded-full">Aktif</span>
        </div>
        <p class="text-3xl font-bold text-[#1F2937]">{{ number_format($totalAktif) }}</p>
        <p class="text-xs text-gray-500 mt-1 font-medium">Total Populasi Aktif</p>
        <div class="mt-3 pt-3 border-t border-gray-100">
            <p class="text-[11px] text-gray-400">Kapasitas kandang: <span class="font-semibold text-gray-600">{{ $persenOkupansi }}%</span></p>
        </div>
    </div>

    {{-- Pejantan --}}
    <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <span class="text-[10px] font-semibold uppercase tracking-wider text-blue-500 bg-blue-50 px-2 py-1 rounded-full">Jantan</span>
        </div>
        <p class="text-3xl font-bold text-[#1F2937]">{{ number_format($pejantan) }}</p>
        <p class="text-xs text-gray-500 mt-1 font-medium">Pejantan (Sire)</p>
        <div class="mt-3 pt-3 border-t border-gray-100">
            <p class="text-[11px] text-gray-400">
                {{ $totalAktif > 0 ? round(($pejantan / $totalAktif) * 100) : 0 }}% dari total populasi
            </p>
        </div>
    </div>

    {{-- Betina --}}
    <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-[#607F5B]/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-[#607F5B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <span class="text-[10px] font-semibold uppercase tracking-wider text-[#607F5B] bg-[#607F5B]/10 px-2 py-1 rounded-full">Betina</span>
        </div>
        <p class="text-3xl font-bold text-[#1F2937]">{{ number_format($betina) }}</p>
        <p class="text-xs text-gray-500 mt-1 font-medium">Betina (Ewe / Induk)</p>
        <div class="mt-3 pt-3 border-t border-gray-100">
            <p class="text-[11px] text-gray-400">
                {{ $totalAktif > 0 ? round(($betina / $totalAktif) * 100) : 0 }}% dari total populasi
            </p>
        </div>
    </div>

    {{-- Mortalitas --}}
    <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-[#B14B6F]/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-[#B14B6F]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            @if($mortalitasBulanIni > 0)
                <span class="text-[10px] font-semibold uppercase tracking-wider text-[#B14B6F] bg-[#B14B6F]/10 px-2 py-1 rounded-full">Waspada</span>
            @else
                <span class="text-[10px] font-semibold uppercase tracking-wider text-[#2E7D32] bg-[#2E7D32]/10 px-2 py-1 rounded-full">Aman</span>
            @endif
        </div>
        <p class="text-3xl font-bold {{ $mortalitasBulanIni > 0 ? 'text-[#B14B6F]' : 'text-[#1F2937]' }}">
            {{ $mortalitasBulanIni }}
        </p>
        <p class="text-xs text-gray-500 mt-1 font-medium">Mortalitas Bulan Ini</p>
        <div class="mt-3 pt-3 border-t border-gray-100">
            <p class="text-[11px] text-gray-400">{{ now()->locale('id')->isoFormat('MMMM YYYY') }}</p>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════
     ROW 2 — CHARTS: Pertumbuhan + Mortalitas
═══════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    {{-- Weight Growth Chart --}}
    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-semibold text-[#1F2937]">Tren Berat Badan Rata-rata</h3>
                <p class="text-xs text-gray-400 mt-0.5">6 bulan terakhir (kg)</p>
            </div>
            <span class="w-2.5 h-2.5 rounded-full bg-[#2E7D32]"></span>
        </div>
        <div class="h-52">
            <canvas id="chartPertumbuhan"></canvas>
        </div>
    </div>

    {{-- Mortality Chart --}}
    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-semibold text-[#1F2937]">Tingkat Mortalitas</h3>
                <p class="text-xs text-gray-400 mt-0.5">6 bulan terakhir (ekor)</p>
            </div>
            <span class="w-2.5 h-2.5 rounded-full bg-[#B14B6F]"></span>
        </div>
        <div class="h-52">
            <canvas id="chartMortalitas"></canvas>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════
     ROW 3 — CHARTS: Reproduksi + Konsumsi Pakan
═══════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    {{-- Reproduction Chart --}}
    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-semibold text-[#1F2937]">Performa Reproduksi</h3>
                <p class="text-xs text-gray-400 mt-0.5">Jumlah kelahiran per bulan (ekor)</p>
            </div>
            <span class="w-2.5 h-2.5 rounded-full bg-[#607F5B]"></span>
        </div>
        <div class="h-52">
            <canvas id="chartReproduksi"></canvas>
        </div>
    </div>

    {{-- Feed Consumption Chart --}}
    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-semibold text-[#1F2937]">Konsumsi Pakan Bulanan</h3>
                <p class="text-xs text-gray-400 mt-0.5">6 bulan terakhir (kg)</p>
            </div>
            <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
        </div>
        <div class="h-52">
            <canvas id="chartPakan"></canvas>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════
     ROW 4 — Distribusi Kategori + Kandang Occupancy + FCR
═══════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Donut: Distribusi per Kategori --}}
    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-[#1F2937] mb-1">Distribusi Populasi</h3>
        <p class="text-xs text-gray-400 mb-5">Per kategori domba</p>
        <div class="h-44 flex items-center justify-center">
            <canvas id="chartDistribusi"></canvas>
        </div>
        <div class="mt-4 grid grid-cols-2 gap-1.5">
            @foreach(['anak' => 'bg-amber-400', 'betina' => 'bg-[#607F5B]', 'induk' => 'bg-[#2E7D32]', 'pejantan' => 'bg-blue-400'] as $kat => $color)
                <div class="flex items-center gap-1.5 text-[11px] text-gray-500">
                    <span class="w-2 h-2 rounded-full {{ $color }} flex-shrink-0"></span>
                    <span class="capitalize">{{ $kat }}</span>
                    <span class="ml-auto font-semibold text-[#1F2937]">{{ $byKategori[$kat] ?? 0 }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Kandang Occupancy --}}
    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-[#1F2937] mb-1">Kapasitas Kandang</h3>
        <p class="text-xs text-gray-400 mb-5">Total kapasitas: {{ number_format($totalKapasitas) }} ekor</p>

        @forelse($kandangList as $kandang)
            @php
                $isi   = \App\Models\Domba::where('status','aktif')->where('kandang_id', $kandang->kandang_id)->count();
                $pct   = $kandang->kapasitas > 0 ? min(100, round(($isi / $kandang->kapasitas) * 100)) : 0;
                $color = $pct >= 90 ? '#B14B6F' : ($pct >= 70 ? '#F59E0B' : '#2E7D32');
            @endphp
            <div class="mb-4">
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-xs font-medium text-[#1F2937] truncate">{{ $kandang->nama_kandang }}</span>
                    <span class="text-[11px] text-gray-500 ml-2 whitespace-nowrap">{{ $isi }}/{{ $kandang->kapasitas }}</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500"
                         style="width: {{ $pct }}%; background-color: {{ $color }};"></div>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">{{ $pct }}% terisi</p>
            </div>
        @empty
            <p class="text-xs text-gray-400 italic">Belum ada data kandang.</p>
        @endforelse
    </div>

    {{-- FCR Summary --}}
    <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-[#1F2937] mb-1">Efisiensi Pakan (FCR)</h3>
        <p class="text-xs text-gray-400 mb-5">Feed Conversion Ratio — {{ now()->locale('id')->isoFormat('MMMM YYYY') }}</p>

        <div class="flex flex-col items-center justify-center py-4">
            @if($fcrValue !== null)
                <div class="w-24 h-24 rounded-full flex items-center justify-center border-4
                    {{ $fcrValue <= 6 ? 'border-[#2E7D32] text-[#2E7D32]' : ($fcrValue <= 9 ? 'border-amber-400 text-amber-500' : 'border-[#B14B6F] text-[#B14B6F]') }}">
                    <span class="text-3xl font-bold">{{ $fcrValue }}</span>
                </div>
                <p class="text-xs text-gray-500 mt-3 font-medium">
                    @if($fcrValue <= 6) Sangat Efisien
                    @elseif($fcrValue <= 9) Cukup Efisien
                    @else Perlu Perhatian
                    @endif
                </p>
                <p class="text-[11px] text-gray-400 mt-1">
                    FCR ideal domba: 5–7
                </p>
            @else
                <div class="w-24 h-24 rounded-full flex items-center justify-center border-4 border-dashed border-gray-200">
                    <span class="text-xs text-gray-400 text-center leading-tight px-2">Belum ada data</span>
                </div>
                <p class="text-xs text-gray-400 mt-3 text-center">Catat pakan &amp; timbangan<br>untuk melihat FCR.</p>
            @endif
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
            <div class="flex justify-between text-[11px]">
                <span class="text-gray-400">Pakan bulan ini</span>
                <span class="font-semibold text-[#1F2937]">{{ number_format($pakanBulanIni, 1) }} kg</span>
            </div>
            <div class="flex justify-between text-[11px]">
                <span class="text-gray-400">Total populasi aktif</span>
                <span class="font-semibold text-[#1F2937]">{{ number_format($totalAktif) }} ekor</span>
            </div>
        </div>
    </div>

</div>

@endsection

{{-- ═══════════════════════════════════════════════════════════
     CHART.JS SCRIPTS
═══════════════════════════════════════════════════════════ --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    'use strict';

    const labels     = @json($monthLabels);
    const GREEN      = '#2E7D32';
    const GREEN_PALE = 'rgba(46,125,50,0.12)';
    const BERRY      = '#B14B6F';
    const BERRY_PALE = 'rgba(177,75,111,0.12)';
    const EARTHY     = '#607F5B';
    const AMBER      = '#F59E0B';
    const AMBER_PALE = 'rgba(245,158,11,0.12)';

    const baseFont = { family: 'Inter, sans-serif', size: 11 };

    const defaultOptions = (yLabel = '') => ({
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1F2937',
                titleFont: { ...baseFont, weight: '600' },
                bodyFont: baseFont,
                padding: 10,
                cornerRadius: 8,
            },
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { font: baseFont, color: '#9CA3AF' },
            },
            y: {
                beginAtZero: true,
                grid: { color: '#F3F4F6' },
                ticks: { font: baseFont, color: '#9CA3AF' },
                title: { display: !!yLabel, text: yLabel, font: baseFont, color: '#9CA3AF' },
            },
        },
    });

    // ── Weight Growth (line) ────────────────────────────────────
    new Chart(document.getElementById('chartPertumbuhan'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                data: @json($pertumbuhanData),
                borderColor: GREEN,
                backgroundColor: GREEN_PALE,
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: GREEN,
                fill: true,
                tension: 0.4,
            }],
        },
        options: defaultOptions('kg'),
    });

    // ── Mortality (bar) ─────────────────────────────────────────
    new Chart(document.getElementById('chartMortalitas'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                data: @json($mortalitasData),
                backgroundColor: BERRY_PALE,
                borderColor: BERRY,
                borderWidth: 1.5,
                borderRadius: 6,
            }],
        },
        options: defaultOptions('ekor'),
    });

    // ── Reproduction (bar) ──────────────────────────────────────
    new Chart(document.getElementById('chartReproduksi'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                data: @json($reproduksiData),
                backgroundColor: 'rgba(96,127,91,0.15)',
                borderColor: EARTHY,
                borderWidth: 1.5,
                borderRadius: 6,
            }],
        },
        options: defaultOptions('ekor'),
    });

    // ── Feed Consumption (line) ─────────────────────────────────
    new Chart(document.getElementById('chartPakan'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                data: @json($pakanData),
                borderColor: AMBER,
                backgroundColor: AMBER_PALE,
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: AMBER,
                fill: true,
                tension: 0.4,
            }],
        },
        options: defaultOptions('kg'),
    });

    // ── Distribusi Kategori (doughnut) ──────────────────────────
    new Chart(document.getElementById('chartDistribusi'), {
        type: 'doughnut',
        data: {
            labels: ['Anak', 'Betina', 'Induk', 'Pejantan'],
            datasets: [{
                data: @json($kategoriData),
                backgroundColor: [AMBER, EARTHY, GREEN, '#60A5FA'],
                borderWidth: 0,
                hoverOffset: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1F2937',
                    titleFont: { ...baseFont, weight: '600' },
                    bodyFont: baseFont,
                    padding: 10,
                    cornerRadius: 8,
                },
            },
        },
    });
})();
</script>
@endpush
