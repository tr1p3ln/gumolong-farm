{{-- PARTIAL: obat-vaksin/partials/edit-obat.blade.php --}}

<div class="overflow-y-auto p-6 space-y-5" style="max-height: calc(85vh - 130px);">

    {{-- ── NAMA OBAT ── --}}
    <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">
            Nama Obat / Vaksin <span class="text-red-500">*Wajib</span>
        </label>
        <input type="text" name="nama_obat" id="edit_nama_obat" form="formEditObat"
            placeholder="Contoh: Vaksin Anthrax..."
            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary placeholder-gray-300 transition"
            required>
    </div>

    {{-- ── TIPE ── --}}
    <div>
        <label class="block text-xs font-semibold text-gray-700 mb-2">
            Tipe <span class="text-red-500">*Wajib</span>
        </label>
        <input type="hidden" name="tipe" id="edit_inputTipe" form="formEditObat" value="vaksin">
        <div class="inline-flex rounded-lg border border-gray-300 overflow-hidden text-sm font-medium">
            @foreach(['obat' => 'Obat', 'vaksin' => 'Vaksin', 'vitamin' => 'Vitamin'] as $val => $label)
                <button type="button"
                    onclick="editPilihTipe('{{ $val }}')"
                    id="edit_btn_tipe_{{ $val }}"
                    class="edit-tipe-btn px-5 py-2 transition-colors bg-white text-gray-600 hover:bg-gray-50">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── SATUAN & STOK ── --}}
    <div>
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">
            Stok &amp; Satuan
        </p>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Satuan <span class="text-red-500">*Wajib</span>
                </label>
                <select name="satuan" id="edit_satuan" form="formEditObat"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white transition"
                    required>
                    <option value="">Pilih Satuan...</option>
                    @foreach(['botol'=>'Botol','dosis'=>'Dosis','vial'=>'Vial','tablet'=>'Tablet','kapsul'=>'Kapsul','sachet'=>'Sachet','ml'=>'Ml','gram'=>'Gram','liter'=>'Liter','ampul'=>'Ampul'] as $v => $l)
                        <option value="{{ $v }}">{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Stok <span class="text-red-500">*Wajib</span>
                </label>
                <input type="number" name="stok" id="edit_stok" form="formEditObat" min="0"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary transition"
                    required>
            </div>
        </div>
    </div>

    {{-- ── STOK MIN ── --}}
    <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">
            Stok Minimum <span class="text-red-500">*Wajib</span>
        </label>
        <input type="number" name="stok_minimum" id="edit_stok_minimum" form="formEditObat" min="0"
            placeholder="Batas notifikasi..."
            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary placeholder-gray-300 transition"
            required>
        <p class="text-xs text-gray-400 mt-1">Notifikasi aktif jika stok ≤ nilai ini.</p>
    </div>

    {{-- ── TANGGAL EXPIRED ── --}}
    <div>
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">
            Kadaluwarsa
        </p>
        <label class="block text-xs font-semibold text-gray-700 mb-1">
            Tanggal Expired <span class="text-gray-400 font-normal">Opsional</span>
        </label>
        <input type="date" name="tanggal_expired" id="edit_tanggal_expired" form="formEditObat"
            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary transition">
    </div>

</div>

{{-- Form tersembunyi — action di-set dinamis via JS --}}
<form id="formEditObat" method="POST">
    @csrf
    @method('PUT')
</form>

{{-- Sticky Footer --}}
<div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-white flex-shrink-0">
    <button type="button" onclick="closeEditObatModal()"
        class="px-5 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-100 transition">
        Batal
    </button>
    <button type="submit" form="formEditObat" id="editSubmitBtn"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:opacity-90 text-white text-sm font-semibold rounded-lg transition shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Simpan Perubahan
    </button>
</div>