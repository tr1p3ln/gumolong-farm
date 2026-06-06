{{--
    PARTIAL: obat-vaksin/partials/tambah-obat.blade.php
--}}

{{-- Scrollable body --}}
<div class="overflow-y-auto p-6 space-y-5" style="max-height: calc(85vh - 130px);">

    {{-- Info Banner --}}
    <div class="flex gap-2.5 bg-green-50 border border-green-200 rounded-lg px-3.5 py-3">
        <svg class="w-4 h-4 text-primary flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        <p class="text-xs text-green-800 leading-relaxed">
            Tambah stok obat, vaksin, atau vitamin baru ke inventaris. Sistem akan otomatis
            mengirimi notifikasi jika stok berada di bawah batas minimum atau mendekati
            tanggal kadaluwarsa.
        </p>
    </div>

    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Form Tambah</p>

    {{-- ── NAMA OBAT ── --}}
    <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">
            Nama Obat / Vaksin <span class="text-red-500">*Wajib</span>
        </label>
        <input type="text" name="nama_obat" form="formTambahObat"
            value="{{ old('nama_obat') }}"
            placeholder="Contoh: Vaksin Anthrax..."
            class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary placeholder-gray-300 transition
                   {{ $errors->has('nama_obat') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
            required>
        <p class="text-xs text-gray-400 mt-1">Masukkan nama komersial atau generik secara lengkap.</p>
        @error('nama_obat')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- ── TIPE ── --}}
    <div>
        <label class="block text-xs font-semibold text-gray-700 mb-2">
            Tipe <span class="text-red-500">*Wajib</span>
        </label>
        <input type="hidden" name="tipe" id="inputTipe" form="formTambahObat" value="{{ old('tipe', 'vaksin') }}">
        <div class="inline-flex rounded-lg border border-gray-300 overflow-hidden text-sm font-medium">
            @foreach(['obat' => 'Obat', 'vaksin' => 'Vaksin', 'vitamin' => 'Vitamin'] as $val => $label)
                <button type="button"
                    onclick="tambahPilihTipe('{{ $val }}')"
                    id="btn-tipe-{{ $val }}"
                    class="tipe-btn px-5 py-2 transition-colors
                           {{ old('tipe', 'vaksin') === $val
                               ? 'bg-primary text-white'
                               : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
        @error('tipe')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- ── STOK & SATUAN ── --}}
    <div>
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Stok &amp; Satuan</p>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Satuan <span class="text-red-500">*Wajib</span>
                </label>
                <select name="satuan" form="formTambahObat"
                    class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white transition
                           {{ $errors->has('satuan') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                    required>
                    <option value="">Pilih Satuan...</option>
                    @foreach(['ml' => 'ml', 'dosis' => 'Dosis', 'tablet' => 'Tablet'] as $val => $label)
                        <option value="{{ $val }}" {{ old('satuan') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('satuan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Stok Awal <span class="text-red-500">*Wajib</span>
                </label>
                <input type="number" name="stok" form="formTambahObat" min="0"
                    value="{{ old('stok', 0) }}"
                    class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary transition
                           {{ $errors->has('stok') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                    required>
                @error('stok')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ── STOK MIN & HARGA BELI ── --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">
                Stok Minimum <span class="text-red-500">*Wajib</span>
            </label>
            <input type="number" name="stok_minimum" form="formTambahObat" min="0"
                value="{{ old('stok_minimum', 0) }}"
                id="inputStokMin"
                placeholder="Batas notifikasi..."
                class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary placeholder-gray-300 transition
                       {{ $errors->has('stok_minimum') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                required>
            <p class="text-xs text-gray-400 mt-1">Notifikasi aktif jika stok ≤ nilai ini.</p>
            @error('stok_minimum')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">
                Harga Beli / Satuan <span class="text-gray-400 font-normal">Opsional</span>
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">Rp</span>
                <input type="number" name="harga_beli" form="formTambahObat" min="0" step="100"
                    value="{{ old('harga_beli') }}"
                    placeholder="0"
                    class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary placeholder-gray-300 transition
                           {{ $errors->has('harga_beli') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
            </div>
            @error('harga_beli')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- ── TANGGAL EXPIRED & INTERVAL VAKSINASI ── --}}
    <div>
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">
            Kadaluwarsa &amp; Jadwal Vaksinasi
        </p>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Tanggal Expired <span class="text-gray-400 font-normal">Opsional</span>
                </label>
                <input type="date" name="tanggal_expired" form="formTambahObat"
                    value="{{ old('tanggal_expired') }}"
                    id="inputTglExpired"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Interval Vaksinasi <span class="text-gray-400 font-normal">Opsional</span>
                </label>
                <div class="flex">
                    <input type="number" name="interval_vaksinasi" form="formTambahObat" min="1"
                        value="{{ old('interval_vaksinasi') }}"
                        placeholder="Contoh: 6"
                        class="w-full border border-gray-300 rounded-l-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary placeholder-gray-300 transition">
                    <span class="inline-flex items-center px-3 border border-l-0 border-gray-300 rounded-r-lg bg-gray-50 text-xs text-gray-500 font-medium whitespace-nowrap">
                        Bulan
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Khusus vaksin &amp; obat cacing.</p>
                @error('interval_vaksinasi')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ── KETERANGAN ── --}}
    <div>
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Keterangan &amp; Dosis Anjuran <span class="normal-case font-normal">(Opsional)</span></p>
        <label class="block text-xs font-semibold text-gray-700 mb-1">
            Keterangan / Cara Pemakaian
        </label>
        <textarea name="keterangan" form="formTambahObat" rows="3"
            placeholder="Masukkan instruksi penggunaan atau catatan khusus..."
            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary placeholder-gray-300 transition resize-none
                   {{ $errors->has('keterangan') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">{{ old('keterangan') }}</textarea>
        @error('keterangan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- ── PREVIEW NOTIFIKASI ── --}}
    <div class="rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-600 border-b border-gray-100">
            ⊙ Preview Notifikasi Sistem
        </div>
        <div class="divide-y divide-gray-100">
            <div class="flex items-center justify-between px-4 py-2.5">
                <span class="text-xs text-gray-600">Notifikasi stok menipis</span>
                <span class="text-xs font-medium text-primary" id="previewStok">
                    Aktif jika stok &lt; [ stok minimum ]
                </span>
            </div>
            <div class="flex items-center justify-between px-4 py-2.5">
                <span class="text-xs text-gray-600">Notifikasi kadaluwarsa</span>
                <span class="text-xs font-medium text-primary" id="previewExp">
                    H-30 hari dari [ tanggal expired ]
                </span>
            </div>
        </div>
    </div>

</div>{{-- /scrollable body --}}

{{-- ── STICKY FOOTER ── --}}
<form id="formTambahObat" action="{{ route('obat-vaksin.store') }}" method="POST">
    @csrf
</form>

<div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-white">
    <button type="button" onclick="closeTambahObatModal()"
        class="px-5 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-100 transition">
        Batal
    </button>
    <button type="submit" form="formTambahObat"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:opacity-90 text-white text-sm font-semibold rounded-lg transition shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Simpan Obat / Vaksin
    </button>
</div>

<script>
    function tambahPilihTipe(val) {
        document.getElementById('inputTipe').value = val;
        document.querySelectorAll('.tipe-btn').forEach(btn => {
            btn.classList.remove('bg-primary', 'text-white');
            btn.classList.add('bg-white', 'text-gray-600');
        });
        const active = document.getElementById('btn-tipe-' + val);
        if (active) {
            active.classList.remove('bg-white', 'text-gray-600');
            active.classList.add('bg-primary', 'text-white');
        }
    }

    function updatePreview() {
        const min    = document.getElementById('inputStokMin')?.value;
        const exp    = document.getElementById('inputTglExpired')?.value;
        const elStok = document.getElementById('previewStok');
        const elExp  = document.getElementById('previewExp');
        if (elStok) elStok.textContent = min ? 'Aktif jika stok < ' + min : 'Nonaktif (isi stok minimum)';
        if (elExp)  elExp.textContent  = exp ? 'H-30 hari dari [ ' + exp + ' ]' : 'Nonaktif (isi tanggal expired)';
    }

    document.getElementById('inputStokMin')?.addEventListener('input', updatePreview);
    document.getElementById('inputTglExpired')?.addEventListener('change', updatePreview);
    updatePreview();
</script>