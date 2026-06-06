{{-- ═══════════════════════════════════════════════════
    Modal Edit Domba — UC-MD.2
    Mounted via @include di domba/index.blade.php
    Memerlukan parent x-data: { modalEdit, modalHapus, selectedId, openHapus() }
    Memerlukan $kandangs dari controller
═══════════════════════════════════════════════════ --}}
<div
    x-show="modalEdit"
    x-data="{
        loading: false,
        dombaData: null,
        form: {
            nama: '',
            ras: '',
            tanggal_lahir: '',
            asal: '',
            kategori: '',
            status: '',
            kandang_id: '',
            catatan: ''
        },
        errors: {},

        async loadDomba(earTagId) {
            if (!earTagId) return;
            this.loading = true;
            this.dombaData = null;
            this.errors = {};
            try {
                const res = await fetch('/domba/' + earTagId, {
                    headers: { 'Accept': 'application/json' }
                });
                const json = await res.json();
                if (!json.success) return;

                this.dombaData = json.data;
                this.form = {
                    nama:          this.dombaData.nama          || '',
                    ras:           this.dombaData.ras           || '',
                    tanggal_lahir: this.dombaData.tanggal_lahir
                                       ? String(this.dombaData.tanggal_lahir).substring(0, 10) : '',
                    asal:          this.dombaData.asal          || 'lahir_di_kandang',
                    kategori:      this.dombaData.kategori      || '',
                    status:        this.dombaData.status        || '',
                    kandang_id:    String(this.dombaData.kandang_id || ''),
                    catatan:       this.dombaData.catatan       || ''
                };
            } catch (err) {
                console.error('Error loading domba:', err);
            } finally {
                this.loading = false;
            }
        },

        async saveChanges() {
            if (!this.dombaData) return;
            this.loading = true;
            this.errors = {};
            try {
                const res = await fetch('/domba/' + this.dombaData.ear_tag_id, {
                    method: 'PUT',
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
                }
            } catch (err) {
                console.error('Error saving:', err);
                this.errors = { general: ['Terjadi kesalahan saat menyimpan data.'] };
            } finally {
                this.loading = false;
            }
        }
    }"
    x-init="$watch('modalEdit', val => { if (val) loadDomba(selectedId) })"
    @keydown.escape.window="if (modalEdit) { modalEdit = false; }"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-start justify-center p-4"
    style="display: none;">

    {{-- Backdrop --}}
    <div class="absolute inset-0" @click="modalEdit = false"></div>

    {{-- ═══ MODAL CARD ═══ --}}
    <div class="relative w-full max-w-5xl mx-auto bg-white rounded-xl shadow-2xl overflow-hidden my-4" @click.stop>

        {{-- ── HEADER ── --}}
        <div class="px-8 py-6 flex justify-between items-start border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-900"
                    x-text="dombaData
                        ? 'Edit Data Domba — ' + dombaData.ear_tag_id + ' (' + (dombaData.nama || 'Tanpa Nama') + ')'
                        : 'Edit Data Domba'">
                </h1>
                <p class="text-xs text-gray-500 font-medium mt-1"
                   x-text="dombaData
                       ? 'UC-MD.2 | Kamus Data: tabel DOMBA | ID: ' + (dombaData.e_ear_tag_id || '-')
                       : 'Memuat data...'">
                </p>
            </div>
            <button type="button" @click="modalEdit = false"
                    class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-md flex-shrink-0 ml-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- ── INFO BANNER EDIT MODE ── --}}
        <div class="mx-8 mt-6 bg-gray-50 border-l-4 border-secondary p-4 flex gap-3 items-center rounded-r">
            <svg class="w-5 h-5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-gray-700 leading-tight">
                <span class="font-bold">EDIT MODE</span> — Beberapa field dikunci untuk menjaga integritas data.
                Ear Tag, Kode Unik, dan Jenis Kelamin tidak dapat diubah setelah domba terdaftar.
            </p>
        </div>

        {{-- ── LOADING STATE ── --}}
        <div x-show="loading && !dombaData" class="p-8 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-300 border-t-primary"></div>
            <p class="mt-4 text-gray-500 text-sm">Memuat data domba...</p>
        </div>

        {{-- ── MAIN CONTENT ── --}}
        <div x-show="dombaData" class="p-8 space-y-8">

            {{-- Validation errors --}}
            <div x-show="Object.keys(errors).length > 0"
                 class="bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-sm font-bold text-red-700 mb-1">Terdapat kesalahan:</p>
                <ul class="text-xs text-red-600 list-disc pl-4 space-y-0.5">
                    <template x-for="(msgs, field) in errors" :key="field">
                        <template x-for="msg in (Array.isArray(msgs) ? msgs : [msgs])" :key="msg">
                            <li x-text="msg"></li>
                        </template>
                    </template>
                </ul>
            </div>

            {{-- SECTION 1: IDENTITAS TETAP --}}
            <section class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-black tracking-widest text-gray-500 uppercase">
                        Identitas Tetap (Tidak Dapat Diubah)
                    </span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>

                <div class="border-b border-gray-300 mb-4 opacity-30"></div>

                <div class="grid grid-cols-4 gap-6">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Ear Tag / Kode</p>
                        <p class="font-mono font-bold text-gray-900" x-text="dombaData?.ear_tag_id"></p>
                        <p class="text-xs text-gray-500 italic" x-text="dombaData?.e_ear_tag_id || '-'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Nama</p>
                        <p class="font-bold text-gray-900" x-text="dombaData?.nama || '-'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Jenis Kelamin</p>
                        <p class="font-bold text-gray-900"
                           x-text="dombaData?.jenis_kelamin === 'betina' ? 'Betina ♀' : 'Jantan ♂'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Kode Unik</p>
                        <p class="font-mono font-bold text-primary" x-text="dombaData?.ear_tag_id"></p>
                    </div>
                </div>

                <div class="pt-4 mt-4 border-t border-gray-200">
                    <p class="text-xs italic text-gray-500">
                        Field ini dikunci. Perubahan ear tag atau jenis kelamin memerlukan proses registrasi ulang.
                    </p>
                </div>
            </section>

            {{-- SECTION 2: EDITABLE FIELDS --}}
            <div class="grid grid-cols-2 gap-10">

                {{-- Kolom Kiri --}}
                <div class="space-y-6">

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2">
                            Nama Domba <span class="font-normal text-gray-400">(optional)</span>
                        </label>
                        <input type="text" x-model="form.nama"
                               class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2">
                            Ras <span class="text-accent">*</span>
                        </label>
                        <input type="text" x-model="form.ras"
                               class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                        <p x-show="errors.ras" x-text="errors.ras?.[0]" class="text-xs text-accent mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2">
                            Tanggal Lahir <span class="font-normal text-gray-400">(optional)</span>
                        </label>
                        <input type="date" x-model="form.tanggal_lahir"
                               class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-3">
                            Jenis Asal <span class="text-accent">*</span>
                        </label>
                        <div class="flex gap-3">
                            <button type="button"
                                    @click="form.asal = 'lahir_di_kandang'"
                                    :class="form.asal === 'lahir_di_kandang'
                                        ? 'border-2 border-primary text-primary bg-white font-semibold'
                                        : 'border border-gray-300 text-gray-600 hover:border-gray-400'"
                                    class="px-4 py-2 text-xs font-bold rounded-full transition-colors flex items-center gap-2">
                                <span x-show="form.asal === 'lahir_di_kandang'" class="text-primary">✓</span>
                                Lahir di Kandang
                            </button>
                            <button type="button"
                                    @click="form.asal = 'dari_luar'"
                                    :class="form.asal === 'dari_luar'
                                        ? 'border-2 border-primary text-primary bg-white font-semibold'
                                        : 'border border-gray-300 text-gray-600 hover:border-gray-400'"
                                    class="px-4 py-2 text-xs font-bold rounded-full transition-colors flex items-center gap-2">
                                <span x-show="form.asal === 'dari_luar'" class="text-primary">✓</span>
                                Pembelian Luar
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="space-y-6">

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2">
                            Kategori <span class="text-accent">*</span>
                        </label>
                        <select x-model="form.kategori"
                                class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm
                                       focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                            <option value="">Pilih Kategori</option>
                            <option value="cempe">Cempe</option>
                            <option value="dara">Dara</option>
                            <option value="indukan">Indukan</option>
                            <option value="pejantan">Pejantan</option>
                        </select>
                        <p x-show="errors.kategori" x-text="errors.kategori?.[0]" class="text-xs text-accent mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2">
                            Status <span class="text-accent">*</span>
                        </label>
                        <select x-model="form.status"
                                class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm
                                       focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                            <option value="aktif">Aktif</option>
                            <option value="karantina">Karantina</option>
                            <option value="terjual">Terjual</option>
                            <option value="mati">Mati</option>
                        </select>

                        {{-- Status warning --}}
                        <div class="mt-3 bg-rose-50 border-l-4 border-accent p-4 rounded-r">
                            <p class="text-xs font-bold text-accent flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Perhatian perubahan status:
                            </p>
                            <ul class="text-xs text-gray-700 space-y-1 ml-6 list-disc">
                                <li><span class="font-bold">Terjual:</span> Domba akan dipindahkan ke arsip inventaris.</li>
                                <li><span class="font-bold">Mati:</span> Diperlukan input penyebab kematian untuk laporan kesehatan.</li>
                                <li><span class="font-bold">Karantina:</span> Menghilangkan domba dari daftar distribusi pakan reguler.</li>
                            </ul>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2">
                            Penempatan Kandang <span class="text-accent">*</span>
                        </label>
                        <select x-model="form.kandang_id"
                                class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm
                                       focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                            <option value="">Pilih Kandang</option>
                            @foreach($kandangs as $kandang)
                                <option value="{{ $kandang->kandang_id }}">{{ $kandang->nama_kandang }}</option>
                            @endforeach
                        </select>
                        <p x-show="errors.kandang_id" x-text="errors.kandang_id?.[0]" class="text-xs text-accent mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2">
                            Catatan / Keterangan <span class="font-normal text-gray-400">(optional)</span>
                        </label>
                        <textarea x-model="form.catatan" rows="4"
                                  placeholder="Kondisi sehat, nafsu makan baik."
                                  class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm
                                         focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition resize-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: RIWAYAT PERUBAHAN --}}
            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                <p class="text-xs font-bold text-gray-700 mb-4">Riwayat Perubahan Terakhir</p>
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center flex-shrink-0">
                            <div class="w-2 h-2 rounded-full bg-primary mt-1"></div>
                            <div class="w-0.5 flex-1 bg-gray-200 mt-1"></div>
                        </div>
                        <div class="pb-1">
                            <p class="text-xs font-bold text-gray-900 leading-none">
                                {{ auth()->user()?->nama ?? auth()->user()?->name ?? 'Admin' }}
                                ({{ ucfirst(str_replace('_', ' ', auth()->user()?->role ?? 'Admin')) }})
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Update penempatan kandang</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ now()->subDays(2)->format('d M Y • H:i') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center flex-shrink-0">
                            <div class="w-2 h-2 rounded-full bg-gray-300 mt-1"></div>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-900 leading-none">Ahmad (Petugas)</p>
                            <p class="text-xs text-gray-500 mt-1">Update catatan kesehatan rutin</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ now()->subDays(5)->format('d M Y • H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /dombaData --}}

        {{-- ── FOOTER ── --}}
        <div class="px-8 py-6 bg-gray-100 border-t border-gray-200 flex justify-between items-center">

            {{-- Kiri: Hapus (Super Admin only) --}}
            <div class="flex flex-col">
                @if(auth()->check() && auth()->user()?->role === 'super_admin')
                    <button type="button"
                            @click="modalEdit = false; openHapus(dombaData?.ear_tag_id)"
                            class="flex items-center gap-2 px-4 py-2 border border-accent text-accent
                                   text-xs font-bold rounded-lg hover:bg-rose-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Domba
                    </button>
                    <span class="text-xs text-gray-400 mt-1 ml-1">(Hanya Super Admin)</span>
                @else
                    <div></div>
                @endif
            </div>

            {{-- Kanan: Batal + Simpan --}}
            <div class="flex gap-3">
                <button type="button"
                        @click="modalEdit = false"
                        class="px-6 py-2 border border-gray-300 text-gray-700 text-sm font-bold rounded-lg
                               hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="button"
                        @click="saveChanges()"
                        :disabled="loading"
                        class="flex items-center gap-2 px-8 py-2 bg-primary text-white text-sm font-bold
                               rounded-lg shadow-md hover:opacity-90 transition-all
                               disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="loading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                </button>
            </div>
        </div>

    </div>
</div>
