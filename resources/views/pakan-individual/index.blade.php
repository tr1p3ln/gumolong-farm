@extends('layouts.app')

@section('page-title', 'Pakan Individual & FCR')

@section('content')
@include('pakan-individual.partials.detail-pakan')
@include('pakan-individual.partials.catat-pakan')



{{-- ══ PAGE HEADER ══ --}}
<div class="flex items-center justify-between mb-5">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Pakan Individual & FCR</h1>
        <p class="text-xs text-gray-400 mt-0.5">Dashboard monitoring efisiensi pakan & feed conversion ratio</p>
    </div>
    {{-- Tombol header: buka modal tanpa pre-fill --}}
    <button onclick="openCatatPakan()"
        class="inline-flex items-center gap-2 bg-primary hover:opacity-90 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Catat Pemberian Pakan
    </button>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-lg px-4 py-3 mb-4 text-sm text-green-800">
        <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

{{-- ══ METRIC CARDS ══ --}}
<div class="grid grid-cols-3 gap-4 mb-5">

    {{-- Rata-rata FCR --}}
    <div class="bg-gray-50 rounded-lg p-4">
        <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 mb-2">Rata-rata FCR Farm</p>
        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-bold text-gray-800">{{ number_format($avgFcr, 2) }}</span>
            <span class="text-xs font-semibold text-blue-600">Farm Avg</span>
        </div>
        <div
        class="h-full rounded-full bg-blue-500 transition-all"
        @style([
            'width: '.min(100, (($avgFcr ?? 0) / 12) * 100).'%',
        ])
    ></div>
        <p class="text-[11px] text-gray-400 mt-2">FCR = Total pakan (kg) / pertambahan bobot (kg)</p>
    </div>

    {{-- FCR Terbaik --}}
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">FCR Terbaik</p>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">Efisien</span>
        </div>
        @if($fcrTerbaik)
        <div class="flex items-center gap-3">
            <div class="px-2.5 py-1.5 bg-green-100 text-green-700 font-mono text-xs font-bold rounded-md">
                {{ $fcrTerbaik->ear_tag_id }}
            </div>
            <div>
                <div class="text-2xl font-bold text-green-600">{{ number_format($fcrTerbaik->fcr, 2) }}</div>
                <div class="text-xs text-gray-400">{{ $fcrTerbaik->nama }} ({{ ucfirst($fcrTerbaik->kategori) }})</div>
            </div>
        </div>
        <div class="mt-3">
            <button
                data-domba='@json(["ear_tag_id" => $fcrTerbaik->ear_tag_id, "nama" => $fcrTerbaik->nama, "kategori" => $fcrTerbaik->kategori])'
                onclick="openDetailPakanFromBtn(this)"
                class="text-xs text-gray-500 hover:text-primary transition">
                Lihat Detail →
            </button>
        </div>
        @else
        <p class="text-sm text-gray-400 italic">Belum ada data</p>
        @endif
    </div>

    {{-- FCR Terburuk --}}
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">FCR Terburuk</p>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700">Boros</span>
        </div>
        @if($fcrTerburuk)
        <div class="flex items-center gap-3">
            <div class="px-2.5 py-1.5 bg-red-100 text-red-700 font-mono text-xs font-bold rounded-md">
                {{ $fcrTerburuk->ear_tag_id }}
            </div>
            <div>
                <div class="text-2xl font-bold text-red-600">{{ number_format($fcrTerburuk->fcr, 2) }}</div>
                <div class="text-xs text-gray-400">{{ $fcrTerburuk->nama }} ({{ ucfirst($fcrTerburuk->kategori) }})</div>
            </div>
        </div>
        <div class="mt-3">
            @php $terburukJson = json_encode(['ear_tag_id' => $fcrTerburuk->ear_tag_id, 'nama' => $fcrTerburuk->nama, 'kategori' => $fcrTerburuk->kategori]); @endphp
            <button data-domba='@json(["ear_tag_id" => $fcrTerburuk->ear_tag_id, "nama" => $fcrTerburuk->nama, "kategori" => $fcrTerburuk->kategori])'
                onclick="openDetailPakanFromBtn(this)"
                class="text-xs text-gray-500 hover:text-primary transition">
                Lihat Detail →
            </button>
        </div>
        @else
        <p class="text-sm text-gray-400 italic">Belum ada data</p>
        @endif
    </div>

</div>

{{-- ══ MAIN CONTENT ══ --}}
<div class="grid gap-4" style="grid-template-columns: 1fr 280px; align-items: start;">

    {{-- TABEL --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">

        {{-- Filter --}}
        <div class="px-5 py-4 border-b border-gray-100">
            <form method="GET" action="{{ route('pakan-individual.index') }}" class="flex flex-wrap gap-3 items-center">
                <div class="flex-1 min-w-[200px] relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari ear tag atau nama..."
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary placeholder-gray-400">
                </div>
                <select name="kategori" class="border border-gray-300 rounded-md px-3 py-2 text-sm bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary min-w-[150px]">
                    <option value="">Semua Kategori</option>
                    <option value="pejantan" @selected(request('kategori')==='pejantan')>Pejantan</option>
                    <option value="indukan"  @selected(request('kategori')==='indukan')>Indukan</option>
                    <option value="cempe"    @selected(request('kategori')==='cempe')>Cempe</option>
                </select>
                <select name="status_fcr" class="border border-gray-300 rounded-md px-3 py-2 text-sm bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary min-w-[150px]">
                    <option value="">Semua Status FCR</option>
                    <option value="efisien" @selected(request('status_fcr')==='efisien')>Efisien</option>
                    <option value="normal"  @selected(request('status_fcr')==='normal')>Normal</option>
                    <option value="boros"   @selected(request('status_fcr')==='boros')>Boros</option>
                </select>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm bg-white text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary">
                <button type="submit"
                    class="px-4 py-2 bg-primary hover:opacity-90 text-white text-sm font-semibold rounded-md transition">
                    Cari
                </button>
                @if(request()->hasAny(['search','kategori','status_fcr','tanggal']))
                    <a href="{{ route('pakan-individual.index') }}"
                        class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-md hover:bg-gray-50 transition">Reset</a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ear Tag</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ras</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Berat (kg)</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pakan Hari Ini</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total 30hr (kg)</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">FCR</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rekomendasi</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                @forelse($dombaFcr as $row)
                    @php
                        $fcrStatus = match(true) {
                            $row->fcr === null    => ['label' => '—',              'class' => 'bg-gray-100 text-gray-500',    'row' => ''],
                            $row->fcr < 5.0       => ['label' => 'Sangat Efisien', 'class' => 'bg-green-100 text-green-700',  'row' => ''],
                            $row->fcr <= 7.0      => ['label' => 'Normal',         'class' => 'bg-blue-100 text-blue-700',    'row' => ''],
                            $row->fcr <= 9.0      => ['label' => 'Kurang Efisien', 'class' => 'bg-yellow-100 text-yellow-700','row' => ''],
                            default               => ['label' => 'Perlu Evaluasi', 'class' => 'bg-red-100 text-red-700',      'row' => 'bg-red-50/40'],
                        };
                    
                        $rekomendasiLabel = match(true) {
                            $row->fcr === null => '—',
                            $row->fcr < 5.0    => 'Pertahankan',
                            $row->fcr <= 7.0   => 'Stabil',
                            $row->fcr <= 9.0   => 'Kurangi Pakan',
                            default            => 'Review Pakan',
                        };
                    
                        $rekomendasiClass = match(true) {
                            $row->fcr === null => 'text-gray-400',
                            $row->fcr < 5.0    => 'text-gray-500',
                            $row->fcr <= 7.0   => 'text-gray-500',
                            $row->fcr <= 9.0   => 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700',
                            default            => 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors {{ $fcrStatus['row'] }}">
                        <td class="px-4 py-3 font-mono text-sm font-semibold text-gray-800">{{ $row->ear_tag_id }}</td>
                        <td class="px-4 py-3 text-gray-800">{{ $row->nama }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                {{ ucfirst($row->kategori) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $row->ras }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $row->berat_kg ? number_format($row->berat_kg, 1) : '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $row->pakan_hari_ini ? number_format($row->pakan_hari_ini / 1000, 1) . ' kg' : '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $row->total_pakan_30hr ? number_format($row->total_pakan_30hr / 1000, 1) : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($row->fcr !== null)
                            <div class="font-semibold {{
                                $row->fcr < 5   ? 'text-green-600'  :
                                ($row->fcr <= 7  ? 'text-blue-600'   :
                                ($row->fcr <= 9  ? 'text-yellow-600' : 'text-red-600'))
                            }}">
                                {{ number_format($row->fcr, 2) }}{{ $row->fcr > 9 ? '!' : '' }}
                            </div>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $fcrStatus['class'] }}">
                                {{ $fcrStatus['label'] }}
                            </span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="{{ $rekomendasiClass }} text-sm">{{ $rekomendasiLabel }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="inline-flex items-center gap-1.5">
                        @php $rowData = ['ear_tag_id' => $row->ear_tag_id, 'nama' => $row->nama, 'kategori' => $row->kategori, 'ras' => $row->ras ?? '', 'berat_kg' => $row->berat_kg]; @endphp
                            <button
                                data-domba='@json($rowData)'
                                onclick="openDetailPakanFromBtn(this)"
                                class="text-xs text-gray-600 border border-gray-300 hover:bg-gray-100 px-2.5 py-1 rounded transition">
                                Detail
                            </button>
                                <button
                                    onclick="openCatatPakan({
                                        ear_tag_id: '{{ $row->ear_tag_id }}',
                                        nama:       '{{ addslashes($row->nama) }}',
                                        kategori:   '{{ ucfirst($row->kategori) }}'
                                    })"
                                    class="text-xs text-primary border border-primary/30 hover:bg-primary/10 px-2.5 py-1 rounded transition font-semibold">
                                    + Catat
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-gray-400 text-sm">Belum ada data pemberian pakan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100">
            <p class="text-sm text-gray-500">
                @if($dombaFcr->total() > 0)
                    Showing {{ $dombaFcr->firstItem() }}–{{ $dombaFcr->lastItem() }} of {{ $dombaFcr->total() }} sheep
                @else
                    No data found
                @endif
            </p>
            @if($dombaFcr->hasPages())
                <div>{{ $dombaFcr->links() }}</div>
            @endif
        </div>
    </div>

    {{-- SIDEBAR --}}
    <div style="display:flex;flex-direction:column;gap:12px;">

        {{-- Rekomendasi Pakan --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-xs font-semibold text-gray-700">Rekomendasi Pakan</span>
            </div>

            {{-- Domba Selector --}}
            <div class="px-4 py-3 border-b border-gray-100">
                <div class="flex items-center gap-2 bg-green-50 rounded-md px-3 py-2">
                    <div class="w-5 h-5 rounded bg-primary flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-[9px] font-bold">B</span>
                    </div>
                    <select id="rekomenDombaSelect"
                        class="flex-1 bg-transparent text-xs font-semibold text-green-800 border-none outline-none cursor-pointer"
                        onchange="loadRekomendasi(this.value)">
                        @foreach($dombaList as $d)
                            <option value="{{ $d->ear_tag_id }}">{{ $d->ear_tag_id }} {{ $d->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Breakdown pakan --}}
            <div id="rekomendasiContent" class="px-4 py-3 space-y-3">
                @foreach($rekomendasiPakan as $item)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs text-gray-600">{{ ucfirst($item->jenis) }}</span>
                        <span class="text-xs font-semibold text-gray-800">{{ number_format($item->total_gram) }}g</span>
                    </div>
                    <div
                    class="h-full rounded-full transition-all"
                    @style([
                        'width: '.$item->persen.'%',
                        'background: '.$item->warna,
                    ])
                ></div>
                    <div class="text-right text-[10px] text-gray-400 mt-0.5">{{ number_format($item->persen, 0) }}%</div>
                </div>
                @endforeach

                <div class="border-t border-gray-100 pt-3 flex justify-between items-center">
                    <span class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Total Harian</span>
                    <span class="text-sm font-bold text-gray-800">{{ number_format($totalPakanHarian) }} g/hari</span>
                </div>
            </div>
        </div>

        {{-- Keterangan Status FCR --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-xs font-semibold text-gray-700">Keterangan Status FCR</span>
            </div>
            <div class="space-y-2">
            <div class="flex justify-between items-center text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                    <span class="text-gray-700">Sangat Efisien</span>
                </div>
                <span class="font-mono text-gray-500">&lt; 5.0</span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>
                    <span class="text-gray-700">Normal</span>
                </div>
                <span class="font-mono text-gray-500">5.0 – 7.0</span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-yellow-500 inline-block"></span>
                    <span class="text-gray-700">Kurang Efisien</span>
                </div>
                <span class="font-mono text-gray-500">7.0 – 9.0</span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>
                    <span class="text-gray-700">Perlu Evaluasi</span>
                </div>
                <span class="font-mono text-gray-500">&gt; 9.0</span>
            </div>
        </div>

            <p class="text-[10px] text-gray-400 mt-3 pt-3 border-t border-gray-100">
                * Nilai FCR rendah menunjukkan efisiensi pakan yang lebih baik.
            </p>
        </div>

    </div>
</div>

<script>
// loadRekomendasi sidebar — AJAX update breakdown pakan per domba
function loadRekomendasi(earTagId) {
    fetch(`/pakan-individual/${earTagId}/stats`)
        .then(r => r.json())
        .then(data => {
            if (!data.rekomendasi || !data.rekomendasi.length) {
                document.getElementById('rekomendasiContent').innerHTML =
                    '<p class="text-xs text-gray-400 italic py-2">Belum ada data pakan.</p>';
                return;
            }

            const warnaMap = {
                rumput: '#639922', konsentrat: '#378ADD',
                silase: '#BA7517', dedak: '#888780', ampas_tahu: '#888780'
            };

            let html = '';
            data.rekomendasi.forEach(item => {
                const warna = warnaMap[item.jenis] ?? '#aaaaaa';
                html += `
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs text-gray-600">${item.jenis.charAt(0).toUpperCase() + item.jenis.slice(1)}</span>
                        <span class="text-xs font-semibold text-gray-800">${Number(item.total_gram).toLocaleString('id-ID')}g</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                        <div class="h-full rounded-full transition-all" style="width:${item.persen}%;background:${warna}"></div>
                    </div>
                    <div class="text-right text-[10px] text-gray-400 mt-0.5">${item.persen}%</div>
                </div>`;
            });

            const total = data.total_ideal_gram ?? 0;
            html += `
            <div class="border-t border-gray-100 pt-3 flex justify-between items-center">
                <span class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Total Harian</span>
                <span class="text-sm font-bold text-gray-800">${Number(total).toLocaleString('id-ID')} g/hari</span>
            </div>`;

            document.getElementById('rekomendasiContent').innerHTML = html;
        })
        .catch(() => {});
}

function openDetailPakanFromBtn(btn) {
    const domba = JSON.parse(btn.dataset.domba);
    openDetailPakan(domba.ear_tag_id, domba);
}
</script>

@endsection