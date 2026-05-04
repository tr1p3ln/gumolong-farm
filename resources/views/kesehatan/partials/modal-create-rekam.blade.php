{{-- Modal: Tambah Rekam Medis --}}
<div x-show="showCreateRekam" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     @keydown.escape.window="showCreateRekam = false">

    <div class="bg-white w-full max-w-[640px] max-h-[92vh] shadow-2xl rounded-xl flex flex-col border border-surface-container"
         @click.outside="showCreateRekam = false">

        {{-- Header --}}
        <header class="flex items-start justify-between px-6 py-5 border-b border-surface-container bg-white shrink-0">
            <div>
                <h2 class="text-xl font-bold text-on-surface">Tambah Rekam Medis</h2>
                <p class="text-xs text-outline mt-1">UC-05-02 | Kamus Data: tabel MEDICAL_RECORD</p>
            </div>
            <button type="button" @click="showCreateRekam = false"
                    class="text-outline hover:text-on-surface transition-colors">
                <span class="material-symbols-outlined text-2xl">close</span>
            </button>
        </header>

        <form method="POST" action="{{ route('kesehatan.store') }}" class="overflow-y-auto flex-1">
            @csrf
            <main class="p-6 flex flex-col gap-6">

                {{-- SECTION 1: Pilih Domba --}}
                <section class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold text-on-surface uppercase tracking-wider">
                            Pilih Domba <span class="text-error">*</span>
                        </label>
                        <div class="relative" x-show="!selectedDomba">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none">search</span>
                            <input type="text" x-model="dombaSearch"
                                   placeholder="Cari Ear Tag atau nama domba..."
                                   class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-sm"/>
                        </div>

                        {{-- Dropdown hasil --}}
                        <div x-show="dombaSearch.length > 0 && !selectedDomba"
                             class="border border-outline-variant rounded-lg divide-y divide-surface-container max-h-44 overflow-y-auto bg-white shadow-lg">
                            <template x-for="d in filteredDombaRekam.slice(0, 8)" :key="d.ear_tag_id">
                                <div @click="selectedDomba = d; dombaSearch = d.ear_tag_id"
                                     class="px-4 py-2.5 flex items-center gap-3 hover:bg-surface-container-low cursor-pointer transition-colors">
                                    <span class="font-bold text-primary text-sm" x-text="d.ear_tag_id"></span>
                                    <span class="text-xs text-outline" x-text="d.nama ? '· ' + d.nama : ''"></span>
                                    <span class="ml-auto text-xs bg-surface-container px-2 py-0.5 rounded" x-text="d.ras"></span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded"
                                          :class="d.status === 'karantina'
                                            ? 'bg-error-container text-on-error-container'
                                            : 'bg-secondary-container text-on-secondary-container'"
                                          x-text="d.kategori"></span>
                                </div>
                            </template>
                            <div x-show="filteredDombaRekam.length === 0"
                                 class="px-4 py-3 text-sm text-outline text-center italic">
                                Tidak ada domba ditemukan
                            </div>
                        </div>
                    </div>

                    {{-- Info row card (shown after domba selected) --}}
                    <div x-show="selectedDomba"
                         class="grid grid-cols-3 gap-4 p-4 bg-surface-container-lowest border border-outline-variant rounded-lg">
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-bold text-outline uppercase tracking-wider">EAR TAG</span>
                            <span class="text-sm font-bold text-on-surface" x-text="selectedDomba?.ear_tag_id"></span>
                        </div>
                        <div class="flex flex-col gap-1 border-l border-outline-variant pl-4">
                            <span class="text-[10px] font-bold text-outline uppercase tracking-wider">KATEGORI</span>
                            <span class="text-sm text-on-surface capitalize" x-text="selectedDomba?.kategori"></span>
                        </div>
                        <div class="flex flex-col gap-1 border-l border-outline-variant pl-4">
                            <span class="text-[10px] font-bold text-outline uppercase tracking-wider">STATUS SAAT INI</span>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                      :class="selectedDomba?.status === 'karantina'
                                        ? 'bg-error text-white'
                                        : 'bg-primary text-white'"
                                      x-text="selectedDomba?.status?.toUpperCase()"></span>
                                <button type="button" @click="selectedDomba = null; dombaSearch = ''"
                                        class="text-outline hover:text-error transition-colors">
                                    <span class="material-symbols-outlined text-base">close</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <p x-show="!selectedDomba" class="text-xs text-outline italic">
                        Review terisi otomatis setelah pilih domba
                    </p>
                    <input type="hidden" name="ear_tag_id" :value="selectedDomba?.ear_tag_id">
                </section>

                {{-- SECTION 2: Tanggal Sakit + Status --}}
                <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold text-on-surface uppercase tracking-wider">
                            Tanggal Sakit <span class="text-error">*</span>
                        </label>
                        <div class="relative">
                            <input type="date" name="tanggal_sakit" required
                                   class="w-full pl-3 pr-10 py-2.5 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-sm"/>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none">calendar_today</span>
                        </div>
                        <p class="text-[10px] text-outline italic">Kamus Data: kolom tgl_sakit tabel MEDICAL_RECORD</p>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[11px] font-bold text-on-surface uppercase tracking-wider">Status</label>
                        <select name="status"
                                class="w-full px-3 py-2.5 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-sm">
                            <option value="sakit">Sakit</option>
                            <option value="dalam_perawatan">Dalam Perawatan</option>
                            <option value="sembuh">Sembuh</option>
                        </select>
                        <p class="text-[10px] text-outline italic">Kamus Data: ENUM status tabel MEDICAL_RECORD</p>
                    </div>
                </section>

                {{-- SECTION 3: Gejala --}}
                <section class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-on-surface uppercase tracking-wider">
                        Gejala <span class="text-error">*</span>
                    </label>
                    <textarea name="gejala" rows="4" required
                              placeholder="Masukkan detail gejala fisik yang terlihat..."
                              class="w-full px-3 py-2.5 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-sm resize-none"></textarea>
                    <p class="text-[10px] text-outline italic">Kamus Data: kolom gejala TEXT NOT NULL</p>
                </section>

                {{-- SECTION 4: Diagnosa --}}
                <section class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-on-surface uppercase tracking-wider">
                        Diagnosa <span class="text-[11px] text-outline font-normal normal-case">(opsional)</span>
                    </label>
                    <textarea name="diagnosa" rows="4"
                              placeholder="Hasil pemeriksaan medis..."
                              class="w-full px-3 py-2.5 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-sm resize-none"></textarea>
                    <p class="text-[10px] text-outline italic">Kamus Data: kolom diagnosa TEXT NULL</p>
                </section>

                {{-- SECTION 5: Tanggal Sembuh --}}
                <section class="flex flex-col gap-1.5">
                    <div class="flex items-center gap-2">
                        <label class="text-[11px] font-bold text-on-surface uppercase tracking-wider">Tanggal Sembuh</label>
                        <span class="text-[11px] text-outline">(opsional — isi jika sudah sembuh)</span>
                    </div>
                    <div class="relative">
                        <input type="date" name="tanggal_sembuh"
                               class="w-full pl-3 pr-10 py-2.5 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-sm"/>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none">calendar_today</span>
                    </div>
                    <p class="text-[10px] text-outline italic">Kamus Data: kolom tanggal_sembuh NULL tabel MEDICAL_RECORD</p>
                </section>

                {{-- SECTION 6: Status Karantina --}}
                <section class="p-4 bg-[#FAFAF7] border border-outline-variant rounded-lg flex flex-col gap-5">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-bold text-on-surface uppercase tracking-wider">Status Karantina</span>
                            <span class="text-[10px] text-outline font-normal">(FR-5.3 — Kamus Data: status_karantina BOOLEAN)</span>
                        </div>
                        <div class="flex gap-6 mt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="tandai_karantina" value="ya"
                                       x-model="tandaiKarantina"
                                       class="w-4 h-4 text-primary border-outline-variant focus:ring-primary"/>
                                <span class="text-sm text-on-surface">Ya — pindahkan ke kandang isolasi</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="tandai_karantina" value="tidak"
                                       x-model="tandaiKarantina"
                                       class="w-4 h-4 text-primary border-outline-variant focus:ring-primary"/>
                                <span class="text-sm text-on-surface">Tidak</span>
                            </label>
                        </div>
                    </div>
                    <div x-show="tandaiKarantina === 'ya'" class="flex flex-col gap-1.5">
                        <div class="flex items-center gap-2">
                            <label class="text-[11px] font-bold text-on-surface uppercase tracking-wider">Kandang Isolasi</label>
                            <span class="text-[10px] text-outline italic">(FK → tabel KANDANG, tipe=isolasi)</span>
                        </div>
                        <select name="kandang_karantina_id"
                                class="w-full px-3 py-2.5 bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-sm">
                            <option value="">— Pilih kandang isolasi —</option>
                            @foreach($kandangList->where('tipe', 'isolasi') as $k)
                            <option value="{{ $k->kandang_id }}">{{ $k->nama_kandang }} (kap. {{ $k->kapasitas }})</option>
                            @endforeach
                        </select>
                    </div>
                </section>

                {{-- SECTION 7: Obat Diberikan --}}
                <section class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <label class="text-[11px] font-bold text-on-surface uppercase tracking-wider">Obat Diberikan</label>
                            <span class="text-[11px] text-outline">(opsional — Kamus Data: tabel PEMAKAIAN_OBAT)</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3">
                        <template x-for="(row, idx) in obatRows" :key="idx">
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <select :name="'obat_ids[' + idx + ']'"
                                            class="w-full px-3 py-2.5 bg-surface-container-low border border-outline-variant rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none appearance-none">
                                        <option value="">— Pilih obat —</option>
                                        @foreach($obatList as $o)
                                        <option value="{{ $o->obat_id }}">{{ $o->nama_obat }} (stok: {{ $o->stok }} {{ $o->satuan }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="number" :name="'obat_jumlah[' + idx + ']'" min="1"
                                       x-model="row.jumlah" placeholder="Jumlah"
                                       class="w-24 px-3 py-2.5 bg-surface-container-low border border-outline-variant rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none text-center"/>
                                <button type="button" @click="removeObatRow(idx)"
                                        class="p-2.5 text-outline hover:text-error transition-colors border border-outline-variant rounded-lg">
                                    <span class="material-symbols-outlined text-xl">delete</span>
                                </button>
                            </div>
                        </template>
                        <p x-show="obatRows.length === 0" class="text-xs text-outline italic">Belum ada obat ditambahkan.</p>
                    </div>
                    <button type="button" @click="addObatRow()"
                            class="w-fit px-4 py-2 text-xs font-bold border border-primary text-primary rounded-lg hover:bg-primary/5 transition-all flex items-center gap-2 uppercase tracking-wide">
                        <span class="material-symbols-outlined text-lg">add</span>
                        Tambah Baris Obat
                    </button>
                    <p class="text-[10px] text-outline italic">Setiap obat akan dicatat ke tabel PEMAKAIAN_OBAT</p>
                </section>

                {{-- SECTION 8: Dicatat Oleh --}}
                <section class="flex flex-col gap-1.5">
                    <div class="flex items-center gap-2">
                        <label class="text-[11px] font-bold text-on-surface uppercase tracking-wider">Dicatat Oleh</label>
                        <span class="text-[11px] text-outline">(auto dari session)</span>
                    </div>
                    <input type="text" readonly
                           value="{{ auth()->user()->nama ?? auth()->user()->name ?? 'User' }}"
                           class="w-full px-3 py-2.5 bg-surface-container border border-outline-variant rounded-lg text-sm text-outline cursor-not-allowed"/>
                    <p class="text-[10px] text-outline italic">Kamus Data: kolom user_id FK → tabel USER</p>
                </section>

            </main>

            <footer class="p-4 bg-white border-t border-surface-container flex justify-end gap-3 shrink-0 rounded-b-xl">
                <button type="button" @click="showCreateRekam = false"
                        class="px-6 py-2.5 text-sm font-bold text-outline border border-outline-variant rounded-lg hover:bg-surface-container transition-all active:scale-95 uppercase tracking-wide">
                    Batal
                </button>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-bold text-white bg-primary rounded-lg flex items-center gap-2 hover:bg-primary/90 transition-all shadow-md active:scale-95 uppercase tracking-wide">
                    <span class="material-symbols-outlined text-lg">save</span>
                    Simpan Rekam Medis
                </button>
            </footer>
        </form>
    </div>
</div>
