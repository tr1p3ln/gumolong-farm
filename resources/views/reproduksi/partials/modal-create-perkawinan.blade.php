{{-- Modal: Catat Perkawinan --}}
<div x-show="showCreatePerkawinan" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     @keydown.escape.window="showCreatePerkawinan = false">

    <div class="bg-surface w-full max-w-4xl max-h-[92vh] shadow-2xl rounded-xl overflow-hidden flex flex-col border border-surface-container"
         @click.outside="showCreatePerkawinan = false">

        {{-- Header --}}
        <header class="px-8 py-6 bg-white border-b border-surface-container flex justify-between items-start flex-shrink-0">
            <div class="space-y-1">
                <h2 class="text-xl font-bold text-on-surface">Catat Perkawinan</h2>
            </div>
            <button type="button" @click="showCreatePerkawinan = false"
                    class="text-outline hover:text-on-surface transition-colors p-1 rounded-lg hover:bg-surface-container-low">
                <span class="material-symbols-outlined text-2xl">close</span>
            </button>
        </header>

        {{-- Scrollable body --}}
        <form method="POST" action="{{ route('reproduksi.store') }}" class="overflow-y-auto flex-1">
            @csrf
            <main class="p-8 space-y-8">

                {{-- ── Section 1: Pejantan ────────────────────────────────────── --}}
                <section class="space-y-3">
                    <label class="block text-xs font-semibold text-on-surface">
                        Pejantan
                        <span class="text-error ml-1">*</span>
                    </label>

                    {{-- Search --}}
                    <div class="relative" x-show="!selectedPejantan">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-xl pointer-events-none">search</span>
                        <input type="text" x-model="pejantanSearch"
                               placeholder="Cari Ear Tag pejantan..."
                               class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all text-sm"/>
                    </div>

                    {{-- Dropdown hasil pencarian --}}
                    <div x-show="pejantanSearch.length > 0 && !selectedPejantan"
                         class="border border-outline-variant rounded-lg divide-y divide-surface-container max-h-44 overflow-y-auto bg-white shadow-lg">
                        <template x-for="p in filteredPejantan.slice(0, 8)" :key="p.ear_tag_id">
                            <div @click="selectedPejantan = p; pejantanSearch = p.ear_tag_id"
                                 class="px-4 py-2.5 flex items-center gap-3 hover:bg-surface-container-low cursor-pointer transition-colors">
                                <span class="font-bold text-primary text-sm" x-text="p.ear_tag_id"></span>
                                <span class="text-xs text-outline" x-text="p.nama ? '· ' + p.nama : ''"></span>
                                <span class="ml-auto text-xs text-on-surface-variant bg-surface-container px-2 py-0.5 rounded" x-text="p.ras"></span>
                            </div>
                        </template>
                        <div x-show="filteredPejantan.length === 0"
                             class="px-4 py-3 text-sm text-outline text-center italic">
                            Tidak ada pejantan aktif ditemukan
                        </div>
                    </div>

                    {{-- Selected Card --}}
                    <div x-show="selectedPejantan"
                         class="grid grid-cols-3 gap-4 p-4 bg-white border border-primary/20 rounded-xl shadow-sm">
                        <div>
                            <p class="text-[10px] font-bold text-outline uppercase tracking-wider mb-1">Ear Tag</p>
                            <p class="font-bold text-primary text-lg" x-text="selectedPejantan?.ear_tag_id"></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-outline uppercase tracking-wider mb-1">Ras</p>
                            <p class="text-on-surface font-medium" x-text="selectedPejantan?.ras ?? '—'"></p>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-[10px] font-bold text-outline uppercase tracking-wider mb-1">Kategori</p>
                            <div class="flex items-center justify-between">
                                <span class="px-3 py-1 bg-secondary-container text-on-secondary-container text-[11px] font-bold rounded-full">Pejantan</span>
                                <button type="button" @click="selectedPejantan = null; pejantanSearch = ''"
                                        class="text-outline hover:text-error transition-colors ml-2">
                                    <span class="material-symbols-outlined text-lg">close</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs italic text-outline">Hanya domba berkategori Pejantan yang dapat dipilih</p>
                    <input type="hidden" name="pejantan_id" :value="selectedPejantan?.ear_tag_id">
                </section>

                {{-- ── Section 2: Indukan ──────────────────────────────────────── --}}
                <section class="space-y-3">
                    <label class="block text-xs font-semibold text-on-surface">
                        Indukan
                        <span class="text-error ml-1">*</span>
                    </label>

                    {{-- Search --}}
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-xl pointer-events-none">search</span>
                        <input type="text" x-model="indukanSearch"
                               placeholder="Cari Ear Tag indukan..."
                               class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all text-sm"/>
                    </div>

                    {{-- Dropdown --}}
                    <div x-show="indukanSearch.length > 0"
                         class="border border-outline-variant rounded-lg divide-y divide-surface-container max-h-44 overflow-y-auto bg-white shadow-lg">
                        <template x-for="i in filteredIndukan.slice(0, 8)" :key="i.ear_tag_id">
                            <div @click="addIndukan(i)"
                                 class="px-4 py-2.5 flex items-center gap-3 hover:bg-surface-container-low cursor-pointer transition-colors">
                                <span class="font-bold text-secondary text-sm" x-text="i.ear_tag_id"></span>
                                <span class="text-xs text-outline" x-text="i.nama ? '· ' + i.nama : ''"></span>
                                <span class="ml-auto text-xs text-on-surface-variant bg-surface-container px-2 py-0.5 rounded" x-text="i.ras"></span>
                                <span x-show="selectedIndukanList.find(s => s.ear_tag_id === i.ear_tag_id)"
                                      class="material-symbols-outlined text-primary text-base" style="font-variation-settings:'FILL' 1">check_circle</span>
                            </div>
                        </template>
                        <div x-show="filteredIndukan.length === 0"
                             class="px-4 py-3 text-sm text-outline text-center italic">
                            Tidak ada indukan aktif ditemukan
                        </div>
                    </div>

                    {{-- Selected List --}}
                    <div class="space-y-2">
                        <template x-for="(ind, idx) in selectedIndukanList" :key="ind.ear_tag_id">
                            <div class="flex items-center justify-between p-3 bg-white border border-surface-variant rounded-lg">
                                <div class="flex gap-4 items-center">
                                    <span class="font-bold text-secondary" x-text="ind.ear_tag_id"></span>
                                    <span class="text-xs py-0.5 px-2 bg-surface-container-high rounded text-on-surface-variant" x-text="ind.ras ?? '—'"></span>
                                    <span class="text-xs py-0.5 px-2 bg-secondary-container text-on-secondary-container rounded font-medium">Indukan</span>
                                </div>
                                <button type="button" @click="removeIndukan(ind.ear_tag_id)"
                                        class="text-error text-xs font-semibold flex items-center gap-1 hover:bg-error-container/30 px-2 py-1 rounded transition-colors">
                                    <span class="material-symbols-outlined text-base">close</span> Hapus
                                </button>
                                <input type="hidden" :name="'indukan_ids[' + idx + ']'" :value="ind.ear_tag_id">
                            </div>
                        </template>

                        <p x-show="selectedIndukanList.length === 0"
                           class="text-xs text-outline italic py-1">Belum ada indukan dipilih.</p>
                    </div>

                    <button type="button"
                            @click="indukanSearch = ''"
                            x-show="selectedIndukanList.length > 0"
                            class="w-full flex items-center justify-center gap-2 py-2.5 border-2 border-dashed border-outline-variant text-secondary font-bold hover:bg-secondary-container/20 rounded-xl transition-all text-sm">
                        <span class="material-symbols-outlined text-lg">add_circle</span> Tambah Indukan Lain
                    </button>

                    <p class="text-xs text-on-surface-variant bg-surface-container-lowest p-2.5 rounded-lg border border-surface-container">
                        Sistem akan membuat 1 record PERKAWINAN per indukan yang dipilih.
                    </p>
                </section>

                {{-- ── Section 3: Tanggal & Metode ────────────────────────────── --}}
                <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-on-surface">
                            Tanggal Kawin <span class="text-error">*</span>
                        </label>
                        <input type="date" name="tanggal_perkawinan"
                               x-model="tanggalKawin" @change="calcHPL()" required
                               class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none text-sm bg-white"/>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-on-surface">
                            Metode <span class="text-error">*</span>
                        </label>
                        <select name="metode" required
                                class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none bg-white text-sm">
                            <option value="alami">Alami</option>
                            <option value="inseminasi_buatan">Inseminasi Buatan (IB)</option>
                        </select>
                    </div>
                </section>

                {{-- ── Section 4: Estimasi HPL ─────────────────────────────────── --}}
                <section class="p-6 bg-surface-container-lowest border-2 border-primary/10 rounded-2xl relative overflow-hidden">
                    <p class="text-[10px] font-bold text-primary uppercase tracking-widest mb-4">Estimasi HPL</p>
                    <div class="grid grid-cols-3 gap-6 items-center">
                        <div>
                            <p class="text-xs text-outline mb-1">Tgl Kawin</p>
                            <p class="text-base font-semibold text-on-surface"
                               x-text="tanggalKawin ? new Date(tanggalKawin + 'T00:00:00').toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'}) : '—'"></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="h-px flex-1 bg-outline-variant"></div>
                            <span class="text-[10px] font-bold px-2 py-1 bg-secondary-container text-on-secondary-container rounded-full whitespace-nowrap">+ 150 Hari</span>
                            <div class="h-px flex-1 bg-outline-variant"></div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-outline mb-1">Estimasi HPL</p>
                            <p class="text-2xl font-black text-primary" x-text="hplText || '—'"></p>
                        </div>
                    </div>
                </section>

                {{-- ── Section 5: Preview Records ───────────────────────────────── --}}
                <section x-show="selectedIndukanList.length > 0 && selectedPejantan && tanggalKawin"
                         class="p-5 bg-blue-50 rounded-xl border border-blue-200 space-y-3">
                    <p class="flex items-center gap-2 text-sm font-bold text-blue-900">
                        <span class="material-symbols-outlined text-blue-700 text-base">inventory_2</span>
                        Preview: Record yang Akan Dibuat
                        (<span x-text="selectedIndukanList.length"></span> record)
                    </p>
                    <ul class="space-y-1.5">
                        <template x-for="ind in selectedIndukanList" :key="ind.ear_tag_id">
                            <li class="font-mono text-xs text-blue-800 bg-blue-100/50 px-3 py-1.5 rounded"
                                x-text="'KWN-xxx | ' + (selectedPejantan?.ear_tag_id ?? '?') + ' × ' + ind.ear_tag_id + ' | ' + (tanggalKawin ? new Date(tanggalKawin + 'T00:00:00').toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'}) : '?') + ' | HPL: ' + (hplText || '?')">
                            </li>
                        </template>
                    </ul>
                </section>

                {{-- ── Section 6: Status & Metadata ──────────────────────────────── --}}
                <section class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-surface-container pt-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-on-surface">Status Awal</label>
                        <select class="w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-low text-outline cursor-not-allowed text-sm" disabled>
                            <option selected>Menunggu Konfirmasi</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-on-surface">Dicatat Oleh</label>
                        <input type="text" readonly
                               value="{{ auth()->user()->nama ?? auth()->user()->name ?? 'User' }}"
                               class="w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-low text-outline cursor-not-allowed text-sm font-medium"/>
                    </div>
                </section>

            </main>

            {{-- Footer --}}
            <footer class="px-8 py-5 bg-white border-t border-surface-container flex justify-end items-center gap-4 flex-shrink-0">
                <button type="button" @click="showCreatePerkawinan = false"
                        class="px-6 py-2.5 border border-outline text-on-surface-variant font-bold text-sm rounded-lg hover:bg-surface-container transition-all active:scale-95">
                    Batal
                </button>
                <button type="submit"
                        class="px-8 py-2.5 bg-primary text-white font-bold text-sm rounded-lg shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all active:scale-95 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">save</span>
                    Simpan Perkawinan
                </button>
            </footer>
        </form>
    </div>
</div>
