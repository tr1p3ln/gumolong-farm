@extends('layouts.app')
@section('page-title', 'Template Tugas Rutin')

@section('content')
<div x-data="{
    modalTambah: false,
    modalEdit:   false,
    selectedId:  null,

    formTambah: {
        judul:         '',
        deskripsi:     '',
        kandang_id:    '',
        user_id:       '',
        waktu_default: '',
        prioritas:     'sedang',
    },

    formEdit: {},

    loadEdit(id, data) {
        this.selectedId = id;
        this.formEdit = {
            judul:         data.judul,
            deskripsi:     data.deskripsi     || '',
            kandang_id:    data.kandang_id,
            user_id:       data.user_id       || '',
            waktu_default: data.waktu_default ? data.waktu_default.substring(0, 5) : '',
            prioritas:     data.prioritas,
        };
        this.modalEdit = true;
    },

    async submitTambah() {
        try {
            const res = await fetch('/template-tugas', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                },
                body: JSON.stringify(this.formTambah),
            });
            const result = await res.json();
            if (result.success) window.location.reload();
        } catch (err) { console.error(err); }
    },

    async submitEdit() {
        try {
            const res = await fetch('/template-tugas/' + this.selectedId, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                },
                body: JSON.stringify(this.formEdit),
            });
            const result = await res.json();
            if (result.success) window.location.reload();
        } catch (err) { console.error(err); }
    },

    async toggleTemplate(id) {
        try {
            const res = await fetch('/template-tugas/' + id + '/toggle', {
                method:  'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept':       'application/json',
                },
            });
            const result = await res.json();
            if (result.success) window.location.reload();
        } catch (err) { console.error(err); }
    },

    async hapusTemplate(id) {
        if (!confirm('Hapus template ini? Tugas rutin yang sudah di-generate tidak akan terpengaruh.')) return;
        try {
            const res = await fetch('/template-tugas/' + id, {
                method:  'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
            });
            const result = await res.json();
            if (result.success) window.location.reload();
        } catch (err) { console.error(err); }
    },
}">

{{-- ══════════════════════════════
     PAGE HEADER
══════════════════════════════ --}}
<div class="flex justify-between items-start mb-6">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <a href="{{ route('tugas-harian.index') }}"
               class="text-gray-400 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Template Tugas Rutin</h1>
        </div>
        <p class="text-gray-500 text-sm mt-1 ml-8">
            Kelola template tugas yang di-generate otomatis setiap hari.
            <span class="font-semibold text-[#2E7D32]">{{ $templates->where('is_active', true)->count() }} aktif</span>
            dari {{ $templates->count() }} template.
        </p>
    </div>
    <button @click="modalTambah = true"
            class="flex items-center gap-2 px-5 py-2.5 bg-[#2E7D32] text-white
                   text-sm font-bold rounded-lg hover:bg-green-800 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Template
    </button>
</div>

{{-- ══════════════════════════════
     INFO BANNER
══════════════════════════════ --}}
<div class="bg-cyan-50 border border-cyan-200 rounded-xl p-4 mb-6 flex items-start gap-3">
    <svg class="w-5 h-5 text-cyan-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div class="text-sm text-cyan-800">
        <p class="font-semibold mb-0.5">Cara kerja Template Rutin</p>
        <p class="text-cyan-700 text-xs">
            Template di sini mendefinisikan tugas standar harian per kandang (pemberian pakan, pembersihan, dll).
            Gunakan tombol <strong>Generate Rutin</strong> di halaman Daily Task Monitor untuk membuat tugas nyata dari template ini
            ke tanggal yang dipilih. Tugas yang sudah ada tidak akan diduplikat.
        </p>
    </div>
</div>

{{-- ══════════════════════════════
     TABEL TEMPLATE
══════════════════════════════ --}}
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-900">Daftar Template</h3>
        <p class="text-xs text-gray-500">{{ $templates->count() }} template terdaftar</p>
    </div>

    <table class="w-full text-left">
        <thead class="border-b border-gray-200">
            <tr>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Tugas Rutin</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Kandang</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Petugas Default</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Waktu</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Prioritas</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($templates as $t)
            <tr class="hover:bg-gray-50 transition-colors {{ !$t->is_active ? 'opacity-50' : '' }}">
                <td class="px-6 py-4">
                    <p class="font-bold text-gray-900">{{ $t->judul }}</p>
                    @if($t->deskripsi)
                    <p class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $t->deskripsi }}</p>
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
                            <div class="w-7 h-7 rounded-full bg-[#607F5B] flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-xs font-bold">
                                    {{ strtoupper(substr($t->petugas->nama, 0, 1)) }}
                                </span>
                            </div>
                            <span class="text-sm font-medium text-gray-700">{{ $t->petugas->nama }}</span>
                        </div>
                    @else
                        <span class="text-xs text-gray-400 italic">Tidak di-assign</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    @if($t->waktu_default)
                    <span class="text-sm font-mono text-gray-700">
                        {{ \Carbon\Carbon::parse($t->waktu_default)->format('H:i') }}
                    </span>
                    @else
                    <span class="text-xs text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    @php
                        $pc = ['tinggi' => 'bg-red-50 border-red-200 text-red-700',
                               'sedang' => 'bg-amber-50 border-amber-200 text-amber-700',
                               'rendah' => 'bg-green-50 border-green-200 text-green-700'];
                    @endphp
                    <span class="px-2.5 py-1 text-xs font-bold border rounded-full {{ $pc[$t->prioritas] ?? '' }}">
                        {{ ucfirst($t->prioritas) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    @if($t->is_active)
                    <span class="px-2.5 py-1 text-xs font-bold border rounded-full bg-green-50 border-green-200 text-green-700">
                        Aktif
                    </span>
                    @else
                    <span class="px-2.5 py-1 text-xs font-bold border rounded-full bg-gray-100 border-gray-200 text-gray-500">
                        Nonaktif
                    </span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex gap-1.5 justify-end">
                        <button @click='loadEdit({{ $t->id }}, @json(["judul" => $t->judul, "deskripsi" => $t->deskripsi, "kandang_id" => $t->kandang_id, "user_id" => $t->user_id, "waktu_default" => $t->waktu_default, "prioritas" => $t->prioritas]))'
                                class="px-3 py-1 text-xs font-bold border border-gray-300 text-gray-600
                                       rounded-lg hover:bg-gray-50 transition-colors">
                            Edit
                        </button>
                        <button @click="toggleTemplate({{ $t->id }})"
                                class="px-3 py-1 text-xs font-bold border rounded-lg transition-colors
                                       {{ $t->is_active
                                           ? 'border-amber-200 text-amber-600 hover:bg-amber-50'
                                           : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                            {{ $t->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                        <button @click="hapusTemplate({{ $t->id }})"
                                class="px-3 py-1 text-xs font-bold border border-red-200 text-[#B14B6F]
                                       rounded-lg hover:bg-red-50 transition-colors">
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-16 text-center">
                    <p class="text-gray-400 italic mb-3">Belum ada template tugas rutin.</p>
                    <button @click="modalTambah = true"
                            class="px-5 py-2 bg-[#2E7D32] text-white text-sm font-bold rounded-lg hover:bg-green-800 transition-colors">
                        Buat Template Pertama
                    </button>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ══════════════════════════════
     MODAL TAMBAH TEMPLATE
══════════════════════════════ --}}
<div x-show="modalTambah"
     @keydown.escape.window="modalTambah = false"
     class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
     style="display: none;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-200 flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Tambah Template Tugas Rutin</h2>
                <p class="text-xs text-gray-500 mt-1">Template ini akan digunakan saat Generate Rutin dijalankan.</p>
            </div>
            <button @click="modalTambah = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-8 space-y-5">
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">
                    Judul Tugas Rutin <span class="text-red-500">*</span>
                </label>
                <input type="text" x-model="formTambah.judul"
                       placeholder="Contoh: Pemberian pakan pagi"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:ring-2 focus:ring-[#2E7D32] focus:border-transparent outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Deskripsi / Instruksi</label>
                <textarea x-model="formTambah.deskripsi" rows="2"
                          placeholder="Instruksi detail untuk petugas..."
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
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Petugas Default</label>
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
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Waktu Default</label>
                    <input type="time" x-model="formTambah.waktu_default"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-[#2E7D32] outline-none">
                    <p class="text-xs text-gray-400 mt-1">Jam mulai saat tugas di-generate</p>
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
                Simpan Template
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════
     MODAL EDIT TEMPLATE
══════════════════════════════ --}}
<div x-show="modalEdit"
     @keydown.escape.window="modalEdit = false"
     class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
     style="display: none;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-200 flex justify-between items-start">
            <h2 class="text-xl font-bold text-gray-900">Edit Template Tugas Rutin</h2>
            <button @click="modalEdit = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-8 space-y-5">
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Judul Tugas Rutin</label>
                <input type="text" x-model="formEdit.judul"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                              focus:ring-2 focus:ring-[#2E7D32] focus:border-transparent outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Deskripsi</label>
                <textarea x-model="formEdit.deskripsi" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                 focus:ring-2 focus:ring-[#2E7D32] focus:border-transparent outline-none resize-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Kandang</label>
                    <select x-model="formEdit.kandang_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#2E7D32] outline-none">
                        @foreach($kandangs as $k)
                        <option value="{{ $k->kandang_id }}">{{ $k->nama_kandang }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Petugas Default</label>
                    <select x-model="formEdit.user_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#2E7D32] outline-none">
                        <option value="">Tidak di-assign</option>
                        @foreach($petugasList as $p)
                        <option value="{{ $p->user_id }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Waktu Default</label>
                    <input type="time" x-model="formEdit.waktu_default"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[#2E7D32] outline-none">
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
