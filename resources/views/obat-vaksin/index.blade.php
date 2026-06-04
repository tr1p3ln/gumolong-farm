@extends('layouts.app')

@section('page-title', 'Obat & Vaksin')

@section('content')

{{-- ══ SEMUA MODAL ══ --}}
{{-- Modal Tambah --}}
<div id="modalTambahObat"
    class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-xl shadow-xl flex flex-col" style="max-height:90vh;">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h2 class="text-base font-bold text-gray-800">Tambah Obat / Vaksin</h2>
                <p class="text-[11px] text-gray-400 mt-0.5 font-mono">UC-02-01 | KAMUS DATA: TABEL OBAT_VAKSIN</p>
            </div>
            <button onclick="closeTambahObatModal()"
                class="w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 transition text-xl leading-none">&times;</button>
        </div>
        <div class="flex flex-col flex-1 min-h-0 overflow-hidden">
            @include('obat-vaksin.partials.tambah-obat')
        </div>
    </div>
</div>

{{-- Modal Lihat --}}
@include('obat-vaksin.partials.lihat-obat')

{{-- Modal Edit --}}
<div id="modalEditObat"
    class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-xl shadow-xl flex flex-col" style="max-height:90vh;">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h2 class="text-base font-bold text-gray-800">Edit Obat / Vaksin</h2>
                <p class="text-[11px] text-gray-400 mt-0.5 font-mono" id="editModalSubtitle">—</p>
            </div>
            <button onclick="closeEditObatModal()"
                class="w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 transition text-xl leading-none">&times;</button>
        </div>
        <div id="editLoadingState" class="flex-1 flex items-center justify-center py-16">
            <div class="flex flex-col items-center gap-3">
                <svg class="animate-spin w-8 h-8 text-primary" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <p class="text-sm text-gray-400">Memuat data...</p>
            </div>
        </div>
        <div id="editFormWrapper" class="flex flex-col flex-1 min-h-0 overflow-hidden hidden">
            @include('obat-vaksin.partials.edit-obat')
        </div>
    </div>
</div>

{{-- Modal Hapus --}}
@include('obat-vaksin.partials.hapus-obat')

{{-- Modal Catat Pakai --}}
<div id="modalCatatPakai"
    class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-2xl rounded-xl shadow-xl flex flex-col" style="max-height: 90vh; height: 90vh; overflow: hidden;">

        {{-- HEADER --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h2 class="text-base font-bold text-gray-800">Catat Penggunaan Obat / Vaksin</h2>
                <p class="text-[11px] text-gray-400 mt-0.5 font-mono">UC-02-05 | KAMUS DATA: TABEL PEMAKAIAN_OBAT</p>
            </div>
            <button onclick="closeCatatPakaiModal()"
                class="w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 transition text-xl leading-none">&times;</button>
        </div>

        {{-- BODY (scrollable via partial) --}}
        <div class="flex flex-col flex-1 min-h-0 overflow-hidden">
            @include('obat-vaksin.partials.catat-obat')
        </div>

    </div>
</div>

{{-- ══ PAGE HEADER ══ --}}
<div class="flex items-center justify-between mb-5">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Obat & Vaksin</h1>
        <p class="text-xs text-gray-400 mt-0.5">
            Kamus Data: tabel <span class="font-mono bg-gray-100 px-1 rounded">OBAT_VAKSIN</span>
            — ENUM tipe: <span class="text-gray-500">obat, vaksin, vitamin</span>
        </p>
    </div>
    <button onclick="openTambahObatModal()"
        class="inline-flex items-center gap-2 bg-primary hover:opacity-90 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Obat / Vaksin
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
@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-4 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
    <script>document.addEventListener('DOMContentLoaded', () => openTambahObatModal())</script>
@endif


{{-- ══ MAIN CARD ══ --}}
<div class="bg-white rounded-lg shadow-sm overflow-hidden">

    <div class="border-b border-gray-200 px-6">
        <nav class="flex gap-6 -mb-px">
            <button onclick="switchTab('daftar-stok')" id="tab-daftar-stok"
                class="tab-btn py-4 text-sm font-medium border-b-2 border-primary text-primary whitespace-nowrap transition-colors">Daftar Stok</button>
            <button onclick="switchTab('riwayat-pemakaian')" id="tab-riwayat-pemakaian"
                class="tab-btn py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap transition-colors">Riwayat Pemakaian</button>
            <button onclick="switchTab('jadwal-vaksinasi')" id="tab-jadwal-vaksinasi"
                class="tab-btn py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap transition-colors">Jadwal Vaksinasi</button>
        </nav>
    </div>

    <div id="content-daftar-stok" class="tab-content p-6">

        <form method="GET" action="{{ route('obat-vaksin.index') }}" class="flex flex-wrap gap-3 items-center mb-5">
            <div class="flex-1 min-w-[200px] relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama obat atau vaksin..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary placeholder-gray-400">
            </div>
            <select name="tipe" class="border border-gray-300 rounded-md px-3 py-2 text-sm bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary min-w-[140px]">
                <option value="">Semua Tipe</option>
                <option value="vitamin" @selected(request('tipe')==='vitamin')>Vitamin</option>
                <option value="vaksin"  @selected(request('tipe')==='vaksin')>Vaksin</option>
                <option value="obat"    @selected(request('tipe')==='obat')>Obat</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-primary hover:opacity-90 text-white text-sm font-semibold rounded-md transition">Cari</button>
            @if(request()->hasAny(['search','tipe']))
                <a href="{{ route('obat-vaksin.index') }}"
                    class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-md hover:bg-gray-50 transition">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Obat/Vaksin</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok Min</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl Expired</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                @forelse($obatVaksin as $item)
                    @php
                        $badge     = $item->status_badge;
                        $tipeBadge = match($item->tipe) {
                            'vaksin'  => 'bg-blue-100 text-blue-700',
                            'vitamin' => 'bg-purple-100 text-purple-700',
                            default   => 'bg-amber-100 text-amber-700',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $item->formatted_id }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $item->nama_obat }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $tipeBadge }}">
                                {{ ucfirst($item->tipe) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $item->stok }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ ucfirst($item->satuan) }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $item->stok_minimum }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            @if($item->tanggal_expired)
                                <span class="{{ $item->status === 'expired' ? 'line-through text-red-400' : '' }}">
                                    {{ $item->tanggal_expired->format('d M Y') }}
                                </span>
                                @if($item->status === 'expired')
                                    <span class="text-red-400 ml-1 text-xs">✕</span>
                                @endif
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badge['class'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="inline-flex items-center gap-1.5">
                                <button
                                    data-id="{{ $item->obat_id }}"
                                    onclick="openLihatObatModal(this.dataset.id)"
                                    class="text-xs text-gray-600 border border-gray-300 hover:bg-gray-100 px-2.5 py-1 rounded transition">
                                    Lihat
                                </button>
                                <button
                                    data-id="{{ $item->obat_id }}"
                                    onclick="openEditObatModal(this.dataset.id)"
                                    class="text-xs text-primary border border-primary/30 hover:bg-primary/10 px-2.5 py-1 rounded transition font-semibold">
                                    Edit
                                </button>
                                <button
                                    data-id="{{ $item->obat_id }}"
                                    onclick="openCatatPakaiModal(this.dataset.id)"
                                    title="Catat Pemakaian"
                                    class="w-8 h-8 flex items-center justify-center text-green-700 border border-green-300 hover:bg-green-50 rounded transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.586-9.414a2 2 0 112.828 2.828L12 14l-4 1 1-4 8.414-8.414z"/>
                                    </svg>
                                </button>
                                <button
                                    data-id="{{ $item->obat_id }}"
                                    data-nama="{{ $item->nama_obat }}"
                                    data-formatted="{{ $item->formatted_id }}"
                                    onclick="openHapusObatModal(this.dataset.id, this.dataset.nama, this.dataset.formatted)"
                                    class="text-xs text-accent border border-accent/30 hover:bg-red-50 px-2.5 py-1 rounded transition font-semibold">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-gray-400 text-sm">Belum ada data obat/vaksin.</p>
                                <button onclick="openTambahObatModal()" class="text-xs text-primary font-semibold hover:underline">+ Tambah data pertama</button>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
            <p class="text-sm text-gray-500">
                @if($obatVaksin->total() > 0)
                    Menampilkan {{ $obatVaksin->firstItem() }}–{{ $obatVaksin->lastItem() }} dari {{ $obatVaksin->total() }} item
                @else Tidak ada item
                @endif
            </p>
            @if($obatVaksin->hasPages())<div>{{ $obatVaksin->links() }}</div>@endif
        </div>
    </div>

    <div id="content-riwayat-pemakaian" class="tab-content hidden p-6">

    @if($riwayatPemakaian->isEmpty())
        <div class="py-16 text-center">
            <svg class="mx-auto w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-400 text-sm">Belum ada riwayat pemakaian.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Obat / Vaksin</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Domba</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rekam ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cara Pemberian</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Catatan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($riwayatPemakaian as $row)
                        @php
                            $tipeBadge = match($row->tipe) {
                                'vaksin'  => 'bg-blue-100 text-blue-700',
                                'vitamin' => 'bg-purple-100 text-purple-700',
                                default   => 'bg-amber-100 text-amber-700',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($row->tanggal_pakai)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-800">{{ $row->nama_obat }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $tipeBadge }}">
                                        {{ ucfirst($row->tipe) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-mono text-sm font-semibold text-green-700">
                                {{ $row->ear_tag_id }}
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-500">
                                RM-{{ str_pad($row->rekam_id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $row->jumlah }} {{ $row->satuan }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $row->cara_pemberian ?: '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate">
                                {{ $row->catatan ?: '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
            <p class="text-sm text-gray-500">
                Menampilkan {{ $riwayatPemakaian->firstItem() }}–{{ $riwayatPemakaian->lastItem() }}
                dari {{ $riwayatPemakaian->total() }} riwayat
            </p>
            @if($riwayatPemakaian->hasPages())
                <div>{{ $riwayatPemakaian->links() }}</div>
            @endif
        </div>
    @endif

</div>
    <div id="content-jadwal-vaksinasi" class="tab-content hidden p-6">

    @if($jadwalVaksinasi->isEmpty())
        <div class="py-16 text-center">
            <svg class="mx-auto w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-400 text-sm">Belum ada jadwal vaksinasi.</p>
            <p class="text-gray-300 text-xs mt-1">Jadwal muncul otomatis setelah pemakaian obat/vaksin dengan interval dicatat.</p>
        </div>
    @else
        {{-- Legend --}}
        <div class="flex flex-wrap gap-3 mb-4">
            @foreach([
                ['jatuh_tempo', 'bg-red-100 text-red-700',    'Jatuh Tempo'],
                ['mendekati',   'bg-orange-100 text-orange-700', 'Mendekati (≤7 hari)'],
                ['segera',      'bg-yellow-100 text-yellow-700', 'Segera (≤30 hari)'],
                ['aman',        'bg-green-100 text-green-700',  'Aman'],
            ] as [$key, $cls, $label])
                <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full {{ $cls }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>{{ $label }}
                </span>
            @endforeach
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Domba</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Obat / Vaksin</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Terakhir Diberikan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Interval</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jadwal Berikutnya</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($jadwalVaksinasi as $row)
                        @php
                            $statusConfig = match($row->status_jadwal) {
                                'jatuh_tempo' => ['bg-red-100 text-red-700',       'Jatuh Tempo'],
                                'mendekati'   => ['bg-orange-100 text-orange-700', 'Mendekati'],
                                'segera'      => ['bg-yellow-100 text-yellow-700', 'Segera'],
                                default       => ['bg-green-100 text-green-700',   'Aman'],
                            };
                            [$badgeClass, $badgeLabel] = $statusConfig;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-mono font-semibold text-green-700">
                                {{ $row->ear_tag_id }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-gray-800">
                                {{ $row->nama_obat }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ \Carbon\Carbon::parse($row->tanggal_pakai)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                Setiap {{ $row->interval_vaksinasi }} bulan
                            </td>
                            <td class="px-4 py-3 font-semibold text-gray-800">
                                {{ $row->tanggal_berikutnya }}
                                @if($row->hari_lagi >= 0)
                                    <span class="text-xs text-gray-400 font-normal ml-1">({{ $row->hari_lagi }} hari lagi)</span>
                                @else
                                    <span class="text-xs text-red-400 font-normal ml-1">({{ abs($row->hari_lagi) }} hari lalu)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                    {{ $badgeLabel }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p class="text-xs text-gray-400 mt-4">
            * Jadwal dihitung dari pemakaian terakhir setiap obat/vaksin per domba.
        </p>
    @endif

</div>
</div>

<script>
    function openModal(id)  { const m=document.getElementById(id); m.classList.remove('hidden'); m.classList.add('flex'); document.body.style.overflow='hidden'; }
    function closeModal(id) { const m=document.getElementById(id); m.classList.add('hidden'); m.classList.remove('flex'); document.body.style.overflow=''; }

    [
        'modalTambahObat',
        'modalLihatObat',
        'modalEditObat',
        'modalHapusObat',
        'modalCatatPakai'
    ].forEach(id => {
        document.getElementById(id)?.addEventListener('click', function(e) {
            if (e.target === this) closeModal(id);
        });
    });

    // ── Tambah ──
    function openTambahObatModal()  { openModal('modalTambahObat'); }
    function closeTambahObatModal() { closeModal('modalTambahObat'); }

   // ── CATAT PAKAI MODAL ──────────────────────────────────────────────

        function openCatatPakaiModal(obatId) {
        // Reset dulu sebelum buka
        clearRekamSelection();
        document.getElementById('formCatatObat').reset();
        document.getElementById('catat_obat_id').value = obatId;

        fetch(`/obat-vaksin/${obatId}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('catat_nama_obat').value = data.nama_obat;
            document.getElementById('catat_tipe_sediaan').textContent = 'Tipe sediaan: ' + data.satuan;
            document.getElementById('catat_satuan_label').textContent = data.satuan;
        })
        .catch(err => console.error('Error loading obat:', err));

        openModal('modalCatatPakai');
    }

    function closeCatatPakaiModal() {
        const modal = document.getElementById('modalCatatPakai');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        
        // Reset form dan dropdown
        document.getElementById('formCatatObat').reset();
        document.getElementById('rekamSearchInput').value = '';
        document.getElementById('rekamDropdown').style.display = 'none';
        document.getElementById('rekamSelectedCard').style.display = 'none';
        clearRekamSelection();
    }

    // ── SEARCH REKAM MEDIS ─────────────────────────────────────────────

    let rekamSearchTimeout = null;

    function catatRekamSearch(query) {
        clearTimeout(rekamSearchTimeout);
        query = query ? query.trim() : '';

        const dropdown = document.getElementById('rekamDropdown');

        if (query.length < 1) {
            dropdown.style.display = 'none';
            return;
        }

        rekamSearchTimeout = setTimeout(() => {
            fetch(`/obat-vaksin/search-rekam?q=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => renderRekamDropdown(data))
            .catch(() => {
                dropdown.innerHTML = '<div style="padding:12px 16px;font-size:13px;color:#9ca3af;font-style:italic;">Gagal memuat data.</div>';
                dropdown.style.display = 'block';
            });
        }, 300);
    }

    function renderRekamDropdown(items) {
        const dropdown = document.getElementById('rekamDropdown');

        if (!items.length) {
            dropdown.innerHTML = '<div style="padding:12px 16px;font-size:13px;color:#9ca3af;font-style:italic;">Rekam medis tidak ditemukan.</div>';
            dropdown.style.display = 'block';
            return;
        }

        const statusColors = {
            sakit:           { bg:'#fee2e2', color:'#991b1b' },
            dalam_perawatan: { bg:'#fef3c7', color:'#92400e' },
            sembuh:          { bg:'#dcfce7', color:'#166534' },
        };

        dropdown.innerHTML = items.map(item => {
            const sc = statusColors[item.status] ?? { bg:'#f3f4f6', color:'#374151' };
            const statusLabel = item.status.replace('_', ' ').toUpperCase();
            const gejalaShort = item.gejala ? (item.gejala.length > 50 ? item.gejala.slice(0, 50) + '…' : item.gejala) : '—';
            return `
                <div onclick='selectRekam(${JSON.stringify(item)})'
                    style="padding:10px 14px;cursor:pointer;border-bottom:1px solid #f3f4f6;display:flex;flex-direction:column;gap:3px;"
                    onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="font-size:12px;font-weight:600;color:#111827;font-family:monospace;">RM-${String(item.rekam_id).padStart(4,'0')}</span>
                        <span style="font-size:12px;font-weight:600;color:#15803d;font-family:monospace;">${item.ear_tag_id}</span>
                        <span style="font-size:10px;font-weight:600;padding:2px 7px;border-radius:20px;background:${sc.bg};color:${sc.color};margin-left:auto;">${statusLabel}</span>
                    </div>
                    <span style="font-size:11px;color:#9ca3af;">${item.tanggal_sakit} &nbsp;·&nbsp; ${gejalaShort}</span>
                </div>`;
        }).join('');
        dropdown.style.display = 'block';
    }

    function selectRekam(item) {
        document.getElementById('catat_rekam_id').value = item.rekam_id;
        document.getElementById('catat_ear_tag').value  = item.ear_tag_id;

        document.getElementById('rekamSearchRow').style.display  = 'none';
        document.getElementById('rekamDropdown').style.display   = 'none';
        document.getElementById('rekamHint').style.display       = 'none';

        document.getElementById('card_rekam_id').textContent  = 'RM-' + String(item.rekam_id).padStart(4, '0');
        document.getElementById('card_ear_tag').textContent   = item.ear_tag_id;
        document.getElementById('card_tgl_sakit').textContent = item.tanggal_sakit;
        document.getElementById('card_gejala').textContent    = item.gejala ? (item.gejala.length > 80 ? item.gejala.slice(0,80)+'…' : item.gejala) : '—';

        const statusColors = {
            sakit:           { bg:'#fee2e2', color:'#991b1b' },
            dalam_perawatan: { bg:'#fef3c7', color:'#92400e' },
            sembuh:          { bg:'#dcfce7', color:'#166534' },
        };
        const sc = statusColors[item.status] ?? { bg:'#f3f4f6', color:'#374151' };
        const statusEl = document.getElementById('card_status');
        statusEl.textContent      = item.status.replace('_', ' ').toUpperCase();
        statusEl.style.background = sc.bg;
        statusEl.style.color      = sc.color;

        document.getElementById('rekamSelectedCard').style.display = 'block';
    }

    function clearRekamSelection() {
        document.getElementById('catat_rekam_id').value             = '';
        document.getElementById('catat_ear_tag').value              = '';
        document.getElementById('rekamSearchInput').value           = '';
        document.getElementById('rekamSelectedCard').style.display  = 'none';
        document.getElementById('rekamSearchRow').style.display     = 'flex';
        document.getElementById('rekamDropdown').style.display      = 'none';
        document.getElementById('rekamHint').style.display          = 'block';
    }

    // Tutup dropdown kalau klik di luar
    document.addEventListener('click', function(e) {
        const row      = document.getElementById('rekamSearchRow');
        const dropdown = document.getElementById('rekamDropdown');
        if (row && dropdown && !row.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // ── FORM SUBMISSION ────────────────────────────────────────────────

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formCatatObat');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                // Validate rekam_id is selected
                if (!document.getElementById('catat_rekam_id').value) {
                    e.preventDefault();
                    alert('Pilih rekam medis terlebih dahulu!');
                    document.getElementById('rekamSearchInput').focus();
                    return false;
                }
                
                // Validate tanggal_pemberian
                if (!form.tanggal_pemberian.value) {
                    e.preventDefault();
                    alert('Tanggal pemberian wajib diisi!');
                    form.tanggal_pemberian.focus();
                    return false;
                }
                
                // Validate jumlah_dosis
                if (!form.jumlah_dosis.value || parseFloat(form.jumlah_dosis.value) <= 0) {
                    e.preventDefault();
                    alert('Jumlah dosis harus lebih dari 0!');
                    form.jumlah_dosis.focus();
                    return false;
                }
            });
        }
    });

    // ── CLOSE MODAL WHEN CLICKING OUTSIDE ──────────────────────────────

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalCatatPakai');
        
        if (modal) {
            modal.addEventListener('click', function(e) {
                // Jika klik di background modal (bukan di content)
                if (e.target === modal) {
                    closeCatatPakaiModal();
                }
            });
        }
    });

    // ── Lihat ──
    async function openLihatObatModal(id) {
        openModal('modalLihatObat');
        document.getElementById('lihatLoadingState').classList.remove('hidden');
        document.getElementById('lihatContent').classList.add('hidden');

        try {
            const res = await fetch(`/obat-vaksin/${id}`, { headers: { 'Accept': 'application/json' } });
            const d   = await res.json();

            document.getElementById('lihatHeaderNama').textContent  = 'Detail — ' + d.nama_obat;
            document.getElementById('lihatModalSubtitle').textContent = 'ID: ' + d.formatted_id + ' | UC-02-03 (READ)';
            document.getElementById('lihatId').textContent   = d.formatted_id;
            document.getElementById('lihatNama').textContent = d.nama_obat;

            const tipeColors = {
                vaksin:  'bg-blue-100 text-blue-700',
                vitamin: 'bg-purple-100 text-purple-700',
                obat:    'bg-amber-100 text-amber-700',
            };
            const tipeBadge = document.getElementById('lihatTipeBadge');
            tipeBadge.textContent = d.tipe.charAt(0).toUpperCase() + d.tipe.slice(1);
            tipeBadge.className   = 'inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold ' + (tipeColors[d.tipe] ?? 'bg-gray-100 text-gray-700');

            const statusMap = {
                expired:   { label: 'Expired',       class: 'bg-red-100 text-red-700' },
                mendekati: { label: 'Mendekati Exp', class: 'bg-yellow-100 text-yellow-700' },
                menipis:   { label: 'Menipis',       class: 'bg-orange-100 text-orange-700' },
                aman:      { label: 'Aman',          class: 'bg-green-100 text-green-700' },
            };
            const s = statusMap[d.status] ?? statusMap.aman;
            const statusBadge = document.getElementById('lihatStatusBadge');
            statusBadge.textContent = s.label;
            statusBadge.className   = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ' + s.class;

            const stokEl = document.getElementById('lihatStok');
            stokEl.textContent = d.stok;
            stokEl.className   = 'text-3xl font-bold ' + (d.status === 'menipis' ? 'text-orange-500' : 'text-green-600');
            document.getElementById('lihatSatuan').textContent  = d.satuan.charAt(0).toUpperCase() + d.satuan.slice(1);
            document.getElementById('lihatStokMin').textContent = d.stok_minimum + ' ' + d.satuan.charAt(0).toUpperCase() + d.satuan.slice(1);

            if (d.tanggal_expired) {
                const expDate  = new Date(d.tanggal_expired);
                const diffDays = Math.ceil((expDate - new Date()) / (1000 * 60 * 60 * 24));
                document.getElementById('lihatExpiredIcon').style.display = 'inline-block';
                document.getElementById('lihatExpired').textContent = expDate.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                const countBox = document.getElementById('lihatExpiredCountdownBox');
                const countEl  = document.getElementById('lihatExpiredCountdown');
                countBox.classList.remove('hidden');
                if (diffDays < 0) {
                    countEl.textContent = Math.abs(diffDays) + ' hari lalu';
                    countEl.className   = 'text-xs font-bold text-red-500';
                } else {
                    countEl.textContent = diffDays + ' hari lagi';
                    countEl.className   = 'text-xs font-bold ' + (diffDays <= 30 ? 'text-amber-500' : 'text-gray-500');
                }
            } else {
                document.getElementById('lihatExpiredIcon').style.display = 'none';
                document.getElementById('lihatExpired').textContent = '— tidak ada';
                document.getElementById('lihatExpiredCountdownBox').classList.add('hidden');
            }

            document.getElementById('lihatInterval').textContent = d.interval_vaksinasi
                ? 'Setiap ' + d.interval_vaksinasi + ' bulan'
                : '— tidak ada jadwal rutin';

            const formatRp = (val) => val ? 'Rp ' + Number(val).toLocaleString('id-ID') : '—';
            document.getElementById('lihatHarga').textContent = formatRp(d.harga_beli);
            document.getElementById('lihatTotalNilai').textContent = (d.harga_beli && d.stok)
                ? 'Rp ' + (Number(d.harga_beli) * d.stok).toLocaleString('id-ID')
                : '—';

            document.getElementById('lihatKeterangan').textContent = d.keterangan || '— tidak ada keterangan';
            document.getElementById('lihatRiwayat').innerHTML = `
                <div class="flex items-center gap-3 py-2">
                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-400 italic">Belum ada riwayat pemakaian.</p>
                </div>`;
            document.getElementById('lihatCreatedAt').textContent = d.created_at ?? '—';
            document.getElementById('lihatToEditBtn').onclick = () => {
                closeModal('modalLihatObat');
                openEditObatModal(id);
            };

            document.getElementById('lihatLoadingState').classList.add('hidden');
            document.getElementById('lihatContent').classList.remove('hidden');

        } catch (err) {
            alert('Gagal memuat data.');
            closeModal('modalLihatObat');
        }
    }
    function closeLihatObatModal() { closeModal('modalLihatObat'); }

    // ── Edit ──
    async function openEditObatModal(id) {
        openModal('modalEditObat');
        document.getElementById('editLoadingState').classList.remove('hidden');
        document.getElementById('editFormWrapper').classList.add('hidden');
        try {
            const res  = await fetch(`/obat-vaksin/${id}`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            document.getElementById('editModalSubtitle').textContent = data.formatted_id + ' | KAMUS DATA: TABEL OBAT_VAKSIN';
            document.getElementById('formEditObat').action           = `/obat-vaksin/${id}`;
            document.getElementById('edit_nama_obat').value          = data.nama_obat;
            document.getElementById('edit_stok').value               = data.stok;
            document.getElementById('edit_stok_minimum').value       = data.stok_minimum;
            document.getElementById('edit_tanggal_expired').value    = data.tanggal_expired ?? '';
            document.getElementById('edit_satuan').value             = data.satuan;
            editPilihTipe(data.tipe);
            document.getElementById('editLoadingState').classList.add('hidden');
            document.getElementById('editFormWrapper').classList.remove('hidden');
        } catch(err) { alert('Gagal memuat data.'); closeModal('modalEditObat'); }
    }
    function closeEditObatModal() { closeModal('modalEditObat'); }

    function editPilihTipe(val) {
        document.getElementById('edit_inputTipe').value = val;
        document.querySelectorAll('.edit-tipe-btn').forEach(btn => {
            btn.classList.remove('bg-primary','text-white'); btn.classList.add('bg-white','text-gray-600');
        });
        const a = document.getElementById('edit_btn_tipe_' + val);
        if (a) { a.classList.remove('bg-white','text-gray-600'); a.classList.add('bg-primary','text-white'); }
    }

    // ── Hapus ──
    function openHapusObatModal(id, nama, formattedId) {
        document.getElementById('hapusNamaObat').textContent = nama;
        document.getElementById('hapusIdObat').textContent   = '(' + formattedId + ')';
        document.getElementById('formHapusObat').action      = `/obat-vaksin/${id}`;
        openModal('modalHapusObat');
    }
    function closeHapusObatModal() { closeModal('modalHapusObat'); }

    // ── Tabs ──
    function switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(b => { b.classList.remove('border-primary','text-primary'); b.classList.add('border-transparent','text-gray-500'); });
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        document.getElementById('tab-'+tabId).classList.add('border-primary','text-primary');
        document.getElementById('tab-'+tabId).classList.remove('border-transparent','text-gray-500');
        document.getElementById('content-'+tabId).classList.remove('hidden');
    }

    // ── Tipe toggle tambah ──
    function pilihTipe(val) {
        document.getElementById('inputTipe').value = val;
        document.querySelectorAll('.tipe-btn').forEach(btn => { btn.classList.remove('bg-primary','text-white'); btn.classList.add('bg-white','text-gray-600'); });
        const a = document.getElementById('btn-tipe-' + val);
        if (a) { a.classList.remove('bg-white','text-gray-600'); a.classList.add('bg-primary','text-white'); }
    }

    // ── Reset form saat modal dibuka (dipanggil dari openCatatPakaiModal) ──
    function resetCatatPakaiForm(namaObat, tipe, satuan) {
    document.getElementById('catat_nama_obat').value       = namaObat || '';
    document.getElementById('catat_tipe_sediaan').textContent = 'Tipe sediaan: ' + (tipe || '—');
    document.getElementById('catat_satuan_label').textContent = satuan || 'ml';
    document.getElementById('formCatatObat').reset();
    // Jangan reset hidden fields yang baru saja di-set
    clearRekamSelection();
}
</script>

@endsection