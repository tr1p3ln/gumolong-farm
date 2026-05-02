@extends('layouts.app')
@section('page-title', 'Manajemen Kandang')

@section('content')
<div x-data="{
    modalTambah: false,
    modalEdit:   false,
    selectedId:  null,
    errorMsg:    '',

    formTambah: {
        nama_kandang: '',
        tipe:         'utama',
        kapasitas:    '',
    },

    formEdit: {},

    loadEdit(id, data) {
        this.selectedId  = id;
        this.errorMsg    = '';
        this.formEdit = {
            nama_kandang: data.nama_kandang,
            tipe:         data.tipe,
            kapasitas:    data.kapasitas,
        };
        this.modalEdit = true;
    },

    async submitTambah() {
        this.errorMsg = '';
        try {
            const res    = await fetch('/kandang', {
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
                window.location.reload();
            } else {
                this.errorMsg = result.message || result.errors?.nama_kandang?.[0] || 'Terjadi kesalahan.';
            }
        } catch (err) { this.errorMsg = 'Terjadi kesalahan. Coba lagi.'; }
    },

    async submitEdit() {
        this.errorMsg = '';
        try {
            const res    = await fetch('/kandang/' + this.selectedId, {
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
                window.location.reload();
            } else {
                this.errorMsg = result.message || 'Terjadi kesalahan.';
            }
        } catch (err) { this.errorMsg = 'Terjadi kesalahan. Coba lagi.'; }
    },

    async hapusKandang(id) {
        if (!confirm('Hapus kandang ini?\nKandang hanya dapat dihapus jika tidak ada domba aktif.')) return;
        try {
            const res    = await fetch('/kandang/' + id, {
                method:  'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept':       'application/json',
                },
            });
            const result = await res.json();
            if (result.success) {
                window.location.reload();
            } else {
                alert(result.message);
            }
        } catch (err) { alert('Terjadi kesalahan. Coba lagi.'); }
    },
}">

{{-- ══════════════════════════════
     PAGE HEADER
══════════════════════════════ --}}
<div class="flex justify-between items-start mb-6">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Kandang</h1>
        <p class="text-gray-500 text-sm mt-1">
            Kelola data kandang, kapasitas, dan tipe penggunaan kandang.
        </p>
    </div>
    <button @click="modalTambah = true; errorMsg = ''; formTambah = { nama_kandang: '', tipe: 'utama', kapasitas: '' }"
            class="flex items-center gap-2 px-5 py-2.5 bg-[#2E7D32] text-white
                   text-sm font-bold rounded-lg hover:bg-green-800 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Kandang
    </button>
</div>

{{-- ══════════════════════════════
     SUMMARY CARDS
══════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Kandang</label>
        <p class="text-3xl font-black text-gray-900 mt-2">{{ $stats['total'] }}</p>
        <p class="text-xs text-gray-400 mt-1">terdaftar</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Kapasitas</label>
        <p class="text-3xl font-black text-gray-900 mt-2">{{ number_format($stats['kapasitas']) }}</p>
        <p class="text-xs text-gray-400 mt-1">ekor kapasitas</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Terisi</label>
        <p class="text-3xl font-black text-[#B14B6F] mt-2">{{ number_format($stats['terisi']) }}</p>
        <p class="text-xs text-gray-400 mt-1">domba aktif</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tersedia</label>
        <p class="text-3xl font-black text-[#2E7D32] mt-2">{{ number_format($stats['tersedia']) }}</p>
        <p class="text-xs text-gray-400 mt-1">slot kosong</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Occupancy</label>
        @php
            $oColor = $stats['persen'] >= 90 ? '#B14B6F' : ($stats['persen'] >= 70 ? '#D97706' : '#2E7D32');
        @endphp
        <p class="text-3xl font-black mt-2" style="color: {{ $oColor }}">{{ $stats['persen'] }}%</p>
        <div class="mt-2 h-1.5 bg-gray-200 rounded-full overflow-hidden">
            <div class="h-full rounded-full" style="width:{{ $stats['persen'] }}%; background:{{ $oColor }}"></div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════
     TIPE LEGEND
══════════════════════════════ --}}
<div class="flex flex-wrap gap-3 mb-5">
    @php
        $tipeMap = [
            'utama'      => ['Kandang Utama',     'bg-blue-50 border-blue-200 text-blue-700'],
            'isolasi'    => ['Kandang Isolasi',   'bg-amber-50 border-amber-200 text-amber-700'],
            'kawin'      => ['Kandang Kawin',     'bg-pink-50 border-pink-200 text-pink-700'],
            'persalinan' => ['Kandang Persalinan','bg-cyan-50 border-cyan-200 text-cyan-700'],
        ];
    @endphp
    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider self-center">Tipe:</span>
    @foreach($tipeMap as $key => [$label, $cls])
    <span class="px-2.5 py-1 text-xs font-bold border rounded-full {{ $cls }}">{{ $label }}</span>
    @endforeach
</div>

{{-- ══════════════════════════════
     TABEL KANDANG
══════════════════════════════ --}}
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-900">Daftar Kandang</h3>
        <p class="text-xs text-gray-500">{{ $kandangs->count() }} kandang terdaftar</p>
    </div>

    <table class="w-full text-left">
        <thead class="border-b border-gray-200">
            <tr>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Kandang</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Tipe</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Kapasitas</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Terisi</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Tersedia</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Occupancy</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($kandangs as $k)
            @php
                $aktif      = $k->domba_aktif ?? 0;
                $sisaSlot   = max(0, $k->kapasitas - $aktif);
                $persen     = $k->kapasitas > 0 ? round($aktif / $k->kapasitas * 100) : 0;
                $barColor   = $persen >= 90 ? '#B14B6F' : ($persen >= 70 ? '#D97706' : '#2E7D32');
                $tipeConfig = [
                    'utama'      => ['Utama',      'bg-blue-50 border-blue-200 text-blue-700'],
                    'isolasi'    => ['Isolasi',    'bg-amber-50 border-amber-200 text-amber-700'],
                    'kawin'      => ['Kawin',      'bg-pink-50 border-pink-200 text-pink-700'],
                    'persalinan' => ['Persalinan', 'bg-cyan-50 border-cyan-200 text-cyan-700'],
                ];
                [$tipeLabel, $tipeCls] = $tipeConfig[$k->tipe] ?? ['—', 'bg-gray-100 text-gray-600'];
            @endphp
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                    <p class="font-bold text-gray-900">{{ $k->nama_kandang }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">ID #{{ $k->kandang_id }}</p>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-1 text-xs font-bold border rounded-full {{ $tipeCls }}">
                        {{ $tipeLabel }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="text-sm font-bold text-gray-700">{{ number_format($k->kapasitas) }}</span>
                    <span class="text-xs text-gray-400 ml-0.5">ekor</span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="text-sm font-bold {{ $aktif > 0 ? 'text-gray-800' : 'text-gray-400' }}">
                        {{ number_format($aktif) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="text-sm font-bold" style="color: {{ $sisaSlot > 0 ? '#2E7D32' : '#B14B6F' }}">
                        {{ number_format($sisaSlot) }}
                    </span>
                </td>
                <td class="px-6 py-4 min-w-[140px]">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all"
                                 style="width:{{ $persen }}%; background:{{ $barColor }}"></div>
                        </div>
                        <span class="text-xs font-bold w-8 text-right" style="color:{{ $barColor }}">
                            {{ $persen }}%
                        </span>
                    </div>
                    @if($persen >= 90)
                    <p class="text-[10px] text-[#B14B6F] font-semibold mt-0.5">Hampir penuh</p>
                    @elseif($persen === 0)
                    <p class="text-[10px] text-gray-400 mt-0.5">Kosong</p>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex gap-1.5 justify-end">
                        <button @click='loadEdit({{ $k->kandang_id }}, @json(["nama_kandang" => $k->nama_kandang, "tipe" => $k->tipe, "kapasitas" => $k->kapasitas]))'
                                class="px-3 py-1.5 text-xs font-bold border border-gray-300 text-gray-600
                                       rounded-lg hover:bg-gray-50 transition-colors">
                            Edit
                        </button>
                        <button @click="hapusKandang({{ $k->kandang_id }})"
                                class="px-3 py-1.5 text-xs font-bold border border-red-200 text-[#B14B6F]
                                       rounded-lg hover:bg-red-50 transition-colors
                                       {{ $aktif > 0 ? 'opacity-40 cursor-not-allowed' : '' }}"
                                @if($aktif > 0) title="Ada domba aktif di kandang ini" @endif>
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-16 text-center text-gray-400">
                    <p class="text-4xl mb-3">🏠</p>
                    <p class="font-semibold text-gray-500 text-base">Belum ada kandang terdaftar</p>
                    <p class="text-sm mt-1 mb-4">Mulai dengan menambahkan kandang pertama.</p>
                    <button @click="modalTambah = true"
                            class="px-5 py-2 bg-[#2E7D32] text-white text-sm font-bold
                                   rounded-lg hover:bg-green-800 transition-colors">
                        Tambah Kandang Pertama
                    </button>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ══════════════════════════════
     MODAL TAMBAH KANDANG
══════════════════════════════ --}}
<div x-show="modalTambah"
     @keydown.escape.window="modalTambah = false"
     class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
     style="display: none;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-200 flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Tambah Kandang</h2>
                <p class="text-xs text-gray-500 mt-1">Kamus Data: tabel KANDANG</p>
            </div>
            <button @click="modalTambah = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-8 space-y-5">
            {{-- Error --}}
            <div x-show="errorMsg" class="bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                <p class="text-sm text-red-700 font-medium" x-text="errorMsg"></p>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">
                    Nama Kandang <span class="text-red-500">*</span>
                </label>
                <input type="text" x-model="formTambah.nama_kandang"
                       placeholder="Contoh: Kandang A, Kandang Induk 1"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                              focus:ring-2 focus:ring-[#2E7D32] focus:border-transparent outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-3">
                    Tipe Kandang <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" @click="formTambah.tipe = 'utama'"
                            :class="formTambah.tipe === 'utama'
                                ? 'border-blue-500 bg-blue-50 text-blue-700 font-bold ring-2 ring-blue-200'
                                : 'border-gray-200 text-gray-500 hover:border-gray-300'"
                            class="py-2.5 text-sm border rounded-lg transition-all text-left px-4">
                        <span class="block font-semibold">Utama</span>
                        <span class="block text-[10px] opacity-70">Kandang produktif harian</span>
                    </button>
                    <button type="button" @click="formTambah.tipe = 'isolasi'"
                            :class="formTambah.tipe === 'isolasi'
                                ? 'border-amber-500 bg-amber-50 text-amber-700 font-bold ring-2 ring-amber-200'
                                : 'border-gray-200 text-gray-500 hover:border-gray-300'"
                            class="py-2.5 text-sm border rounded-lg transition-all text-left px-4">
                        <span class="block font-semibold">Isolasi</span>
                        <span class="block text-[10px] opacity-70">Domba sakit / karantina</span>
                    </button>
                    <button type="button" @click="formTambah.tipe = 'kawin'"
                            :class="formTambah.tipe === 'kawin'
                                ? 'border-pink-500 bg-pink-50 text-pink-700 font-bold ring-2 ring-pink-200'
                                : 'border-gray-200 text-gray-500 hover:border-gray-300'"
                            class="py-2.5 text-sm border rounded-lg transition-all text-left px-4">
                        <span class="block font-semibold">Kawin</span>
                        <span class="block text-[10px] opacity-70">Area perkawinan</span>
                    </button>
                    <button type="button" @click="formTambah.tipe = 'persalinan'"
                            :class="formTambah.tipe === 'persalinan'
                                ? 'border-cyan-500 bg-cyan-50 text-cyan-700 font-bold ring-2 ring-cyan-200'
                                : 'border-gray-200 text-gray-500 hover:border-gray-300'"
                            class="py-2.5 text-sm border rounded-lg transition-all text-left px-4">
                        <span class="block font-semibold">Persalinan</span>
                        <span class="block text-[10px] opacity-70">Kandang melahirkan</span>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">
                    Kapasitas <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <input type="number" x-model="formTambah.kapasitas"
                           min="1" max="9999" placeholder="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                                  focus:ring-2 focus:ring-[#2E7D32] focus:border-transparent outline-none">
                    <span class="text-sm text-gray-500 whitespace-nowrap">ekor</span>
                </div>
            </div>
        </div>

        <div class="px-8 py-5 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <button @click="modalTambah = false"
                    class="px-6 py-2 border border-gray-300 text-gray-600 text-sm font-bold
                           rounded-lg hover:bg-gray-100 transition-colors">
                Batal
            </button>
            <button @click="submitTambah()"
                    class="px-8 py-2 bg-[#2E7D32] text-white text-sm font-bold
                           rounded-lg hover:bg-green-800 transition-colors">
                Simpan Kandang
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════
     MODAL EDIT KANDANG
══════════════════════════════ --}}
<div x-show="modalEdit"
     @keydown.escape.window="modalEdit = false"
     class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
     style="display: none;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-200 flex justify-between items-start">
            <h2 class="text-xl font-bold text-gray-900">Edit Kandang</h2>
            <button @click="modalEdit = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-8 space-y-5">
            {{-- Error --}}
            <div x-show="errorMsg" class="bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                <p class="text-sm text-red-700 font-medium" x-text="errorMsg"></p>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Nama Kandang</label>
                <input type="text" x-model="formEdit.nama_kandang"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                              focus:ring-2 focus:ring-[#2E7D32] focus:border-transparent outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-3">Tipe Kandang</label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" @click="formEdit.tipe = 'utama'"
                            :class="formEdit.tipe === 'utama'
                                ? 'border-blue-500 bg-blue-50 text-blue-700 font-bold ring-2 ring-blue-200'
                                : 'border-gray-200 text-gray-500 hover:border-gray-300'"
                            class="py-2.5 text-sm border rounded-lg transition-all text-left px-4">
                        <span class="block font-semibold">Utama</span>
                        <span class="block text-[10px] opacity-70">Kandang produktif harian</span>
                    </button>
                    <button type="button" @click="formEdit.tipe = 'isolasi'"
                            :class="formEdit.tipe === 'isolasi'
                                ? 'border-amber-500 bg-amber-50 text-amber-700 font-bold ring-2 ring-amber-200'
                                : 'border-gray-200 text-gray-500 hover:border-gray-300'"
                            class="py-2.5 text-sm border rounded-lg transition-all text-left px-4">
                        <span class="block font-semibold">Isolasi</span>
                        <span class="block text-[10px] opacity-70">Domba sakit / karantina</span>
                    </button>
                    <button type="button" @click="formEdit.tipe = 'kawin'"
                            :class="formEdit.tipe === 'kawin'
                                ? 'border-pink-500 bg-pink-50 text-pink-700 font-bold ring-2 ring-pink-200'
                                : 'border-gray-200 text-gray-500 hover:border-gray-300'"
                            class="py-2.5 text-sm border rounded-lg transition-all text-left px-4">
                        <span class="block font-semibold">Kawin</span>
                        <span class="block text-[10px] opacity-70">Area perkawinan</span>
                    </button>
                    <button type="button" @click="formEdit.tipe = 'persalinan'"
                            :class="formEdit.tipe === 'persalinan'
                                ? 'border-cyan-500 bg-cyan-50 text-cyan-700 font-bold ring-2 ring-cyan-200'
                                : 'border-gray-200 text-gray-500 hover:border-gray-300'"
                            class="py-2.5 text-sm border rounded-lg transition-all text-left px-4">
                        <span class="block font-semibold">Persalinan</span>
                        <span class="block text-[10px] opacity-70">Kandang melahirkan</span>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Kapasitas</label>
                <div class="flex items-center gap-2">
                    <input type="number" x-model="formEdit.kapasitas"
                           min="1" max="9999"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                                  focus:ring-2 focus:ring-[#2E7D32] focus:border-transparent outline-none">
                    <span class="text-sm text-gray-500 whitespace-nowrap">ekor</span>
                </div>
                <p class="text-xs text-gray-400 mt-1.5">
                    Kapasitas tidak boleh dikurangi di bawah jumlah domba aktif saat ini.
                </p>
            </div>
        </div>

        <div class="px-8 py-5 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <button @click="modalEdit = false"
                    class="px-6 py-2 border border-gray-300 text-gray-600 text-sm font-bold
                           rounded-lg hover:bg-gray-100 transition-colors">
                Batal
            </button>
            <button @click="submitEdit()"
                    class="px-8 py-2 bg-[#2E7D32] text-white text-sm font-bold
                           rounded-lg hover:bg-green-800 transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </div>
</div>

</div>{{-- /x-data --}}
@endsection
