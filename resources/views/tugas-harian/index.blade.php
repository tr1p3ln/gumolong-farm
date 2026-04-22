@extends('layouts.app')
@section('page-title', 'Daily Task Monitor')

@section('content')
<div x-data="{
    modalTambah: false,
    modalEdit: false,
    selectedId: null,

    formTambah: {
        judul: '',
        deskripsi: '',
        kandang_id: '',
        user_id: '',
        tanggal: '{{ $tanggal }}',
        prioritas: 'sedang',
    },

    formEdit: {},
    editLoading: false,
    statusLoading: false,

    async submitTambah() {
        try {
            const res = await fetch('/tugas-harian', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(this.formTambah)
            });
            const result = await res.json();
            if (result.success) window.location.reload();
        } catch (err) { console.error(err); }
    },

    async loadEdit(id) {
        this.selectedId = id;
        this.editLoading = true;
        this.modalEdit = true;
        try {
            const res = await fetch('/tugas-harian/' + id, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            this.formEdit = {
                judul:       data.judul,
                deskripsi:   data.deskripsi || '',
                kandang_id:  data.kandang_id,
                user_id:     data.user_id || '',
                tanggal:     data.tanggal ? data.tanggal.substring(0, 10) : '',
                prioritas:   data.prioritas,
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
                    'Accept': 'application/json'
                },
                body: JSON.stringify(this.formEdit)
            });
            const result = await res.json();
            if (result.success) window.location.reload();
        } catch (err) { console.error(err); }
    },

    async updateStatus(id, status, catatan = '') {
        this.statusLoading = true;
        try {
            const res = await fetch('/tugas-harian/' + id + '/status', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status, catatan_penyelesaian: catatan })
            });
            const result = await res.json();
            if (result.success) window.location.reload();
        } catch (err) { console.error(err); }
        finally { this.statusLoading = false; }
    },

    async hapusTugas(id) {
        if (!confirm('Hapus tugas ini?')) return;
        await fetch('/tugas-harian/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
        });
        window.location.reload();
    }
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
    <button @click="modalTambah = true"
            class="flex items-center gap-2 px-5 py-2.5 bg-[#2E7D32] text-white
                   text-sm font-bold rounded-lg hover:bg-green-800 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Tugas
    </button>
</div>

{{-- ══════════════════════════════
     FILTER BAR
══════════════════════════════ --}}
<form action="{{ route('tugas-harian.index') }}" method="GET"
      class="bg-white border border-gray-200 rounded-xl p-4 flex flex-wrap gap-3 mb-6 items-center">
    <div class="flex items-center gap-2">
        <label class="text-xs font-bold text-gray-500 uppercase">Tanggal:</label>
        <input type="date" name="tanggal" value="{{ $tanggal }}"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm
                      focus:ring-2 focus:ring-[#2E7D32] outline-none">
    </div>
    <div class="flex items-center gap-2">
        <label class="text-xs font-bold text-gray-500 uppercase">Kandang:</label>
        <select name="kandang_id"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm
                       focus:ring-2 focus:ring-[#2E7D32] outline-none">
            <option value="">Semua Kandang</option>
            @foreach($kandangs as $k)
            <option value="{{ $k->kandang_id }}"
                    {{ request('kandang_id') == $k->kandang_id ? 'selected' : '' }}>
                {{ $k->nama_kandang }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="flex items-center gap-2">
        <label class="text-xs font-bold text-gray-500 uppercase">Status:</label>
        <select name="status"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm
                       focus:ring-2 focus:ring-[#2E7D32] outline-none">
            <option value="">Semua Status</option>
            <option value="belum"        {{ request('status') === 'belum'        ? 'selected' : '' }}>Belum</option>
            <option value="dalam_proses" {{ request('status') === 'dalam_proses' ? 'selected' : '' }}>Dalam Proses</option>
            <option value="selesai"      {{ request('status') === 'selesai'      ? 'selected' : '' }}>Selesai</option>
            <option value="dilewati"     {{ request('status') === 'dilewati'     ? 'selected' : '' }}>Dilewati</option>
        </select>
    </div>
    <button type="submit"
            class="px-5 py-2 bg-[#2E7D32] text-white text-sm font-bold rounded-lg hover:bg-green-800 transition-colors">
        Filter
    </button>
    <a href="{{ route('tugas-harian.index') }}"
       class="px-4 py-2 border border-gray-300 text-gray-600 text-sm font-bold rounded-lg hover:bg-gray-50 transition-colors">
        Reset
    </a>
    <a href="{{ route('tugas-harian.mobile') }}" target="_blank"
       class="ml-auto flex items-center gap-2 px-4 py-2 border border-[#607F5B]
              text-[#607F5B] text-sm font-bold rounded-lg hover:bg-green-50 transition-colors">
        Buka Tampilan Mobile
    </a>
</form>

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
            $persen = $globalSummary['persen_selesai'];
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
            <div class="h-full rounded-full transition-all"
                 style="width: {{ $kPersen }}%; background: {{ $kColor }}"></div>
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
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Tugas</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Kandang</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Petugas</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Prioritas</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Waktu</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tugas as $t)
            <tr class="hover:bg-gray-50 transition-colors {{ $t->status === 'selesai' ? 'opacity-70' : '' }}">

                <td class="px-6 py-4">
                    <p class="font-bold text-gray-900 {{ $t->status === 'selesai' ? 'line-through' : '' }}">
                        {{ $t->judul }}
                    </p>
                    @if($t->deskripsi)
                    <p class="text-xs text-gray-500 mt-0.5 truncate max-w-xs">{{ $t->deskripsi }}</p>
                    @endif
                </td>

                <td class="px-6 py-4">
                    <span class="text-sm font-medium text-gray-700">
                        {{ $t->kandang?->nama_kandang ?? '-' }}
                    </span>
                </td>

                <td class="px-6 py-4">
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

                <td class="px-6 py-4">
                    @php
                        $prioritasConfig = [
                            'tinggi' => 'bg-red-50 border-red-200 text-red-700',
                            'sedang' => 'bg-amber-50 border-amber-200 text-amber-700',
                            'rendah' => 'bg-green-50 border-green-200 text-green-700',
                        ];
                        $prioritasIcon = ['tinggi' => 'Tinggi', 'sedang' => 'Sedang', 'rendah' => 'Rendah'];
                    @endphp
                    <span class="px-2.5 py-1 text-xs font-bold border rounded-full {{ $prioritasConfig[$t->prioritas] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ $prioritasIcon[$t->prioritas] ?? $t->prioritas }}
                    </span>
                </td>

                <td class="px-6 py-4">
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

                <td class="px-6 py-4 text-xs text-gray-500">
                    @if($t->waktu_mulai || $t->waktu_selesai)
                        <p>
                            {{ $t->waktu_mulai ? \Carbon\Carbon::parse($t->waktu_mulai)->format('H:i') : '-' }}
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

                <td class="px-6 py-4">
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
                <td colspan="7" class="px-6 py-12 text-center text-gray-400 italic">
                    Tidak ada tugas untuk tanggal ini.
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
                <p class="text-xs text-gray-500 mt-1">UC-DT.1 | Kamus Data: tabel TUGAS_HARIAN</p>
            </div>
            <button @click="modalTambah = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-8 space-y-5">
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
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Deskripsi</label>
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
                                :class="formTambah.prioritas === 'rendah'
                                    ? 'border-green-500 bg-green-50 text-green-700 font-bold'
                                    : 'border-gray-300 text-gray-500'"
                                class="flex-1 py-1.5 text-xs border rounded-lg transition-colors">
                            Rendah
                        </button>
                        <button type="button" @click="formTambah.prioritas = 'sedang'"
                                :class="formTambah.prioritas === 'sedang'
                                    ? 'border-amber-400 bg-amber-50 text-amber-700 font-bold'
                                    : 'border-gray-300 text-gray-500'"
                                class="flex-1 py-1.5 text-xs border rounded-lg transition-colors">
                            Sedang
                        </button>
                        <button type="button" @click="formTambah.prioritas = 'tinggi'"
                                :class="formTambah.prioritas === 'tinggi'
                                    ? 'border-red-400 bg-red-50 text-red-700 font-bold'
                                    : 'border-gray-300 text-gray-500'"
                                class="flex-1 py-1.5 text-xs border rounded-lg transition-colors">
                            Tinggi
                        </button>
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
            <button @click="modalEdit = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div x-show="editLoading" class="p-8 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-200 border-t-[#2E7D32]"></div>
        </div>

        <div x-show="!editLoading" class="p-8 space-y-5">
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

</div>{{-- /x-data --}}
@endsection
