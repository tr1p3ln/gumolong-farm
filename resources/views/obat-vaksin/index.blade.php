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
        <div class="flex flex-col flex-1 min-h-0">
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
        <div id="editFormWrapper" class="flex flex-col flex-1 min-h-0 hidden">
            @include('obat-vaksin.partials.edit-obat')
        </div>
    </div>
</div>

{{-- Modal Hapus --}}
@include('obat-vaksin.partials.hapus-obat')

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

{{-- Alerts --}}
<div class="flex items-start justify-between bg-amber-50 border border-amber-300 rounded-lg px-4 py-3 mb-3">
    <div class="flex items-start gap-2">
        <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-amber-800">Mendekati Expired — 3 Item</p>
            <p class="text-xs text-amber-700 mt-0.5">Vaksin PMK, Antibiotik Penicillin, Vitamin B12 akan kadaluwarsa dalam <strong>2 hari</strong>.</p>
        </div>
    </div>
    <button class="text-xs font-medium text-amber-700 border border-amber-400 hover:bg-amber-100 px-3 py-1.5 rounded-md transition whitespace-nowrap ml-4">Lihat Detail</button>
</div>
<div class="flex items-start justify-between bg-orange-50 border border-orange-300 rounded-lg px-4 py-3 mb-6">
    <div class="flex items-start gap-2">
        <svg class="w-4 h-4 text-orange-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-orange-800">Stok Tipis — 5 Item</p>
            <p class="text-xs text-orange-700 mt-0.5">5 jenis obat/vaksin berada di bawah batas stok minimum.</p>
        </div>
    </div>
    <button class="text-xs font-medium text-orange-700 border border-orange-400 hover:bg-orange-100 px-3 py-1.5 rounded-md transition whitespace-nowrap ml-4">Lihat Detail</button>
</div>

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
        <div class="py-16 text-center">
            <svg class="mx-auto w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-400 text-sm">Riwayat Pemakaian — dalam pengembangan.</p>
        </div>
    </div>
    <div id="content-jadwal-vaksinasi" class="tab-content hidden p-6">
        <div class="py-16 text-center">
            <svg class="mx-auto w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-400 text-sm">Jadwal Vaksinasi — dalam pengembangan.</p>
        </div>
    </div>
</div>

<script>
    function openModal(id)  { const m=document.getElementById(id); m.classList.remove('hidden'); m.classList.add('flex'); document.body.style.overflow='hidden'; }
    function closeModal(id) { const m=document.getElementById(id); m.classList.add('hidden'); m.classList.remove('flex'); document.body.style.overflow=''; }

    // Tutup modal klik backdrop
    ['modalTambahObat','modalLihatObat','modalEditObat','modalHapusObat'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', function(e) {
            if (e.target === this) closeModal(id);
        });
    });

    // ── Tambah ────────────────────────────────────────────────────
    function openTambahObatModal()  { openModal('modalTambahObat'); }
    function closeTambahObatModal() { closeModal('modalTambahObat'); }

    // ── Lihat ─────────────────────────────────────────────────────
    async function openLihatObatModal(id) {
    openModal('modalLihatObat');
    document.getElementById('lihatLoadingState').classList.remove('hidden');
    document.getElementById('lihatContent').classList.add('hidden');
 
    try {
        const res = await fetch(`/obat-vaksin/${id}`, { headers: { 'Accept': 'application/json' } });
        const d   = await res.json();
 
        // ── Header ──
        document.getElementById('lihatHeaderNama').textContent  = 'Detail — ' + d.nama_obat;
        document.getElementById('lihatModalSubtitle').textContent = 'ID: ' + d.formatted_id + ' | UC-02-03 (READ)';
 
        // ── Row 1: ID + Nama ──
        document.getElementById('lihatId').textContent   = d.formatted_id;
        document.getElementById('lihatNama').textContent = d.nama_obat;
 
        // ── Row 2: Tipe + Status ──
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
 
        // ── Row 3: Stok ──
        const stokEl = document.getElementById('lihatStok');
        stokEl.textContent = d.stok;
        stokEl.className   = 'text-3xl font-bold ' + (d.status === 'menipis' ? 'text-orange-500' : 'text-green-600');
        document.getElementById('lihatSatuan').textContent  = d.satuan.charAt(0).toUpperCase() + d.satuan.slice(1);
        document.getElementById('lihatStokMin').textContent = d.stok_minimum + ' ' + d.satuan.charAt(0).toUpperCase() + d.satuan.slice(1);
 
        // ── Row 4: Expired + Interval ──
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
 
        // ── Row 5: Harga + Total Nilai ──
        const formatRp = (val) => val ? 'Rp ' + Number(val).toLocaleString('id-ID') : '—';
        document.getElementById('lihatHarga').textContent = formatRp(d.harga_beli);
        document.getElementById('lihatTotalNilai').textContent = (d.harga_beli && d.stok)
            ? 'Rp ' + (Number(d.harga_beli) * d.stok).toLocaleString('id-ID')
            : '—';
 
        // ── Keterangan ──
        document.getElementById('lihatKeterangan').textContent = d.keterangan || '— tidak ada keterangan';
 
        // ── Riwayat (placeholder, nanti diisi setelah catat pemakaian) ──
        document.getElementById('lihatRiwayat').innerHTML = `
            <div class="flex items-center gap-3 py-2">
                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-xs text-gray-400 italic">Belum ada riwayat pemakaian.</p>
            </div>`;
 
        // ── Meta ──
        document.getElementById('lihatCreatedAt').textContent = d.created_at ?? '—';
 
        // ── Tombol Edit ──
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

    // ── Edit ──────────────────────────────────────────────────────
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

    // ── Hapus ─────────────────────────────────────────────────────
    function openHapusObatModal(id, nama, formattedId) {
        document.getElementById('hapusNamaObat').textContent = nama;
        document.getElementById('hapusIdObat').textContent   = '(' + formattedId + ')';
        document.getElementById('formHapusObat').action      = `/obat-vaksin/${id}`;
        openModal('modalHapusObat');
    }
    function closeHapusObatModal() { closeModal('modalHapusObat'); }

    // ── Tabs ──────────────────────────────────────────────────────
    function switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(b => { b.classList.remove('border-primary','text-primary'); b.classList.add('border-transparent','text-gray-500'); });
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        document.getElementById('tab-'+tabId).classList.add('border-primary','text-primary');
        document.getElementById('tab-'+tabId).classList.remove('border-transparent','text-gray-500');
        document.getElementById('content-'+tabId).classList.remove('hidden');
    }

    // ── Tipe toggle tambah ────────────────────────────────────────
    function pilihTipe(val) {
        document.getElementById('inputTipe').value = val;
        document.querySelectorAll('.tipe-btn').forEach(btn => { btn.classList.remove('bg-primary','text-white'); btn.classList.add('bg-white','text-gray-600'); });
        const a = document.getElementById('btn-tipe-' + val);
        if (a) { a.classList.remove('bg-white','text-gray-600'); a.classList.add('bg-primary','text-white'); }
    }
</script>

@endsection