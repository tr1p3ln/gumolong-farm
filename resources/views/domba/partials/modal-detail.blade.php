{{-- ═══════════════════════════════════════════════════
    Modal Detail / Profil Domba — UC-MD.3 (Read Only)
    4 tab: Pertumbuhan | Kesehatan | Pakan | Silsilah
    Mounted via @include di domba/index.blade.php
    Memerlukan parent x-data: { modalDetail, modalEdit, selectedId, editDomba() }
═══════════════════════════════════════════════════ --}}
<div
    x-show="modalDetail"
    x-data="{
        activeTab: 'pertumbuhan',
        dombaData: null,
        loading: false,

        async loadDomba(earTagId) {
            if (!earTagId) return;
            this.loading = true;
            this.dombaData = null;
            try {
                const res = await fetch('/domba/' + earTagId + '?detail=1', {
                    headers: { 'Accept': 'application/json' }
                });
                const json = await res.json();
                if (json.success) this.dombaData = json.data;
            } catch (err) {
                console.error('Error loading domba:', err);
            } finally {
                this.loading = false;
            }
        },

        hitungUmur(tanggalLahir) {
            if (!tanggalLahir) return '-';
            const lahir    = new Date(tanggalLahir);
            const sekarang = new Date();
            const diffMs   = sekarang - lahir;
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
            const years    = Math.floor(diffDays / 365);
            const months   = Math.floor((diffDays % 365) / 30);
            const days     = diffDays % 30;
            return years + 'y ' + months + 'm ' + days + 'd';
        },

        formatTgl(tgl) {
            if (!tgl) return '-';
            return new Date(tgl).toLocaleDateString('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric'
            });
        }
    }"
    x-init="$watch('modalDetail', val => { if (val) { activeTab = 'pertumbuhan'; loadDomba(selectedId); } })"
    @keydown.escape.window="if (modalDetail) { modalDetail = false; }"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-start justify-center p-4"
    style="display: none;">

    {{-- Backdrop --}}
    <div class="absolute inset-0" @click="modalDetail = false"></div>

    {{-- ═══ MODAL CARD ═══ --}}
    <div class="relative w-full max-w-6xl mx-auto bg-white rounded-xl shadow-2xl my-4" @click.stop>

        {{-- ── HEADER ── --}}
        <header class="p-6 flex justify-between items-start border-b border-gray-200 bg-white rounded-t-xl">
            <div>
                <h2 class="text-2xl font-bold text-primary tracking-tight"
                    x-text="dombaData
                        ? 'Detail Domba — ' + dombaData.ear_tag_id + ' (' + (dombaData.nama || 'Tanpa Nama') + ')'
                        : 'Detail Domba'">
                </h2>
                <p class="text-xs font-medium text-gray-500 mt-1 uppercase tracking-wider"
                   x-text="dombaData
                       ? 'UC-MD.3 | Kamus Data: tabel DOMBA + PENIMBANGAN + MEDICAL_RECORD | ' + (dombaData.e_ear_tag_id || '-')
                       : 'Memuat...'">
                </p>
            </div>
            <button type="button" @click="modalDetail = false"
                    class="p-2 hover:bg-gray-100 transition-colors rounded-full flex-shrink-0 ml-4">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </header>

        {{-- ── READ ONLY BANNER ── --}}
        <div class="bg-gray-100 border-l-4 border-primary px-6 py-3 flex items-center gap-3">
            <svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span class="text-xs font-black uppercase tracking-widest text-gray-600">
                READ ONLY — Profil lengkap domba
                <span class="text-primary font-mono" x-text="dombaData?.ear_tag_id"></span>
            </span>
        </div>

        {{-- ── LOADING STATE ── --}}
        <div x-show="loading && !dombaData" class="p-8 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-300 border-t-primary"></div>
            <p class="mt-4 text-gray-500 text-sm">Memuat profil domba...</p>
        </div>

        {{-- ════════════════════════════════════════
             MAIN CONTENT
        ════════════════════════════════════════ --}}
        <div x-show="dombaData" class="p-8 space-y-8">

            {{-- ══ SECTION 1: HERO IDENTITY CARD ══ --}}
            <section class="border border-gray-200 rounded-xl overflow-hidden bg-white">
                <div class="p-6">

                    {{-- Header row --}}
                    <div class="col-span-full border-b border-gray-200 pb-6 mb-6 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-baseline gap-3">
                            <h3 class="text-3xl font-extrabold text-gray-900"
                                x-text="dombaData?.nama || 'Tanpa Nama'"></h3>
                            <span class="font-mono text-sm font-bold text-gray-500 uppercase tracking-tighter"
                                  x-text="dombaData?.ear_tag_id"></span>
                        </div>
                        <div class="flex gap-2">
                            <span class="px-3 py-1 text-xs font-bold border border-primary text-primary rounded-full capitalize"
                                  x-text="dombaData?.kategori ?? '—'"></span>
                            <span class="px-3 py-1 text-xs font-bold border border-gray-400 text-gray-600 rounded-full capitalize"
                                  x-text="dombaData?.status ?? '—'"></span>
                        </div>
                    </div>

                    {{-- Info grid 4 col --}}
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-y-6 gap-x-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Electronic ID</label>
                            <p class="text-sm font-semibold text-gray-900"
                               x-text="dombaData?.e_ear_tag_id || '-'"></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Breed</label>
                            <p class="text-sm font-semibold text-gray-900" x-text="dombaData?.ras ?? '-'"></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Category</label>
                            <p class="text-sm font-semibold text-gray-900 capitalize"
                               x-text="dombaData?.kategori ?? '-'"></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Origin</label>
                            <p class="text-sm font-semibold text-gray-900"
                               x-text="dombaData?.asal === 'lahir_di_kandang' ? 'Internal Farm' : 'External'"></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pen Location</label>
                            <p class="text-sm font-semibold text-gray-900"
                               x-text="dombaData?.kandang?.nama_kandang || '-'"></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Date of Birth</label>
                            <p class="text-sm font-semibold text-gray-900"
                               x-text="formatTgl(dombaData?.tanggal_lahir)"></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Current Age</label>
                            <p class="text-sm font-semibold text-gray-900"
                               x-text="hitungUmur(dombaData?.tanggal_lahir)"></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Repro Status</label>
                            <p class="text-sm font-semibold text-gray-900"
                               x-text="dombaData?.jenis_kelamin === 'betina' ? 'Betina / Dam' : 'Jantan / Sire'"></p>
                        </div>

                        {{-- Medical Notes --}}
                        <div class="col-span-2 lg:col-span-4 space-y-1 pt-2 border-t border-gray-100">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Medical Notes</label>
                            <p class="text-sm text-gray-600 leading-relaxed italic"
                               x-text="dombaData?.catatan || 'Tidak ada catatan medis'"></p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ══ SECTION 2: STATS SUMMARY ROW ══ --}}
            <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">

                <div class="p-4 border border-gray-200 rounded-xl bg-white flex flex-col justify-between">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Berat Terkini</label>
                    <div class="flex items-end justify-between mt-2">
                        <span class="text-2xl font-black text-gray-900"
                              x-text="dombaData?.bobot_terakhir ? parseFloat(dombaData.bobot_terakhir).toFixed(1) + ' kg' : '-'">
                        </span>
                        <span class="text-xs font-bold text-primary flex items-center gap-1 pb-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            +0.4 kg
                        </span>
                    </div>
                </div>

                <div class="p-4 border border-gray-200 rounded-xl bg-white flex flex-col justify-between">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">ADG (30 Hari)</label>
                    <div class="flex items-end justify-between mt-2">
                        <span class="text-2xl font-black text-gray-900">-</span>
                        <span class="px-2 py-0.5 text-xs font-bold bg-green-100 text-green-800 rounded-full mb-1">
                            Normal
                        </span>
                    </div>
                </div>

                <div class="p-4 border border-gray-200 rounded-xl bg-white flex flex-col justify-between">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Health State</label>
                    <div class="flex items-end justify-between mt-2">
                        <span class="text-2xl font-black text-gray-900">Sehat</span>
                        <span class="px-2 py-0.5 text-xs font-bold border border-primary text-primary rounded-full mb-1">
                            Tidak Karantina
                        </span>
                    </div>
                </div>

                <div class="p-4 border border-gray-200 rounded-xl bg-white flex flex-col justify-between">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Repro History</label>
                    <div class="flex items-end justify-between mt-2">
                        <span class="text-2xl font-black text-gray-900">-</span>
                        <span class="text-xs font-bold text-gray-400 pb-1">-</span>
                    </div>
                </div>
            </section>

            {{-- ══ SECTION 3: TABBED CONTENT ══ --}}
            <section class="border border-gray-200 rounded-xl overflow-hidden bg-white">

                {{-- Tab Navigation --}}
                <nav class="flex border-b border-gray-200 bg-gray-50 px-6">
                    @foreach([
                        ['key' => 'pertumbuhan', 'label' => 'Pertumbuhan'],
                        ['key' => 'kesehatan',   'label' => 'Kesehatan'],
                        ['key' => 'pakan',       'label' => 'Pakan'],
                        ['key' => 'silsilah',    'label' => 'Silsilah'],
                    ] as $tab)
                        <button type="button"
                                @click="activeTab = '{{ $tab['key'] }}'"
                                :class="activeTab === '{{ $tab['key'] }}'
                                    ? 'text-primary border-primary'
                                    : 'text-gray-500 border-transparent hover:text-primary'"
                                class="px-6 py-4 text-sm font-bold border-b-2 transition-colors -mb-px">
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </nav>

                <div class="p-6 space-y-6">

                    {{-- ════ TAB: PERTUMBUHAN ════ --}}
                    <div x-show="activeTab === 'pertumbuhan'">

                        {{-- Chart placeholder --}}
                        <div class="h-64 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 flex flex-col items-center justify-center relative overflow-hidden mb-6">
                            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#2E7D32_1px,transparent_1px)] [background-size:20px_20px]"></div>
                            <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                            </svg>
                            <span class="text-xs font-black uppercase tracking-widest text-gray-400">
                                [ LINE CHART — BERAT VS WAKTU ]
                            </span>
                        </div>

                        {{-- Penimbangan table --}}
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        @foreach(['Tanggal', 'Berat', 'ADG', 'Petugas', 'Catatan'] as $col)
                                            <th class="py-3 pr-4 font-bold text-gray-500 uppercase tracking-wider text-xs">
                                                {{ $col }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-if="dombaData?.penimbangan?.length > 0">
                                        <template x-for="(timbang, i) in dombaData.penimbangan.slice(0, 5)" :key="i">
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="py-3.5 pr-4 font-semibold text-gray-700"
                                                    x-text="formatTgl(timbang.tanggal_timbang)"></td>
                                                <td class="py-3.5 pr-4 font-bold text-gray-900"
                                                    x-text="parseFloat(timbang.berat_kg).toFixed(1) + ' kg'"></td>
                                                <td class="py-3.5 pr-4 font-medium text-primary"
                                                    x-text="timbang.adg ? '+' + parseFloat(timbang.adg).toFixed(3) + ' kg/d' : '-'"></td>
                                                <td class="py-3.5 pr-4 text-gray-500">-</td>
                                                <td class="py-3.5 text-gray-500 italic"
                                                    x-text="timbang.catatan || '-'"></td>
                                            </tr>
                                        </template>
                                    </template>
                                    <template x-if="!dombaData?.penimbangan?.length">
                                        <tr>
                                            <td colspan="5" class="py-8 text-center text-gray-400 italic text-sm">
                                                Belum ada data penimbangan
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-start pt-2">
                            <a :href="'/pertumbuhan?domba=' + dombaData?.ear_tag_id"
                               class="text-sm font-bold text-secondary flex items-center gap-1 hover:gap-2 transition-all">
                                Lihat Semua Riwayat Penimbangan
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    {{-- ════ TAB: KESEHATAN ════ --}}
                    <div x-show="activeTab === 'kesehatan'">
                        <div class="text-center py-12 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm italic">Riwayat kesehatan akan ditampilkan di sini</p>
                            <p class="text-xs mt-1 text-gray-400">(Module Kesehatan Ternak — dikembangkan oleh Abil)</p>
                        </div>
                    </div>

                    {{-- ════ TAB: PAKAN ════ --}}
                    <div x-show="activeTab === 'pakan'">
                        <div class="text-center py-12 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <p class="text-sm italic">Riwayat pemberian pakan akan ditampilkan di sini</p>
                            <p class="text-xs mt-1 text-gray-400">(Module Pakan Individual — dikembangkan oleh Andre)</p>
                        </div>
                    </div>

                    {{-- ════ TAB: SILSILAH ════ --}}
                    <div x-show="activeTab === 'silsilah'" class="space-y-6">

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                            {{-- Silsilah cards --}}
                            <div class="lg:col-span-2 border border-gray-200 rounded-xl p-6 bg-white">
                                <h4 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-widest flex items-center gap-2">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                    </svg>
                                    Silsilah Domba
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {{-- Induk / Dam --}}
                                    <div class="p-4 border border-gray-200 rounded-lg flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                                <span class="text-green-700 font-bold">♀</span>
                                            </div>
                                            <div class="min-w-0">
                                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block">
                                                    Induk (Dam)
                                                </label>
                                                <span class="font-bold text-sm truncate block"
                                                      x-text="dombaData?.induk?.ear_tag_id
                                                          ? dombaData.induk.ear_tag_id + ' (' + (dombaData.induk.nama || '-') + ')'
                                                          : '-'">
                                                </span>
                                            </div>
                                        </div>
                                        <button type="button"
                                                class="text-xs font-black border border-secondary text-secondary px-3 py-1 rounded hover:bg-green-50 transition-colors uppercase flex-shrink-0"
                                                x-show="dombaData?.induk">
                                            Lihat →
                                        </button>
                                    </div>

                                    {{-- Ayah / Sire --}}
                                    <div class="p-4 border border-gray-200 rounded-lg flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                <span class="text-blue-700 font-bold">♂</span>
                                            </div>
                                            <div class="min-w-0">
                                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block">
                                                    Pejantan (Sire)
                                                </label>
                                                <span class="font-bold text-sm truncate block"
                                                      x-text="dombaData?.ayah?.ear_tag_id
                                                          ? dombaData.ayah.ear_tag_id + ' (' + (dombaData.ayah.nama || '-') + ')'
                                                          : '-'">
                                                </span>
                                            </div>
                                        </div>
                                        <button type="button"
                                                class="text-xs font-black border border-secondary text-secondary px-3 py-1 rounded hover:bg-blue-50 transition-colors uppercase flex-shrink-0"
                                                x-show="dombaData?.ayah">
                                            Lihat →
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Inbreeding Coefficient --}}
                            <div class="border border-gray-200 rounded-xl p-6 bg-gray-50 flex flex-col justify-center items-center text-center">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                                    Inbreeding Coefficient
                                </label>
                                <div class="text-4xl font-black text-primary mb-2">-</div>
                                <span class="px-3 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">
                                    Aman
                                </span>
                                <p class="text-xs text-gray-500 mt-4 leading-relaxed">
                                    Akan dihitung di modul Silsilah
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            {{-- ══ SECTION 4: AKSI CEPAT ══ --}}
            <section class="space-y-4">
                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-widest">Aksi Cepat &amp; Log</h4>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">

                    @foreach([
                        ['label' => 'Timbang Berat', 'path' => '/pertumbuhan', 'color' => 'text-primary',
                         'icon'  => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3'],
                        ['label' => 'Rekam Medis', 'path' => '/kesehatan', 'color' => 'text-accent',
                         'icon'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['label' => 'Jadwal Pakan', 'path' => '/pakan-individual', 'color' => 'text-secondary',
                         'icon'  => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                        ['label' => 'Data Silsilah', 'path' => '/silsilah', 'color' => 'text-primary',
                         'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['label' => 'Vaksinasi', 'path' => '/kesehatan', 'color' => 'text-accent',
                         'icon'  => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
                    ] as $aksi)
                        <a :href="'{{ $aksi['path'] }}?domba=' + dombaData?.ear_tag_id"
                           class="p-4 border border-gray-200 rounded-xl bg-white flex flex-col items-center text-center hover:shadow-md transition-shadow">
                            <svg class="w-8 h-8 {{ $aksi['color'] }} mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="{{ $aksi['icon'] }}"/>
                            </svg>
                            <span class="text-xs font-bold text-gray-700 mb-3 leading-tight">{{ $aksi['label'] }}</span>
                            <button type="button"
                                    class="w-full py-1.5 border border-gray-300 text-xs font-black uppercase rounded hover:bg-gray-50 transition-colors">
                                Buka →
                            </button>
                        </a>
                    @endforeach

                </div>
            </section>

        </div>{{-- /dombaData --}}

        {{-- ── FOOTER ── --}}
        <footer class="p-6 border-t border-gray-200 bg-white rounded-b-xl flex flex-wrap gap-4 items-center justify-between">

            <button type="button"
                    class="px-6 py-2.5 border-2 border-gray-300 text-gray-600 text-sm font-bold rounded-lg flex items-center gap-2 hover:bg-gray-50 transition-colors opacity-50 cursor-not-allowed"
                    disabled>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Export PDF Profil
            </button>

            <div class="flex gap-3 ml-auto">
                <button type="button"
                        @click="modalDetail = false"
                        class="px-8 py-2.5 border-2 border-gray-300 text-gray-600 text-sm font-bold rounded-lg hover:bg-gray-50 transition-colors">
                    Tutup
                </button>
                <button type="button"
                        @click="modalDetail = false; editDomba(dombaData?.ear_tag_id)"
                        class="px-8 py-2.5 bg-primary text-white text-sm font-bold rounded-lg hover:opacity-90 transition-all flex items-center gap-2 shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Data Domba
                </button>
            </div>
        </footer>

    </div>
</div>
