@extends('layouts.app')
@section('page-title', 'Daily Task Monitor')

@section('content')
<div x-data="{
    modalTambah: false,
    modalEdit:   false,
    selectedId:  null,

    formTambah: {
        judul:      '',
        deskripsi:  '',
        kandang_id: '',
        user_id:    '',
        tanggal:    '{{ $tanggal }}',
        tipe:       'rutin',
        prioritas:  'sedang',
    },

    formEdit:    {},
    editLoading: false,
    statusLoading: false,

    // ── Bulk Reassign ─────────────────────────────────────────────────────
    allIds:         @json($tugas->pluck('id')),
    selectedIds:    [],
    reassignUserId: '',
    bulkLoading:    false,

    toggleSelectAll(checked) {
        this.selectedIds = checked ? [...this.allIds] : [];
    },

    isAllSelected() {
        return this.allIds.length > 0 && this.selectedIds.length === this.allIds.length;
    },

    async bulkReassign() {
        if (!this.reassignUserId || this.selectedIds.length === 0) return;
        this.bulkLoading = true;
        try {
            const res = await fetch('/tugas-harian/bulk-reassign', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                },
                body: JSON.stringify({ ids: this.selectedIds, user_id: this.reassignUserId }),
            });
            const result = await res.json();
            if (result.success) {
                window.showToast(result.message || 'Tugas berhasil di-reassign.', 'success');
                setTimeout(() => window.location.reload(), 900);
            } else {
                window.showToast(result.message || 'Gagal melakukan reassign.', 'error');
            }
        } catch (err) { console.error(err); window.showToast('Gagal terhubung ke server.', 'error'); }
        finally { this.bulkLoading = false; }
    },

    // ── Generate Rutin ────────────────────────────────────────────────────
    modalGenerateRutin: false,
    generateTanggal:    '{{ $tanggal }}',
    generateLoading:    false,

    async generateRutin() {
        this.generateLoading = true;
        try {
            const res = await fetch('/tugas-harian/generate-rutin', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                },
                body: JSON.stringify({ tanggal: this.generateTanggal }),
            });
            const result = await res.json();
            if (result.success) {
                window.showToast(result.message || 'Tugas rutin berhasil di-generate.', 'success');
                this.modalGenerateRutin = false;
                setTimeout(() => window.location.reload(), 900);
            } else {
                window.showToast(result.message || 'Gagal generate tugas rutin.', 'error');
            }
        } catch (err) { console.error(err); window.showToast('Gagal terhubung ke server.', 'error'); }
        finally { this.generateLoading = false; }
    },

    // ── CRUD ──────────────────────────────────────────────────────────────
    async submitTambah() {
        try {
            const res = await fetch('/tugas-harian', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                },
                body: JSON.stringify(this.formTambah),
            });
            const result = await res.json();
            if (result.success) {
                window.showToast('Tugas berhasil ditambahkan.', 'success');
                setTimeout(() => window.location.reload(), 900);
            } else {
                window.showToast(result.message || 'Gagal menambahkan tugas.', 'error');
            }
        } catch (err) { console.error(err); window.showToast('Gagal terhubung ke server.', 'error'); }
    },

    async loadEdit(id) {
        this.selectedId  = id;
        this.editLoading = true;
        this.modalEdit   = true;
        try {
            const res  = await fetch('/tugas-harian/' + id, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            this.formEdit = {
                judul:      data.judul,
                deskripsi:  data.deskripsi  || '',
                kandang_id: data.kandang_id,
                user_id:    data.user_id    || '',
                tanggal:    data.tanggal ? data.tanggal.substring(0, 10) : '',
                tipe:       data.tipe       || 'rutin',
                prioritas:  data.prioritas,
            };
        } catch (err) { console.error(err); }
        finally { this.editLoading = false; }
    },

    async submitEdit() {
        try {
            const res = await fetch('/tugas-harian/' + this.selectedId, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                },
                body: JSON.stringify(this.formEdit),
            });
            const result = await res.json();
            if (result.success) {
                window.showToast('Tugas berhasil diperbarui.', 'success');
                setTimeout(() => window.location.reload(), 900);
            } else {
                window.showToast(result.message || 'Gagal memperbarui tugas.', 'error');
            }
        } catch (err) { console.error(err); window.showToast('Gagal terhubung ke server.', 'error'); }
    },

    async updateStatus(id, status, catatan = '') {
        if (status === 'dilewati' && !confirm('Lewati tugas ini?\nTugas akan ditandai sebagai dilewati.')) return;
        this.statusLoading = true;
        try {
            const res = await fetch('/tugas-harian/' + id + '/status', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                },
                body: JSON.stringify({ status, catatan_penyelesaian: catatan }),
            });
            const result = await res.json();
            if (result.success) {
                window.showToast('Status tugas berhasil diperbarui.', 'success');
                setTimeout(() => window.location.reload(), 900);
            } else {
                window.showToast(result.message || 'Gagal memperbarui status.', 'error');
            }
        } catch (err) { console.error(err); window.showToast('Gagal terhubung ke server.', 'error'); }
        finally { this.statusLoading = false; }
    },

    async hapusTugas(id) {
        if (!confirm('Hapus tugas ini?')) return;
        try {
            const res = await fetch('/tugas-harian/' + id, {
                method:  'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
            });
            const result = await res.json();
            if (result.success) {
                window.showToast('Tugas berhasil dihapus.', 'success');
                setTimeout(() => window.location.reload(), 900);
            } else {
                window.showToast(result.message || 'Gagal menghapus tugas.', 'error');
            }
        } catch (err) { window.showToast('Gagal terhubung ke server.', 'error'); }
    },
}">

{{-- ══════════════════════════════
     PAGE HEADER
══════════════════════════════ --}}
<div class="flex justify-between items-start mb-6">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Daily Task Monitor</h1>
        <p class="text-gray-500 text-sm mt-1">
            Monitor checklist harian seluruh kandang —
            <span class="font-semibold text-[#2E7D32]">
                {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
            </span>
        </p>
    </div>
    <div class="flex items-center gap-2">
        @if(in_array(auth()->user()->role, ['super_admin', 'admin']))
        <a href="{{ route('template-tugas.index') }}"
           class="flex items-center gap-2 px-4 py-2.5 border border-[#607F5B] text-[#607F5B]
                  text-sm font-bold rounded-lg hover:bg-green-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Template Rutin
        </a>
        <button @click="modalGenerateRutin = true"
                class="flex items-center gap-2 px-4 py-2.5 border border-amber-400 text-amber-700
                       text-sm font-bold rounded-lg hover:bg-amber-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Generate Rutin
        </button>
        @endif
        <button @click="modalTambah = true"
                class="flex items-center gap-2 px-5 py-2.5 bg-[#2E7D32] text-white
                       text-sm font-bold rounded-lg hover:bg-green-800 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Tugas
        </button>
    </div>
</div>

{{-- ══════════════════════════════
     FILTER BAR
══════════════════════════════ --}}
<form action="{{ route('tugas-harian.index') }}" method="GET"
      class="bg-white border border-gray-200 rounded-xl px-5 pt-5 pb-4 mb-4">

    {{-- Baris filter: grid 5 kolom --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 mb-4">
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Tanggal</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                          focus:ring-2 focus:ring-[#2E7D32] outline-none">
        </div>
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Kandang</label>
            <select name="kandang_id"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                           focus:ring-2 focus:ring-[#2E7D32] outline-none">
                <option value="">Semua Kandang</option>
                @foreach($kandangs as $k)
                <option value="{{ $k->kandang_id }}" {{ request('kandang_id') == $k->kandang_id ? 'selected' : '' }}>
                    {{ $k->nama_kandang }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Tipe Tugas</label>
            <select name="tipe"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                           focus:ring-2 focus:ring-[#2E7D32] outline-none">
                <option value="">Semua Tipe</option>
                <option value="rutin"       {{ request('tipe') === 'rutin'       ? 'selected' : '' }}>Rutin</option>
                <option value="kondisional" {{ request('tipe') === 'kondisional' ? 'selected' : '' }}>Kondisional</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Status</label>
            <select name="status"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                           focus:ring-2 focus:ring-[#2E7D32] outline-none">
                <option value="">Semua Status</option>
                <option value="belum"        {{ request('status') === 'belum'        ? 'selected' : '' }}>Belum</option>
                <option value="dalam_proses" {{ request('status') === 'dalam_proses' ? 'selected' : '' }}>Dalam Proses</option>
                <option value="selesai"      {{ request('status') === 'selesai'      ? 'selected' : '' }}>Selesai</option>
                <option value="dilewati"     {{ request('status') === 'dilewati'     ? 'selected' : '' }}>Dilewati</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Petugas</label>
            <select name="user_id"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                           focus:ring-2 focus:ring-[#2E7D32] outline-none">
                <option value="">Semua Petugas</option>
                @foreach($petugasList as $p)
                <option value="{{ $p->user_id }}" {{ request('user_id') == $p->user_id ? 'selected' : '' }}>
                    {{ $p->nama }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Baris aksi: border atas + tombol kanan --}}
    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
        @php
            $filterAktif = request('kandang_id') || request('tipe') || request('status') || request('user_id');
        @endphp
        <p class="text-xs text-gray-400">
            @if($filterAktif)
                <span class="inline-flex items-center gap-1 text-amber-600 font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span>
                    Filter aktif
                </span>
            @else
                Menampilkan semua tugas
            @endif
        </p>
        <div class="flex items-center gap-2">
            @if($filterAktif)
            <a href="{{ route('tugas-harian.index', ['tanggal' => $tanggal]) }}"
               class="px-4 py-2 border border-gray-300 text-gray-500 text-sm font-bold
                      rounded-lg hover:bg-gray-50 transition-colors">
                Reset Filter
            </a>
            @endif
            <button type="submit"
                    class="px-6 py-2 bg-[#2E7D32] text-white text-sm font-bold
                           rounded-lg hover:bg-green-800 transition-colors">
                Terapkan Filter
            </button>
        </div>
    </div>
</form>

{{-- ══════════════════════════════
     BANNER MODE REASSIGN (muncul saat filter petugas aktif)
══════════════════════════════ --}}
@if($selectedUserId)
@php $petugasAbsen = $petugasList->firstWhere('user_id', $selectedUserId); @endphp
<div class="bg-amber-50 border border-amber-300 rounded-xl px-5 py-4 mb-4 flex items-center gap-3">
    <div class="w-9 h-9 rounded-full bg-amber-200 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
    </div>
    <div class="flex-1">
        <p class="text-sm font-bold text-amber-800">
            Mode Reassign — Tugas milik
            <span class="underline">{{ $petugasAbsen?->nama ?? 'petugas ini' }}</span>
        </p>
        <p class="text-xs text-amber-600 mt-0.5">
            Centang tugas yang ingin dipindahkan, lalu pilih petugas pengganti di panel bawah.
        </p>
    </div>
    <a href="{{ route('tugas-harian.index', ['tanggal' => $tanggal]) }}"
       class="text-xs font-bold text-amber-700 hover:text-amber-900 whitespace-nowrap">
        ✕ Hapus Filter
    </a>
</div>
@endif

{{-- ══════════════════════════════
     BULK REASSIGN ACTION BAR (muncul saat ada tugas tercentang)
══════════════════════════════ --}}
<div x-show="selectedIds.length > 0"
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0 -translate-y-1"
     x-transition:enter-end="opacity-100 translate-y-0"
     class="bg-blue-50 border border-blue-300 rounded-xl px-5 py-4 mb-4 flex flex-wrap items-center gap-4"
     style="display: none;">
    <div class="flex items-center gap-2 text-blue-800">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-bold text-sm">
            <span x-text="selectedIds.length"></span> tugas dipilih
        </span>
    </div>
    <span class="text-blue-200 select-none">|</span>
    <span class="text-sm font-semibold text-gray-600">Pindahkan ke:</span>
    <select x-model="reassignUserId"
            class="border border-blue-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none bg-white">
        <option value="">-- Pilih Petugas Pengganti --</option>
        @foreach($petugasList as $p)
        <option value="{{ $p->user_id }}">
            {{ $p->nama }}
            <span>({{ $p->role === 'kepala_kandang' ? 'Kepala Kandang' : 'Pengurus Kandang' }})</span>
        </option>
        @endforeach
    </select>
    <button @click="bulkReassign()"
            :disabled="!reassignUserId || bulkLoading"
            class="px-5 py-1.5 bg-blue-600 text-white text-sm font-bold rounded-lg
                   hover:bg-blue-700 transition-colors disabled:opacity-50">
        <span x-show="bulkLoading">Memproses...</span>
        <span x-show="!bulkLoading">Reassign Sekarang</span>
    </button>
    <button @click="selectedIds = []; reassignUserId = ''"
            class="text-xs font-bold text-gray-400 hover:text-gray-600 uppercase tracking-wide">
        Batalkan Pilihan
    </button>
</div>

{{-- ══════════════════════════════
     GLOBAL SUMMARY CARDS
══════════════════════════════ --}}
<div class="grid grid-cols-5 gap-4 mb-6">
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Tugas</label>
        <p class="text-3xl font-black text-gray-900 mt-2">{{ $globalSummary['total'] }}</p>
        <p class="text-xs text-gray-400 mt-1">hari ini</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Selesai</label>
        <p class="text-3xl font-black text-[#2E7D32] mt-2">{{ $globalSummary['selesai'] }}</p>
        <p class="text-xs text-gray-400 mt-1">completed</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Dalam Proses</label>
        <p class="text-3xl font-black text-amber-500 mt-2">{{ $globalSummary['dalam_proses'] }}</p>
        <p class="text-xs text-gray-400 mt-1">on going</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Belum Dikerjakan</label>
        <p class="text-3xl font-black text-gray-500 mt-2">{{ $globalSummary['belum'] }}</p>
        <p class="text-xs text-gray-400 mt-1">pending</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Completion Rate</label>
        @php
            $persen    = $globalSummary['persen_selesai'];
            $rateColor = $persen >= 80 ? '#2E7D32' : ($persen >= 50 ? '#D97706' : '#B14B6F');
        @endphp
        <p class="text-3xl font-black mt-2" style="color: {{ $rateColor }}">{{ $persen }}%</p>
        <div class="mt-2 h-1.5 bg-gray-200 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all"
                 style="width: {{ $persen }}%; background: {{ $rateColor }}"></div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════
     PROGRESS PER KANDANG
══════════════════════════════ --}}
@if(count($summaryPerKandang) > 0)
<div class="grid grid-cols-3 gap-4 mb-6">
    @foreach($summaryPerKandang as $kId => $summary)
    @if($summary['total'] > 0)
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        @php
            $kPersen = $summary['persen_selesai'];
            $kColor  = $kPersen >= 80 ? '#2E7D32' : ($kPersen >= 50 ? '#D97706' : '#B14B6F');
        @endphp
        <div class="flex justify-between items-start mb-3">
            <div>
                <h3 class="font-bold text-gray-900">{{ $summary['kandang']->nama_kandang }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">{{ $summary['total'] }} tugas hari ini</p>
            </div>
            <span class="text-sm font-black" style="color: {{ $kColor }}">{{ $kPersen }}%</span>
        </div>
        <div class="h-2 bg-gray-100 rounded-full overflow-hidden mb-3">
            <div class="h-full rounded-full" style="width: {{ $kPersen }}%; background: {{ $kColor }}"></div>
        </div>
        <div class="flex gap-2 flex-wrap">
            <span class="px-2 py-0.5 bg-green-50 border border-green-200 text-green-700 text-xs font-bold rounded-full">
                ✓ {{ $summary['selesai'] }} selesai
            </span>
            @if($summary['dalam_proses'] > 0)
            <span class="px-2 py-0.5 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold rounded-full">
                {{ $summary['dalam_proses'] }} proses
            </span>
            @endif
            @if($summary['belum'] > 0)
            <span class="px-2 py-0.5 bg-gray-100 border border-gray-200 text-gray-600 text-xs font-bold rounded-full">
                {{ $summary['belum'] }} belum
            </span>
            @endif
            @if($summary['dilewati'] > 0)
            <span class="px-2 py-0.5 bg-red-50 border border-red-200 text-red-600 text-xs font-bold rounded-full">
                {{ $summary['dilewati'] }} dilewati
            </span>
            @endif
        </div>
    </div>
    @endif
    @endforeach
</div>
@endif

{{-- ══════════════════════════════
     TABEL DAFTAR TUGAS
══════════════════════════════ --}}
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-900">Daftar Tugas</h3>
        <p class="text-xs text-gray-500">{{ count($tugas) }} tugas ditemukan</p>
    </div>

    <table class="w-full text-left">
        <thead class="border-b border-gray-200">
            <tr>
                <th class="px-4 py-4 w-10">
                    <input type="checkbox"
                           :checked="isAllSelected()"
                           :indeterminate="selectedIds.length > 0 && !isAllSelected()"
                           @change="toggleSelectAll($event.target.checked)"
                           class="w-4 h-4 rounded border-gray-300 text-[#2E7D32] cursor-pointer">
                </th>
                <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Tugas</th>
                <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Kandang</th>
                <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Petugas</th>
                <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Prioritas</th>
                <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Waktu</th>
                <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tugas as $t)
            <tr class="hover:bg-gray-50 transition-colors {{ $t->status === 'selesai' ? 'opacity-70' : '' }}"
                :class="selectedIds.includes({{ $t->id }}) ? 'bg-blue-50' : ''">

                <td class="px-4 py-4">
                    <input type="checkbox"
                           value="{{ $t->id }}"
                           x-model="selectedIds"
                           :value="{{ $t->id }}"
                           class="w-4 h-4 rounded border-gray-300 text-[#2E7D32] cursor-pointer">
                </td>

                <td class="px-4 py-4">
                    <p class="font-bold text-gray-900 {{ $t->status === 'selesai' ? 'line-through' : '' }}">
                        {{ $t->judul }}
                    </p>
                    <div class="flex items-center gap-2 mt-1">
                        @if($t->tipe === 'kondisional')
                        <span class="px-1.5 py-0.5 bg-purple-50 border border-purple-200 text-purple-700 text-[10px] font-bold rounded">
                            Kondisional
                        </span>
                        @else
                        <span class="px-1.5 py-0.5 bg-cyan-50 border border-cyan-200 text-cyan-700 text-[10px] font-bold rounded">
                            Rutin
                        </span>
                        @endif
                        @if($t->deskripsi)
                        <p class="text-xs text-gray-400 truncate max-w-[200px]">{{ $t->deskripsi }}</p>
                        @endif
                    </div>
                </td>

                <td class="px-4 py-4">
                    <span class="text-sm font-medium text-gray-700">
                        {{ $t->kandang?->nama_kandang ?? '-' }}
                    </span>
                </td>

                <td class="px-4 py-4">
                    @if($t->petugas)
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-[#2E7D32] flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-xs font-bold">
                                    {{ strtoupper(substr($t->petugas->nama, 0, 1)) }}
                                </span>
                            </div>
                            <span class="text-sm font-medium text-gray-700">{{ $t->petugas->nama }}</span>
                        </div>
                    @else
                        <span class="text-xs text-gray-400 italic">Belum di-assign</span>
                    @endif
                </td>

                <td class="px-4 py-4">
                    @php
                        $prioritasConfig = [
                            'tinggi' => 'bg-red-50 border-red-200 text-red-700',
                            'sedang' => 'bg-amber-50 border-amber-200 text-amber-700',
                            'rendah' => 'bg-green-50 border-green-200 text-green-700',
                        ];
                    @endphp
                    <span class="px-2.5 py-1 text-xs font-bold border rounded-full {{ $prioritasConfig[$t->prioritas] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($t->prioritas) }}
                    </span>
                </td>

                <td class="px-4 py-4">
                    @php
                        $statusConfig = [
                            'belum'        => 'bg-gray-100 border-gray-200 text-gray-600',
                            'dalam_proses' => 'bg-amber-50 border-amber-200 text-amber-700',
                            'selesai'      => 'bg-green-50 border-green-200 text-green-700',
                            'dilewati'     => 'bg-red-50 border-red-200 text-red-600',
                        ];
                    @endphp
                    <span class="px-2.5 py-1 text-xs font-bold border rounded-full {{ $statusConfig[$t->status] ?? '' }}">
                        {{ $t->status_label }}
                    </span>
                </td>

                <td class="px-4 py-4 text-xs text-gray-500">
                    @if($t->waktu_mulai || $t->waktu_selesai)
                        <p>
                            {{ $t->waktu_mulai  ? \Carbon\Carbon::parse($t->waktu_mulai)->format('H:i')  : '-' }}
                            →
                            {{ $t->waktu_selesai ? \Carbon\Carbon::parse($t->waktu_selesai)->format('H:i') : '...' }}
                        </p>
                        @if($t->durasi)
                        <p class="text-[#2E7D32] font-bold mt-0.5">{{ $t->durasi }}</p>
                        @endif
                    @else
                        <span class="text-gray-300">—</span>
                    @endif
                </td>

                <td class="px-4 py-4">
                    <div class="flex gap-1.5 justify-end flex-wrap">
                        @if($t->status === 'belum')
                        <button @click="updateStatus({{ $t->id }}, 'dalam_proses')"
                                :disabled="statusLoading"
                                class="px-3 py-1 text-xs font-bold border border-amber-300 text-amber-700
                                       rounded-lg hover:bg-amber-50 transition-colors disabled:opacity-50">
                            Mulai
                        </button>
                        @endif

                        @if(in_array($t->status, ['belum', 'dalam_proses']))
                        <button @click="updateStatus({{ $t->id }}, 'selesai')"
                                :disabled="statusLoading"
                                class="px-3 py-1 text-xs font-bold border border-green-300 text-green-700
                                       rounded-lg hover:bg-green-50 transition-colors disabled:opacity-50">
                            Selesai
                        </button>
                        <button @click="updateStatus({{ $t->id }}, 'dilewati')"
                                :disabled="statusLoading"
                                class="px-3 py-1 text-xs font-bold border border-red-200 text-red-500
                                       rounded-lg hover:bg-red-50 transition-colors disabled:opacity-50">
                            Lewati
                        </button>
                        @endif

                        <button @click="loadEdit({{ $t->id }})"
                                class="px-3 py-1 text-xs font-bold border border-gray-300 text-gray-600
                                       rounded-lg hover:bg-gray-50 transition-colors">
                            Edit
                        </button>

                        @if(auth()->user()->role === 'super_admin')
                        <button @click="hapusTugas({{ $t->id }})"
                                class="px-3 py-1 text-xs font-bold border border-red-200 text-[#B14B6F]
                                       rounded-lg hover:bg-red-50 transition-colors">
                            Hapus
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-12 text-center text-gray-400 italic">
                    Tidak ada tugas untuk filter ini.
                    <br>
                    <button @click="modalTambah = true"
                            class="mt-3 px-4 py-2 bg-[#2E7D32] text-white text-sm font-bold
                                   rounded-lg hover:bg-green-800 transition-colors">
                        Tambah Tugas Pertama
                    </button>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ══════════════════════════════
     MODAL TAMBAH TUGAS
══════════════════════════════ --}}
<div x-show="modalTambah"
     @keydown.escape.window="modalTambah = false"
     class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
     style="display: none;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-200 flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Tambah Tugas Harian</h2>
            </div>
            <button @click="modalTambah = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-8 space-y-5">
            {{-- Tipe Tugas --}}
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-3">
                    Tipe Tugas <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-3">
                    <button type="button" @click="formTambah.tipe = 'rutin'"
                            :class="formTambah.tipe === 'rutin'
                                ? 'border-cyan-500 bg-cyan-50 text-cyan-700 font-bold ring-2 ring-cyan-200'
                                : 'border-gray-300 text-gray-500'"
                            class="flex-1 py-2.5 text-sm border rounded-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Rutin (Harian)
                    </button>
                    <button type="button" @click="formTambah.tipe = 'kondisional'"
                            :class="formTambah.tipe === 'kondisional'
                                ? 'border-purple-500 bg-purple-50 text-purple-700 font-bold ring-2 ring-purple-200'
                                : 'border-gray-300 text-gray-500'"
                            class="flex-1 py-2.5 text-sm border rounded-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Kondisional (Mendadak)
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1.5">
                    <span x-show="formTambah.tipe === 'rutin'">Tugas yang dilakukan setiap hari secara terjadwal.</span>
                    <span x-show="formTambah.tipe === 'kondisional'">Tugas mendadak yang diberikan sesuai situasi.</span>
                </p>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">
                    Judul Tugas <span class="text-red-500">*</span>
                </label>
                <input type="text" x-model="formTambah.judul"
                       placeholder="Contoh: Pemberian pakan pagi Kandang A"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:ring-2 focus:ring-[#2E7D32] focus:border-transparent outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Deskripsi / Instruksi</label>
                <textarea x-model="formTambah.deskripsi" rows="3"
                          placeholder="Detail instruksi atau catatan..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                 focus:ring-2 focus:ring-[#2E7D32] focus:border-transparent outline-none resize-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">
                        Kandang <span class="text-red-500">*</span>
                    </label>
                    <select x-model="formTambah.kandang_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-[#2E7D32] outline-none">
                        <option value="">Pilih Kandang</option>
                        @foreach($kandangs as $k)
                        <option value="{{ $k->kandang_id }}">{{ $k->nama_kandang }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Assign ke Petugas</label>
                    <select x-model="formTambah.user_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-[#2E7D32] outline-none">
                        <option value="">Tidak di-assign</option>
                        @foreach($petugasList as $p)
                        <option value="{{ $p->user_id }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" x-model="formTambah.tanggal"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-[#2E7D32] outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-3">
                        Prioritas <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <button type="button" @click="formTambah.prioritas = 'rendah'"
                                :class="formTambah.prioritas === 'rendah' ? 'border-green-500 bg-green-50 text-green-700 font-bold' : 'border-gray-300 text-gray-500'"
                                class="flex-1 py-1.5 text-xs border rounded-lg transition-colors">Rendah</button>
                        <button type="button" @click="formTambah.prioritas = 'sedang'"
                                :class="formTambah.prioritas === 'sedang' ? 'border-amber-400 bg-amber-50 text-amber-700 font-bold' : 'border-gray-300 text-gray-500'"
                                class="flex-1 py-1.5 text-xs border rounded-lg transition-colors">Sedang</button>
                        <button type="button" @click="formTambah.prioritas = 'tinggi'"
                                :class="formTambah.prioritas === 'tinggi' ? 'border-red-400 bg-red-50 text-red-700 font-bold' : 'border-gray-300 text-gray-500'"
                                class="flex-1 py-1.5 text-xs border rounded-lg transition-colors">Tinggi</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-8 py-5 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <button @click="modalTambah = false"
                    class="px-6 py-2 border border-gray-300 text-gray-600 text-sm font-bold rounded-lg hover:bg-gray-100 transition-colors">
                Batal
            </button>
            <button @click="submitTambah()"
                    class="px-8 py-2 bg-[#2E7D32] text-white text-sm font-bold rounded-lg hover:bg-green-800 transition-colors">
                Simpan Tugas
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════
     MODAL EDIT TUGAS
══════════════════════════════ --}}
<div x-show="modalEdit"
     @keydown.escape.window="modalEdit = false"
     class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
     style="display: none;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-200 flex justify-between items-start">
            <h2 class="text-xl font-bold text-gray-900">Edit Tugas Harian</h2>
            <button @click="modalEdit = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div x-show="editLoading" class="p-8 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-200 border-t-[#2E7D32]"></div>
        </div>

        <div x-show="!editLoading" class="p-8 space-y-5">
            {{-- Tipe --}}
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-3">Tipe Tugas</label>
                <div class="flex gap-3">
                    <button type="button" @click="formEdit.tipe = 'rutin'"
                            :class="formEdit.tipe === 'rutin' ? 'border-cyan-500 bg-cyan-50 text-cyan-700 font-bold ring-2 ring-cyan-200' : 'border-gray-300 text-gray-500'"
                            class="flex-1 py-2 text-sm border rounded-lg transition-all">
                        Rutin (Harian)
                    </button>
                    <button type="button" @click="formEdit.tipe = 'kondisional'"
                            :class="formEdit.tipe === 'kondisional' ? 'border-purple-500 bg-purple-50 text-purple-700 font-bold ring-2 ring-purple-200' : 'border-gray-300 text-gray-500'"
                            class="flex-1 py-2 text-sm border rounded-lg transition-all">
                        Kondisional (Mendadak)
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Judul Tugas</label>
                <input type="text" x-model="formEdit.judul"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:ring-2 focus:ring-[#2E7D32] focus:border-transparent outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Deskripsi</label>
                <textarea x-model="formEdit.deskripsi" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                 focus:ring-2 focus:ring-[#2E7D32] focus:border-transparent outline-none resize-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Kandang</label>
                    <select x-model="formEdit.kandang_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-[#2E7D32] outline-none">
                        @foreach($kandangs as $k)
                        <option value="{{ $k->kandang_id }}">{{ $k->nama_kandang }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Petugas</label>
                    <select x-model="formEdit.user_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-[#2E7D32] outline-none">
                        <option value="">Tidak di-assign</option>
                        @foreach($petugasList as $p)
                        <option value="{{ $p->user_id }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Tanggal</label>
                    <input type="date" x-model="formEdit.tanggal"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-[#2E7D32] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-3">Prioritas</label>
                    <div class="flex gap-2">
                        <button type="button" @click="formEdit.prioritas = 'rendah'"
                                :class="formEdit.prioritas === 'rendah' ? 'border-green-500 bg-green-50 text-green-700 font-bold' : 'border-gray-300 text-gray-500'"
                                class="flex-1 py-1.5 text-xs border rounded-lg transition-colors">Rendah</button>
                        <button type="button" @click="formEdit.prioritas = 'sedang'"
                                :class="formEdit.prioritas === 'sedang' ? 'border-amber-400 bg-amber-50 text-amber-700 font-bold' : 'border-gray-300 text-gray-500'"
                                class="flex-1 py-1.5 text-xs border rounded-lg transition-colors">Sedang</button>
                        <button type="button" @click="formEdit.prioritas = 'tinggi'"
                                :class="formEdit.prioritas === 'tinggi' ? 'border-red-400 bg-red-50 text-red-700 font-bold' : 'border-gray-300 text-gray-500'"
                                class="flex-1 py-1.5 text-xs border rounded-lg transition-colors">Tinggi</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-8 py-5 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <button @click="modalEdit = false"
                    class="px-6 py-2 border border-gray-300 text-gray-600 text-sm font-bold rounded-lg hover:bg-gray-100 transition-colors">
                Batal
            </button>
            <button @click="submitEdit()"
                    class="px-8 py-2 bg-[#2E7D32] text-white text-sm font-bold rounded-lg hover:bg-green-800 transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════
     MODAL GENERATE RUTIN
══════════════════════════════ --}}
<div x-show="modalGenerateRutin"
     @keydown.escape.window="modalGenerateRutin = false"
     class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
     style="display: none;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Generate Tugas Rutin</h2>
            <p class="text-xs text-gray-500 mt-1">
                Buat tugas harian dari semua template rutin yang aktif untuk tanggal yang dipilih.
            </p>
        </div>
        <div class="p-8 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">
                    Tanggal Generate <span class="text-red-500">*</span>
                </label>
                <input type="date" x-model="generateTanggal"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:ring-2 focus:ring-[#2E7D32] outline-none">
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                <p class="text-xs text-amber-700">
                    Tugas yang sudah ada dengan judul + kandang yang sama akan dilewati (tidak duplikat).
                </p>
            </div>
        </div>
        <div class="px-8 py-5 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <button @click="modalGenerateRutin = false"
                    class="px-6 py-2 border border-gray-300 text-gray-600 text-sm font-bold rounded-lg hover:bg-gray-100 transition-colors">
                Batal
            </button>
            <button @click="generateRutin()"
                    :disabled="generateLoading"
                    class="px-8 py-2 bg-amber-500 text-white text-sm font-bold rounded-lg hover:bg-amber-600 transition-colors disabled:opacity-50">
                <span x-show="generateLoading">Memproses...</span>
                <span x-show="!generateLoading">Generate Sekarang</span>
            </button>
        </div>
    </div>
</div>

</div>{{-- /x-data --}}
@endsection
