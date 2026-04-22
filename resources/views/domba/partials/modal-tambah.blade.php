{{-- ═══════════════════════════════════════════════════
    Modal Tambah Domba — 3-step wizard
    UC-MD.1 · UC-MD.5 · UC-MD.6
    Mounted via @include di domba/index.blade.php
    Memerlukan parent x-data: { modalTambah, selectedId }
    Memerlukan $kandangs dari controller
═══════════════════════════════════════════════════ --}}
<div
    x-show="modalTambah"
    x-data="{
        step: 1,
        form: {
            asal: 'lahir_di_kandang',
            jenis_kelamin: 'jantan',
            ear_tag_preview: '',
            e_ear_tag_id: '',
            nama: '',
            ras: 'Garut',
            tanggal_lahir: '',
            foto: null,
            kategori: '',
            status: 'aktif',
            kandang_id: '',
            berat_awal: 0.0,
            tanggal_timbang_awal: '',
            catatan: '',
            induk_id: '',
            ayah_id: '',
            nama_vaksin: '',
            tanggal_vaksin: '',
            dosis_vaksin: ''
        },
        loading: false,
        errors: {},
        indukData: null,
        ayahData: null,
        indukLoading: false,
        ayahLoading: false,
        indukError: '',
        ayahError: '',

        async fetchEarTag() {
            try {
                const res = await fetch('/domba/generate-ear-tag?jenis_kelamin=' + this.form.jenis_kelamin, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.form.ear_tag_preview = data.ear_tag ?? '';
            } catch (e) { console.error('fetchEarTag:', e); }
        },

        nextStep() { if (this.step < 3) this.step++; },
        prevStep() { if (this.step > 1) this.step--; },

        async searchInduk() {
            if (!this.form.induk_id) return;
            this.indukLoading = true;
            this.indukError = '';
            this.indukData = null;
            try {
                const res = await fetch('/domba/' + this.form.induk_id.trim(), {
                    headers: { 'Accept': 'application/json' }
                });
                const json = await res.json();
                if (!res.ok || !json.success) { this.indukError = 'Domba tidak ditemukan.'; return; }
                this.indukData = json.data;
            } catch { this.indukError = 'Gagal mencari data.'; }
            finally { this.indukLoading = false; }
        },

        async searchAyah() {
            if (!this.form.ayah_id) return;
            this.ayahLoading = true;
            this.ayahError = '';
            this.ayahData = null;
            try {
                const res = await fetch('/domba/' + this.form.ayah_id.trim(), {
                    headers: { 'Accept': 'application/json' }
                });
                const json = await res.json();
                if (!res.ok || !json.success) { this.ayahError = 'Domba tidak ditemukan.'; return; }
                this.ayahData = json.data;
            } catch { this.ayahError = 'Gagal mencari data.'; }
            finally { this.ayahLoading = false; }
        },

        async submitForm() {
            this.loading = true;
            this.errors = {};
            try {
                const res = await fetch('/domba', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });
                const result = await res.json();
                if (result.success) {
                    window.location.reload();
                } else {
                    this.errors = result.errors || {};
                    if (result.message && !Object.keys(this.errors).length) {
                        this.errors = { general: [result.message] };
                    }
                    this.loading = false;
                }
            } catch (e) {
                this.errors = { general: ['Gagal terhubung ke server. Coba lagi.'] };
                this.loading = false;
            }
        },

        init() {
            this.form.tanggal_timbang_awal = new Date().toISOString().split('T')[0];
            this.fetchEarTag();
            this.$watch('modalTambah', val => {
                if (val) {
                    this.step = 1;
                    this.errors = {};
                    this.indukData = null;
                    this.ayahData = null;
                    this.indukError = '';
                    this.ayahError = '';
                    this.fetchEarTag();
                }
            });
        }
    }"
    x-init="init()"
    @keydown.escape.window="if(modalTambah) { modalTambah = false; }"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-start justify-center p-4 pt-6"
    style="display: none;">

    {{-- Backdrop --}}
    <div class="absolute inset-0" @click="modalTambah = false"></div>

    {{-- ═══ MODAL CARD ═══ --}}
    <div class="relative w-full max-w-4xl bg-white rounded-xl shadow-2xl border border-gray-200 my-4" @click.stop>

        {{-- ── HEADER ── --}}
        <div class="flex items-start justify-between px-8 pt-7 pb-5 border-b border-gray-100">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Tambah Domba Baru</h2>
                <p class="text-xs text-gray-400 mt-1 font-mono tracking-wide">
                    UC-MD.1 · UC-MD.5 · UC-MD.6 | KAMUS DATA: TABEL DOMBA
                </p>
            </div>
            <button type="button" @click="modalTambah = false"
                    class="p-1.5 rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition ml-4 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- ── STEP INDICATOR ── --}}
        <div class="flex items-center justify-between px-8 py-4 bg-gray-50 border-b border-gray-100">
            <div class="flex items-center flex-1 min-w-0">

                {{-- Step 1 --}}
                <div class="flex flex-col items-center flex-shrink-0">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                         :class="step >= 1 ? 'bg-primary text-white' : 'bg-gray-300 text-white'">
                        <span x-text="step > 1 ? '✓' : '1'"></span>
                    </div>
                    <span class="text-[11px] mt-1.5 font-semibold whitespace-nowrap transition-colors"
                          :class="step >= 1 ? 'text-primary' : 'text-gray-400'">Identitas Dasar</span>
                </div>

                <div class="flex-1 h-0.5 mx-3 transition-all duration-300"
                     :class="step > 1 ? 'bg-primary' : 'bg-gray-300'"></div>

                {{-- Step 2 --}}
                <div class="flex flex-col items-center flex-shrink-0">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                         :class="step >= 2 ? 'bg-primary text-white' : 'bg-gray-300 text-white'">
                        <span x-text="step > 2 ? '✓' : '2'"></span>
                    </div>
                    <span class="text-[11px] mt-1.5 font-semibold whitespace-nowrap transition-colors"
                          :class="step >= 2 ? 'text-primary' : 'text-gray-400'">Klasifikasi</span>
                </div>

                <div class="flex-1 h-0.5 mx-3 transition-all duration-300"
                     :class="step > 2 ? 'bg-primary' : 'bg-gray-300'"></div>

                {{-- Step 3 --}}
                <div class="flex flex-col items-center flex-shrink-0">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                         :class="step >= 3 ? 'bg-primary text-white' : 'bg-gray-300 text-white'">3</div>
                    <span class="text-[11px] mt-1.5 font-semibold whitespace-nowrap transition-colors"
                          :class="step >= 3 ? 'text-primary' : 'text-gray-400'">Kandang</span>
                </div>
            </div>

            <div class="ml-6 flex-shrink-0">
                <span class="bg-gray-800 text-white text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-md">
                    STEP <span x-text="step"></span> / 3
                </span>
            </div>
        </div>

        {{-- ════════════════════════════════════════════
             STEP 1 — Identitas Dasar
        ════════════════════════════════════════════ --}}
        <div x-show="step === 1" class="px-8 py-6">

            <div class="flex items-start gap-3 bg-blue-50 border-l-4 border-primary rounded-r-md px-4 py-3 mb-6">
                <span class="text-lg flex-shrink-0 leading-none mt-0.5">ℹ️</span>
                <p class="text-sm text-blue-800">
                    Tentukan <strong>Jenis Asal</strong> domba dengan teliti. Pilihan ini akan
                    menentukan riwayat genetik dan proses klasifikasi selanjutnya.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">

                {{-- Kolom Kiri --}}
                <div class="space-y-6">

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                            Jenis Asal
                        </label>
                        <div class="flex gap-3">
                            <button type="button"
                                    @click="form.asal = 'lahir_di_kandang'"
                                    :class="form.asal === 'lahir_di_kandang'
                                        ? 'border-2 border-primary text-primary bg-white font-semibold shadow-sm'
                                        : 'border border-gray-300 text-gray-600 hover:border-gray-400'"
                                    class="flex-1 py-2.5 px-3 rounded-md text-sm transition-all text-center">
                                🏠 Lahir di Kandang
                            </button>
                            <button type="button"
                                    @click="form.asal = 'dari_luar'"
                                    :class="form.asal === 'dari_luar'
                                        ? 'border-2 border-primary text-primary bg-white font-semibold shadow-sm'
                                        : 'border border-gray-300 text-gray-600 hover:border-gray-400'"
                                    class="flex-1 py-2.5 px-3 rounded-md text-sm transition-all text-center">
                                🚚 Dari Luar
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                            Jenis Kelamin
                        </label>
                        <div class="flex gap-3">
                            <button type="button"
                                    @click="form.jenis_kelamin = 'jantan'; fetchEarTag()"
                                    :class="form.jenis_kelamin === 'jantan'
                                        ? 'border-2 border-primary text-primary bg-white font-semibold shadow-sm'
                                        : 'border border-gray-300 text-gray-600 hover:border-gray-400'"
                                    class="flex-1 py-2.5 px-3 rounded-md text-sm transition-all text-center">
                                ♂ Jantan
                            </button>
                            <button type="button"
                                    @click="form.jenis_kelamin = 'betina'; fetchEarTag()"
                                    :class="form.jenis_kelamin === 'betina'
                                        ? 'border-2 border-primary text-primary bg-white font-semibold shadow-sm'
                                        : 'border border-gray-300 text-gray-600 hover:border-gray-400'"
                                    class="flex-1 py-2.5 px-3 rounded-md text-sm transition-all text-center">
                                ♀ Betina
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                            Kode Unik (Ear Tag)
                        </label>
                        <div class="relative">
                            <input type="text"
                                   x-model="form.ear_tag_preview"
                                   readonly
                                   placeholder="..."
                                   class="w-full border border-gray-200 rounded-md px-3 py-2.5 pr-10 text-sm bg-gray-100 font-mono text-gray-600 cursor-not-allowed">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 select-none">🔒</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Auto-generated berdasarkan jenis kelamin</p>
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="space-y-4">

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                            E-Ear Tag ID
                            <span class="font-normal normal-case text-gray-400">(opsional)</span>
                        </label>
                        <input type="text"
                               x-model="form.e_ear_tag_id"
                               placeholder="Nomor elektronik ear tag"
                               class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                            Nama Domba
                        </label>
                        <input type="text"
                               x-model="form.nama"
                               placeholder="Udin"
                               class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                            Ras / Breed <span class="text-accent">*</span>
                        </label>
                        <input type="text"
                               x-model="form.ras"
                               placeholder="Garut"
                               class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                        <p x-show="errors.ras" x-text="errors.ras?.[0]" class="text-xs text-accent mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                            Tanggal Lahir
                        </label>
                        <input type="date"
                               x-model="form.tanggal_lahir"
                               class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                    </div>

                    <!-- <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                            Foto Domba
                        </label>
                        <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-primary hover:bg-green-50 transition-all group">
                            <svg class="w-7 h-7 text-gray-400 group-hover:text-primary mb-1.5 transition-colors"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-xs text-gray-500 group-hover:text-primary transition-colors"
                                  x-show="!form.foto">Klik untuk upload foto</span>
                            <span class="text-xs text-primary font-medium truncate max-w-xs"
                                  x-show="form.foto" x-text="form.foto"></span>
                            <input type="file" class="hidden" accept="image/*"
                                   @change="form.foto = $event.target.files[0]?.name ?? null">
                        </label>
                    </div> -->
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════
             STEP 2 — Klasifikasi & Kandang
        ════════════════════════════════════════════ --}}
        <div x-show="step === 2" class="px-8 py-6">

            <div class="flex items-start gap-3 bg-blue-50 border-l-4 border-primary rounded-r-md px-4 py-3 mb-6">
                <span class="text-lg flex-shrink-0 leading-none mt-0.5">ℹ️</span>
                <p class="text-sm text-blue-800">
                    Tentukan <strong>kategori, status awal, dan penempatan kandang</strong> domba.
                    Data berat awal digunakan sebagai titik referensi awal tracking pertumbuhan <em>(FR-4)</em>.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">

                {{-- Kolom Kiri --}}
                <div class="space-y-4">

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                            Kategori <span class="text-accent">*</span>
                        </label>
                        <select x-model="form.kategori"
                                class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                            <option value="">— Pilih Kategori —</option>
                            <option value="cempe">Cempe</option>
                            <option value="dara">Dara</option>
                            <option value="indukan">Indukan</option>
                            <option value="pejantan">Pejantan</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">
                            Kamus Data: ENUM kategori tabel DOMBA | Cempe = anak &lt; 6 bulan
                        </p>
                        <p x-show="errors.kategori" x-text="errors.kategori?.[0]" class="text-xs text-accent mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                            Status
                        </label>
                        <select x-model="form.status"
                                class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                            <option value="aktif">Aktif</option>
                            <option value="karantina">Karantina</option>
                            <option value="terjual">Terjual</option>
                            <option value="mati">Mati</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Kamus Data: ENUM status tabel DOMBA</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                            Penempatan Kandang <span class="text-accent">*</span>
                        </label>
                        <select x-model="form.kandang_id"
                                class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                            <option value="">— Pilih Kandang —</option>
                            @foreach($kandangs as $kandang)
                                <option value="{{ $kandang->kandang_id }}">{{ $kandang->nama_kandang }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">
                            Kandang Isolasi khusus untuk domba berstatus Karantina
                        </p>
                        <p x-show="errors.kandang_id" x-text="errors.kandang_id?.[0]" class="text-xs text-accent mt-1"></p>
                    </div>

                    <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-600 mb-3">
                            Kapasitas Kandang:
                        </p>
                        <ul class="space-y-2">
                            @foreach($kandangs as $k)
                                <li class="flex items-center justify-between text-xs">
                                    <span class="font-medium text-gray-700">{{ $k->nama_kandang }}</span>
                                    <span class="text-gray-500">
                                        {{ $k->kapasitas }} ekor ·
                                        <span class="{{ $k->sisa_slot > 0 ? 'text-primary font-semibold' : 'text-accent font-semibold' }}">
                                            tersisa {{ $k->sisa_slot }} slot
                                        </span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="space-y-4">

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                            Berat Awal (kg)
                        </label>
                        <input type="number" x-model="form.berat_awal"
                               step="0.1" min="0" placeholder="0.0"
                               class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                        <p class="text-xs text-gray-400 mt-1">
                            Menjadi titik awal di modul Tracking Pertumbuhan (FR-4)
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                            Tanggal Timbang Awal
                        </label>
                        <input type="date" x-model="form.tanggal_timbang_awal"
                               class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                        <p class="text-xs text-gray-400 mt-1">
                            Otomatis tercatat ke tabel PENIMBANGAN sebagai record pertama
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                            Catatan / Keterangan
                        </label>
                        <textarea x-model="form.catatan" rows="4" placeholder="Sehat Lengkap"
                                  class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition resize-none"></textarea>
                        <p class="text-xs text-gray-400 mt-1">
                            Kamus Data: kolom catatan TEXT NULL tabel DOMBA
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════
             STEP 3 — Silsilah & Tambahan
        ════════════════════════════════════════════ --}}
        <div x-show="step === 3" class="px-8 py-6 space-y-6">

            <div class="flex items-start gap-3 bg-blue-50 border-l-4 border-primary rounded-r-md px-4 py-3">
                <span class="text-lg flex-shrink-0 leading-none mt-0.5">ℹ️</span>
                <p class="text-sm text-blue-800">
                    Data <strong>silsilah</strong> digunakan untuk Pedigree Tracking dan deteksi inbreeding
                    <em>(FR-7)</em>. Lewati bagian ini jika domba tidak diketahui asal-usulnya.
                </p>
            </div>

            {{-- Silsilah / Pedigree --}}
            <div>
                <h3 class="flex items-center gap-3 text-xs font-bold uppercase tracking-wider text-gray-500 mb-4">
                    <span>Silsilah / Pedigree</span>
                    <span class="flex-1 border-t border-gray-200"></span>
                </h3>

                <div class="grid md:grid-cols-2 gap-6">

                    {{-- Induk Betina --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                            ID Induk Betina / Ibu
                        </label>
                        <div class="flex gap-2">
                            <input type="text" x-model="form.induk_id" placeholder="B-001"
                                   class="flex-1 border border-gray-300 rounded-md px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary transition">
                            <button type="button"
                                    @click="searchInduk()"
                                    :disabled="indukLoading || !form.induk_id"
                                    class="px-4 py-2 border border-primary text-primary text-sm font-semibold rounded-md hover:bg-green-50 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1.5">
                                <svg x-show="indukLoading" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <span x-text="indukLoading ? '' : 'Cari'"></span>
                            </button>
                        </div>
                        <p x-show="indukError" x-text="indukError" class="text-xs text-accent mt-1.5"></p>
                        <div x-show="indukData"
                             class="mt-2 bg-green-50 border border-primary rounded-md px-3 py-2 flex items-center justify-between gap-2">
                            <div class="text-xs min-w-0 truncate">
                                <span class="font-mono font-bold text-primary" x-text="indukData?.ear_tag_id"></span>
                                <span class="text-gray-600"
                                      x-text="' | ' + (indukData?.kategori ?? '') + ' — ' + (indukData?.ras ?? '') + ' — ' + (indukData?.kandang?.nama_kandang ?? '—')">
                                </span>
                            </div>
                            <button type="button" @click="indukData = null; form.induk_id = ''"
                                    class="text-accent text-xs font-semibold hover:underline flex-shrink-0">× Hapus</button>
                        </div>
                        <div x-show="!indukData && !form.induk_id"
                             class="mt-2 border-2 border-dashed border-gray-200 rounded-md py-2 text-xs text-gray-400 text-center">
                            Belum ada induk dipilih
                        </div>
                    </div>

                    {{-- Pejantan / Ayah --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                            ID Pejantan / Ayah
                        </label>
                        <div class="flex gap-2">
                            <input type="text" x-model="form.ayah_id" placeholder="J-001"
                                   class="flex-1 border border-gray-300 rounded-md px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary transition">
                            <button type="button"
                                    @click="searchAyah()"
                                    :disabled="ayahLoading || !form.ayah_id"
                                    class="px-4 py-2 border border-primary text-primary text-sm font-semibold rounded-md hover:bg-green-50 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1.5">
                                <svg x-show="ayahLoading" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <span x-text="ayahLoading ? '' : 'Cari'"></span>
                            </button>
                        </div>
                        <p x-show="ayahError" x-text="ayahError" class="text-xs text-accent mt-1.5"></p>
                        <div x-show="ayahData"
                             class="mt-2 bg-green-50 border border-primary rounded-md px-3 py-2 flex items-center justify-between gap-2">
                            <div class="text-xs min-w-0 truncate">
                                <span class="font-mono font-bold text-primary" x-text="ayahData?.ear_tag_id"></span>
                                <span class="text-gray-600"
                                      x-text="' | ' + (ayahData?.kategori ?? '') + ' — ' + (ayahData?.ras ?? '') + ' — ' + (ayahData?.kandang?.nama_kandang ?? '—')">
                                </span>
                            </div>
                            <button type="button" @click="ayahData = null; form.ayah_id = ''"
                                    class="text-accent text-xs font-semibold hover:underline flex-shrink-0">× Hapus</button>
                        </div>
                        <div x-show="!ayahData && !form.ayah_id"
                             class="mt-2 border-2 border-dashed border-gray-200 rounded-md py-2 text-xs text-gray-400 text-center">
                            Belum ada pejantan dipilih
                        </div>
                    </div>
                </div>

                {{-- Inbreeding Warning --}}
                <div x-show="form.induk_id && form.ayah_id"
                     class="mt-4 bg-rose-50 border-l-4 border-accent rounded-r-md px-4 py-3">
                    <div class="flex items-start gap-2">
                        <span class="flex-shrink-0 mt-0.5">⚠️</span>
                        <div>
                            <p class="text-sm font-bold text-accent">Deteksi Inbreeding (FR-7):</p>
                            <p class="text-xs text-rose-700 mt-0.5">
                                Sistem akan otomatis memperingatkan jika induk betina dan pejantan
                                memiliki garis keturunan yang sama.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Vaksinasi Awal --}}
            <div>
                <h3 class="flex items-center gap-3 text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">
                    <span>Vaksinasi Awal</span>
                    <span class="flex-1 border-t border-gray-200"></span>
                </h3>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Nama Vaksin</label>
                        <input type="text" x-model="form.nama_vaksin" placeholder="Orf / Brucellosis"
                               class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary transition">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Tanggal Vaksin</label>
                        <input type="date" x-model="form.tanggal_vaksin"
                               class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary transition">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Dosis (ml)</label>
                        <input type="number" x-model="form.dosis_vaksin" step="0.5" min="0" placeholder="0"
                               class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary transition">
                    </div>
                </div>
            </div>

            {{-- Ringkasan --}}
            <div class="bg-gray-50 border border-gray-200 rounded-md p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-4">
                    Ringkasan Data yang Akan Disimpan
                </p>
                <div class="grid grid-cols-4 gap-4 mb-4">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400 mb-1">Kode Unik</p>
                        <p class="font-mono font-bold text-gray-900 text-sm" x-text="form.ear_tag_preview || '—'"></p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400 mb-1">Jenis Kelamin</p>
                        <p class="font-semibold text-gray-900 text-sm"
                           x-text="form.jenis_kelamin === 'jantan' ? '♂ Jantan' : '♀ Betina'"></p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400 mb-1">Jenis Asal</p>
                        <p class="font-semibold text-gray-900 text-sm"
                           x-text="form.asal === 'lahir_di_kandang' ? 'Lahir di Kandang' : 'Dari Luar'"></p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400 mb-1">Status Awal</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-primary"
                              x-text="form.status ? (form.status.charAt(0).toUpperCase() + form.status.slice(1)) : 'Aktif'">
                        </span>
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-3 flex items-center gap-2 text-xs text-gray-400">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 7v10c0 2.21 3.582 3 8 3s8-.79 8-3V7M4 7c0 2.21 3.582 3 8 3s8-3 8-3M4 7c0-2.21 3.582-3 8-3s8 .79 8 3"/>
                    </svg>
                    DATA AKAN DISIMPAN KE TABEL DOMBA → TABEL PENIMBANGAN → TABEL REKAM_MEDIS
                </div>
            </div>

            {{-- Validation errors --}}
            <div x-show="Object.keys(errors).length > 0"
                 class="bg-red-50 border border-red-200 rounded-md p-3">
                <p class="text-sm font-semibold text-red-700 mb-1">Terdapat kesalahan validasi:</p>
                <ul class="text-xs text-red-600 list-disc pl-4 space-y-0.5">
                    <template x-for="(msgs, field) in errors" :key="field">
                        <template x-for="msg in (Array.isArray(msgs) ? msgs : [msgs])" :key="msg">
                            <li x-text="msg"></li>
                        </template>
                    </template>
                </ul>
            </div>
        </div>

        {{-- ── FOOTER ── --}}
        <div class="px-8 py-5 bg-gray-50 border-t border-gray-200 rounded-b-xl flex items-center justify-between">

            <button type="button"
                    @click="prevStep()"
                    :disabled="step === 1"
                    :class="step === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100'"
                    class="px-5 py-2.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 transition">
                ← Kembali
            </button>

            <div class="flex items-center gap-3">
                {{-- Steps 1 & 2: Next --}}
                <button type="button"
                        x-show="step < 3"
                        @click="nextStep()"
                        class="px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-md hover:opacity-90 transition shadow-sm">
                    <span x-show="step === 1">Lanjut: Klasifikasi &amp; Kandang →</span>
                    <span x-show="step === 2">Lanjut: Silsilah &amp; Tambahan →</span>
                </button>

                {{-- Step 3: Cancel + Submit --}}
                <template x-if="step === 3">
                    <div class="flex items-center gap-3">
                        <button type="button"
                                @click="modalTambah = false"
                                class="px-5 py-2.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="button"
                                @click="submitForm()"
                                :disabled="loading"
                                class="px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-md hover:opacity-90 transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center gap-2">
                            <svg x-show="loading" class="animate-spin w-4 h-4 flex-shrink-0"
                                 fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span x-text="loading ? 'Menyimpan...' : '✓ Simpan Domba Baru'"></span>
                        </button>
                    </div>
                </template>
            </div>
        </div>

    </div>
</div>
