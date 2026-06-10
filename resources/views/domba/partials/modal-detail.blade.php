{{-- ═══════════════════════════════════════════════════
    Modal Detail / Profil Domba (Read Only)
    4 tab: Pertumbuhan | Kesehatan | Pakan | Silsilah
    Mounted via @include di domba/index.blade.php
═══════════════════════════════════════════════════ --}}
<div x-show="modalDetail" x-data="{
    activeTab: 'pertumbuhan',
    dombaData: null,
    loading: false,
    chartInstance: null,

    async loadDomba(earTagId) {
        if (!earTagId) return;
        this.loading = true;
        this.dombaData = null;
        try {
            const res = await fetch('/domba/' + earTagId + '?detail=1', {
                headers: { 'Accept': 'application/json' }
            });
            const json = await res.json();
            if (json.success) {
                this.dombaData = json.data;

                {{-- Tunggu hingga DOM selesai dirender sebelum menggambar chart --}}
                this.$nextTick(() => {
                    this.initChart();
                });
            }
        } catch (err) {
            console.error('Error loading domba:', err);
        } finally {
            this.loading = false;
        }
    },

    initChart() {
        // Menggunakan $refs agar ID tidak bentrok meskipun ada multiple modal
        const ctx = this.$refs.chartCanvas;
        if (!ctx || !this.dombaData || !this.dombaData.penimbangan || this.dombaData.penimbangan.length === 0) return;

        if (this.chartInstance) {
            this.chartInstance.destroy();
        }

        const riwayatSesuaiUrutan = [...this.dombaData.penimbangan].reverse();
        const labels = riwayatSesuaiUrutan.map(t => this.formatTgl(t.tanggal_timbang));
        const weights = riwayatSesuaiUrutan.map(t => parseFloat(t.berat_kg));

        this.chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Berat Domba',
                    data: weights,
                    borderColor: '#2E7D32',
                    backgroundColor: 'rgba(46, 125, 50, 0.12)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: '#2E7D32',
                    fill: true,
                    tension: 0.4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1F2937',
                        padding: 10,
                        cornerRadius: 8,
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter, sans-serif', size: 11 }, color: '#9CA3AF' },
                    },
                    y: {
                        beginAtZero: false,
                        grid: { color: '#F3F4F6' },
                        ticks: { font: { family: 'Inter, sans-serif', size: 11 }, color: '#9CA3AF' },
                        title: { display: true, text: 'kg', font: { family: 'Inter, sans-serif', size: 11 }, color: '#9CA3AF' }
                    },
                },
            }
        });
    },

    hitungUmur(tanggalLahir) {
        if (!tanggalLahir) return '-';
        const lahir = new Date(tanggalLahir);
        const sekarang = new Date();
        const diffMs = sekarang - lahir;
        const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
        const years = Math.floor(diffDays / 365);
        const months = Math.floor((diffDays % 365) / 30);
        const days = diffDays % 30;
        return years + 'y ' + months + 'm ' + days + 'd';
    },

    formatTgl(tgl) {
        if (!tgl) return '-';
        return new Date(tgl).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }
}" x-init="$watch('modalDetail', val => {
    if (val) {
        activeTab = 'pertumbuhan';
        loadDomba(selectedId);
    } else {
        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }
    }
})"
    @keydown.escape.window="if (modalDetail) { modalDetail = false; }" x-cloak
    class="fixed inset-0 z-50 flex items-start justify-center p-4 overflow-y-auto bg-black/50" style="display: none;">

    {{-- Backdrop --}}
    <div class="absolute inset-0" @click="modalDetail = false"></div>

    {{-- ═══ MODAL CARD ═══ --}}
    <div class="relative w-full max-w-6xl mx-auto my-4 bg-white shadow-2xl rounded-xl" @click.stop>

        {{-- ── HEADER ── --}}
        <header class="flex items-start justify-between p-6 bg-white border-b border-gray-200 rounded-t-xl">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-primary"
                    x-text="dombaData
                        ? 'Detail Domba — ' + dombaData.ear_tag_id + ' (' + (dombaData.nama || 'Tanpa Nama') + ')'
                        : 'Detail Domba'">
                </h2>
                <p class="mt-1 text-xs font-medium tracking-wider text-gray-500 uppercase"
                    x-text="dombaData
                       ? 'ID: ' + (dombaData.e_ear_tag_id || '-')
                       : 'Memuat...'">
                </p>
            </div>
            <button type="button" @click="modalDetail = false"
                class="flex-shrink-0 p-2 ml-4 transition-colors rounded-full hover:bg-gray-100">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </header>

        {{-- ── READ ONLY BANNER ── --}}
        <div class="flex items-center gap-3 px-6 py-3 bg-gray-100 border-l-4 border-primary">
            <svg class="flex-shrink-0 w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <span class="text-xs font-black tracking-widest text-gray-600 uppercase">
                READ ONLY — Profil lengkap domba
                <span class="font-mono text-primary" x-text="dombaData?.ear_tag_id"></span>
            </span>
        </div>

        {{-- ── LOADING STATE ── --}}
        <div x-show="loading && !dombaData" class="p-8 text-center">
            <div class="inline-block w-8 h-8 border-4 border-gray-300 rounded-full animate-spin border-t-primary"></div>
            <p class="mt-4 text-sm text-gray-500">Memuat profil domba...</p>
        </div>

        {{-- ════════════════════════════════════════
             MAIN CONTENT
        ════════════════════════════════════════ --}}
        <div x-show="dombaData" class="p-8 space-y-8">

            {{-- ══ SECTION 1: HERO IDENTITY CARD ══ --}}
            <section class="overflow-hidden bg-white border border-gray-200 rounded-xl">
                <div class="p-6">
                    <div
                        class="flex flex-wrap items-center justify-between gap-4 pb-6 mb-6 border-b border-gray-200 col-span-full">
                        <div class="flex items-baseline gap-3">
                            <h3 class="text-3xl font-extrabold text-gray-900" x-text="dombaData?.nama || 'Tanpa Nama'">
                            </h3>
                            <span class="font-mono text-sm font-bold tracking-tighter text-gray-500 uppercase"
                                x-text="dombaData?.ear_tag_id"></span>
                        </div>
                        <div class="flex gap-2">
                            <span
                                class="px-3 py-1 text-xs font-bold capitalize border rounded-full border-primary text-primary"
                                x-text="dombaData?.kategori ?? '—'"></span>
                            <span
                                class="px-3 py-1 text-xs font-bold text-gray-600 capitalize border border-gray-400 rounded-full"
                                x-text="dombaData?.status ?? '—'"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-y-6 gap-x-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold tracking-wider text-gray-500 uppercase">Electronic
                                ID</label>
                            <p class="text-sm font-semibold text-gray-900" x-text="dombaData?.e_ear_tag_id || '-'"></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold tracking-wider text-gray-500 uppercase">Breed</label>
                            <p class="text-sm font-semibold text-gray-900" x-text="dombaData?.ras ?? '-'"></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold tracking-wider text-gray-500 uppercase">Category</label>
                            <p class="text-sm font-semibold text-gray-900 capitalize"
                                x-text="dombaData?.kategori ?? '-'"></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold tracking-wider text-gray-500 uppercase">Origin</label>
                            <p class="text-sm font-semibold text-gray-900"
                                x-text="dombaData?.asal === 'lahir_di_kandang' ? 'Internal Farm' : 'External'"></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold tracking-wider text-gray-500 uppercase">Pen Location</label>
                            <p class="text-sm font-semibold text-gray-900"
                                x-text="dombaData?.kandang?.nama_kandang || '-'"></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold tracking-wider text-gray-500 uppercase">Date of
                                Birth</label>
                            <p class="text-sm font-semibold text-gray-900" x-text="formatTgl(dombaData?.tanggal_lahir)">
                            </p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold tracking-wider text-gray-500 uppercase">Current Age</label>
                            <p class="text-sm font-semibold text-gray-900"
                                x-text="hitungUmur(dombaData?.tanggal_lahir)"></p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold tracking-wider text-gray-500 uppercase">Repro Status</label>
                            <p class="text-sm font-semibold text-gray-900"
                                x-text="dombaData?.jenis_kelamin === 'betina' ? 'Betina / Dam' : 'Jantan / Sire'"></p>
                        </div>
                        <div class="col-span-2 pt-2 space-y-1 border-t border-gray-100 lg:col-span-4">
                            <label class="text-xs font-bold tracking-wider text-gray-500 uppercase">Medical
                                Notes</label>
                            <p class="text-sm italic leading-relaxed text-gray-600"
                                x-text="dombaData?.catatan || 'Tidak ada catatan medis'"></p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ══ SECTION 2: STATS SUMMARY ROW ══ --}}
            <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="flex flex-col justify-between p-4 bg-white border border-gray-200 rounded-xl">
                    <label class="text-xs font-bold tracking-wider text-gray-500 uppercase">Berat Terkini</label>
                    <div class="flex items-end justify-between mt-2">
                        <span class="text-2xl font-black text-gray-900"
                            x-text="dombaData?.bobot_terakhir ? parseFloat(dombaData.bobot_terakhir).toFixed(1) + ' kg' : '-'"></span>
                        <span class="flex items-center gap-1 pb-1 text-xs font-bold text-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            +0.4 kg
                        </span>
                    </div>
                </div>

                <div class="flex flex-col justify-between p-4 bg-white border border-gray-200 rounded-xl">
                    <label class="text-xs font-bold tracking-wider text-gray-500 uppercase">ADG (30 Hari)</label>
                    <div class="flex items-end justify-between mt-2">
                        <span class="text-2xl font-black text-gray-900">-</span>
                        <span
                            class="px-2 py-0.5 text-xs font-bold bg-green-100 text-green-800 rounded-full mb-1">Normal</span>
                    </div>
                </div>

                <div class="flex flex-col justify-between p-4 bg-white border border-gray-200 rounded-xl">
                    <label class="text-xs font-bold tracking-wider text-gray-500 uppercase">Health State</label>
                    <div class="flex items-end justify-between mt-2">
                        <span class="text-2xl font-black text-gray-900"
                            x-text="dombaData?.status === 'karantina' ? 'Karantina' : 'Sehat'"></span>
                        <span
                            :class="dombaData?.status === 'karantina' ? 'border-red-500 text-red-500' :
                                'border-primary text-primary'"
                            class="px-2 py-0.5 text-xs font-bold border rounded-full mb-1"
                            x-text="dombaData?.status === 'karantina' ? 'Karantina Aktif' : 'Tidak Karantina'"></span>
                    </div>
                </div>

                <div class="flex flex-col justify-between p-4 bg-white border border-gray-200 rounded-xl">
                    <label class="text-xs font-bold tracking-wider text-gray-500 uppercase">Repro History</label>
                    <div class="flex items-end justify-between mt-2">
                        <span class="text-2xl font-black text-gray-900">-</span>
                        <span class="pb-1 text-xs font-bold text-gray-400">-</span>
                    </div>
                </div>
            </section>

            {{-- ══ SECTION 3: TABBED CONTENT ══ --}}
            <section class="overflow-hidden bg-white border border-gray-200 rounded-xl">
                <nav class="flex px-6 border-b border-gray-200 bg-gray-50">
                    @foreach ([['key' => 'pertumbuhan', 'label' => 'Pertumbuhan'], ['key' => 'kesehatan', 'label' => 'Kesehatan'], ['key' => 'pakan', 'label' => 'Pakan'], ['key' => 'silsilah', 'label' => 'Silsilah']] as $tab)
                        <button type="button" @click="activeTab = '{{ $tab['key'] }}'"
                            :class="activeTab === '{{ $tab['key'] }}' ? 'text-primary border-primary' :
                                'text-gray-500 border-transparent hover:text-primary'"
                            class="px-6 py-4 -mb-px text-sm font-bold transition-colors border-b-2">
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </nav>

                <div class="p-6 space-y-6">
                    {{-- ════ TAB: PERTUMBUHAN ════ --}}
                    <div x-show="activeTab === 'pertumbuhan'">

                        {{-- LINE CHART DINAMIS BERAT VS WAKTU --}}
                        <div class="p-4 mb-6 bg-white border border-gray-100 shadow-sm rounded-xl">
                            <div class="relative h-60">
                                {{-- x-ref menggantikan id agar aman dari duplikasi DOM --}}
                                <canvas x-ref="chartCanvas"></canvas>
                            </div>
                        </div>

                        {{-- Penimbangan table --}}
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        @foreach (['Tanggal', 'Berat', 'ADG', 'Petugas', 'Catatan'] as $col)
                                            <th
                                                class="py-3 pr-4 text-xs font-bold tracking-wider text-gray-500 uppercase">
                                                {{ $col }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-if="dombaData?.penimbangan?.length > 0">
                                        <template x-for="(timbang, i) in dombaData.penimbangan.slice(0, 5)"
                                            :key="i">
                                            <tr class="transition-colors hover:bg-gray-50">
                                                <td class="py-3.5 pr-4 font-semibold text-gray-700"
                                                    x-text="formatTgl(timbang.tanggal_timbang)"></td>
                                                <td class="py-3.5 pr-4 font-bold text-gray-900"
                                                    x-text="parseFloat(timbang.berat_kg).toFixed(1) + ' kg'"></td>
                                                <td class="py-3.5 pr-4 font-medium text-primary"
                                                    x-text="timbang.adg ? '+' + parseFloat(timbang.adg).toFixed(3) + ' kg/d' : '-'">
                                                </td>
                                                <td class="py-3.5 pr-4 text-gray-500">-</td>
                                                <td class="py-3.5 text-gray-500 italic"
                                                    x-text="timbang.catatan || '-'"></td>
                                            </tr>
                                        </template>
                                    </template>
                                    <template x-if="!dombaData?.penimbangan?.length">
                                        <tr>
                                            <td colspan="5" class="py-8 text-sm italic text-center text-gray-400">
                                                Belum ada data penimbangan</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-start pt-4">
                            <a :href="'/pertumbuhan?domba=' + dombaData?.ear_tag_id"
                                class="flex items-center gap-1 text-sm font-bold transition-all text-secondary hover:gap-2">
                                Lihat Semua Riwayat Penimbangan
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    {{-- ════ TAB: KESEHATAN ════ --}}
                    <div x-show="activeTab === 'kesehatan'" class="space-y-8 animate-fadeIn">
                        {{-- Sub-tabel 1: Rekam Medis Penyakit --}}
                        <div class="space-y-3">
                            <h4
                                class="flex items-center gap-2 text-sm font-bold tracking-wider text-gray-900 uppercase">
                                <span class="w-2 h-4 bg-red-600 rounded-sm"></span>
                                Riwayat Penyakit & Perawatan
                            </h4>

                            <div class="overflow-x-auto bg-white border border-gray-200 shadow-sm rounded-xl">
                                <table class="w-full text-sm text-left border-collapse">
                                    <thead class="border-b border-gray-200 bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-5 py-3 text-xs font-bold tracking-wider text-gray-500 uppercase">
                                                Tgl Sakit / Sembuh</th>
                                            <th
                                                class="px-5 py-3 text-xs font-bold tracking-wider text-gray-500 uppercase">
                                                Gejala & Diagnosa</th>
                                            <th
                                                class="px-5 py-3 text-xs font-bold tracking-wider text-gray-500 uppercase">
                                                Status</th>
                                            <th
                                                class="px-5 py-3 text-xs font-bold tracking-wider text-gray-500 uppercase">
                                                Obat Diberikan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-if="dombaData?.medical_record?.length > 0">
                                            <template x-for="(rekam, i) in dombaData.medical_record"
                                                :key="i">
                                                <tr class="transition-colors hover:bg-gray-50/50">
                                                    <td class="px-5 py-4 whitespace-nowrap">
                                                        <div class="font-semibold text-gray-800"
                                                            x-text="formatTgl(rekam.tanggal_sakit)"></div>
                                                        <div
                                                            class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                            Sembuh: <span
                                                                x-text="rekam.tanggal_sembuh ? formatTgl(rekam.tanggal_sembuh) : '—'"></span>
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-4">
                                                        <div class="font-medium text-gray-900"
                                                            x-text="rekam.diagnosa || 'Belum Ada Diagnosa'"></div>
                                                        <div class="text-xs text-gray-500 italic mt-0.5 max-w-xs truncate"
                                                            :title="rekam.gejala" x-text="'Gejala: ' + rekam.gejala">
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap">
                                                        <span
                                                            :class="{
                                                                'bg-red-100 text-red-800 border border-red-200': rekam
                                                                    .status === 'sakit',
                                                                'bg-amber-100 text-amber-800 border border-amber-200': rekam
                                                                    .status === 'dalam_perawatan',
                                                                'bg-green-100 text-green-800 border border-green-200': rekam
                                                                    .status === 'sembuh',
                                                                'bg-gray-100 text-gray-700 border border-gray-200': rekam
                                                                    .status === 'mati'
                                                            }"
                                                            class="px-2.5 py-1 text-xs font-bold rounded-full capitalize"
                                                            x-text="rekam.status.replace('_', ' ')"></span>
                                                    </td>
                                                    <td class="px-5 py-4">
                                                        <div class="space-y-1">
                                                            <template x-if="rekam.pemakaian_obat?.length > 0">
                                                                <template x-for="(p, idx) in rekam.pemakaian_obat"
                                                                    :key="idx">
                                                                    <div class="text-xs font-medium text-gray-600">
                                                                        <span x-text="p.nama_obat || 'Obat'"></span>
                                                                        <span class="font-bold text-primary"
                                                                            x-text="' ×' + parseInt(p.jumlah) + ' ' + (p.satuan || '')"></span>
                                                                    </div>
                                                                </template>
                                                            </template>
                                                            <template x-if="!rekam.pemakaian_obat?.length">
                                                                <span class="text-xs italic text-gray-400">— Tanpa Obat
                                                                    —</span>
                                                            </template>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </template>
                                        <template x-if="!dombaData?.medical_record?.length">
                                            <tr>
                                                <td colspan="4"
                                                    class="px-5 py-8 text-sm italic text-center text-gray-400">Domba
                                                    ini tidak memiliki catatan riwayat penyakit.</td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Sub-tabel 2: Log Vaksinasi --}}
                        <div class="space-y-3">
                            <h4
                                class="flex items-center gap-2 text-sm font-bold tracking-wider text-gray-900 uppercase">
                                <span class="w-2 h-4 rounded-sm bg-primary"></span>
                                Log Proteksi &amp; Vaksinasi
                            </h4>

                            <div class="overflow-x-auto bg-white border border-gray-200 shadow-sm rounded-xl">
                                <table class="w-full text-sm text-left border-collapse">
                                    <thead class="border-b border-gray-200 bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-5 py-3 text-xs font-bold tracking-wider text-gray-500 uppercase">
                                                Tanggal Vaksin</th>
                                            <th
                                                class="px-5 py-3 text-xs font-bold tracking-wider text-gray-500 uppercase">
                                                Nama Vaksin / Vitamin</th>
                                            <th
                                                class="px-5 py-3 text-xs font-bold tracking-wider text-gray-500 uppercase">
                                                Jadwal Berikutnya</th>
                                            <th
                                                class="px-5 py-3 text-xs font-bold tracking-wider text-gray-500 uppercase">
                                                Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-if="dombaData?.vaksinasi?.length > 0">
                                            <template x-for="(vaksin, vIdx) in dombaData.vaksinasi"
                                                :key="vIdx">
                                                <tr class="transition-colors hover:bg-gray-50/50">
                                                    <td class="px-5 py-4 font-semibold text-gray-700 whitespace-nowrap"
                                                        x-text="formatTgl(vaksin.tanggal_vaksinasi)"></td>
                                                    <td class="px-5 py-4 font-bold text-gray-900">
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                                            <span x-text="vaksin.nama_obat || 'Vaksin'"></span>
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-4 whitespace-nowrap">
                                                        <template x-if="vaksin.tanggal_berikutnya">
                                                            <div
                                                                class="flex items-center gap-1 text-sm font-semibold text-blue-600 bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-lg w-fit">
                                                                <svg class="w-3.5 h-3.5" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2.5"
                                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                                <span
                                                                    x-text="formatTgl(vaksin.tanggal_berikutnya)"></span>
                                                            </div>
                                                        </template>
                                                        <template x-if="!vaksin.tanggal_berikutnya">
                                                            <span class="text-xs italic text-gray-400">Sekali
                                                                Pemberian</span>
                                                        </template>
                                                    </td>
                                                    <td class="max-w-xs px-5 py-4 italic text-gray-500 truncate"
                                                        :title="vaksin.catatan || '-'" x-text="vaksin.catatan || '-'">
                                                    </td>
                                                </tr>
                                            </template>
                                        </template>
                                        <template x-if="!dombaData?.vaksinasi?.length">
                                            <tr>
                                                <td colspan="4"
                                                    class="px-5 py-8 text-sm italic text-center text-gray-400">Belum
                                                    ada data injeksi vaksinasi pada domba ini.</td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- ════ TAB: PAKAN ════ --}}
                    <div x-show="activeTab === 'pakan'" class="space-y-6 animate-fadeIn">

                        {{-- Ringkasan Pakan & Shortcut FCR --}}
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="flex items-center gap-4 p-4 border border-gray-200 rounded-xl bg-gray-50">
                                <div
                                    class="flex items-center justify-center w-10 h-10 rounded-full bg-amber-100 text-amber-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold tracking-wider text-gray-500 uppercase">Total Pakan
                                        (30 Hari)</p>
                                    <p class="text-xl font-black text-gray-900"><span
                                            x-text="dombaData?.total_pakan_30_hari ? parseFloat(dombaData.total_pakan_30_hari).toFixed(2) : '0.00'"></span>
                                        <span class="text-sm font-semibold text-gray-500">kg</span>
                                    </p>
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-between p-4 border border-blue-100 md:col-span-2 rounded-xl bg-blue-50">
                                <div>
                                    <p class="text-sm font-bold text-blue-900">Analisis FCR & Rekomendasi</p>
                                    <p class="mt-1 text-xs text-blue-700">Lihat Feed Conversion Ratio (FCR) detail dan
                                        persentase komposisi pakan ideal.</p>
                                </div>
                                <a :href="'/pakan-individual?search=' + dombaData?.ear_tag_id"
                                    class="px-4 py-2 text-xs font-bold text-white transition-colors bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 whitespace-nowrap">
                                    Buka Modul Pakan
                                </a>
                            </div>
                        </div>

                        {{-- Tabel Log Pakan --}}
                        <div class="space-y-3">
                            <h4
                                class="flex items-center gap-2 text-sm font-bold tracking-wider text-gray-900 uppercase">
                                <span class="w-2 h-4 rounded-sm bg-amber-500"></span>
                                Log Pemberian Pakan (30 Data Terakhir)
                            </h4>

                            <div class="overflow-x-auto bg-white border border-gray-200 shadow-sm rounded-xl">
                                <table class="w-full text-sm text-left border-collapse">
                                    <thead class="border-b border-gray-200 bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-5 py-3 text-xs font-bold tracking-wider text-gray-500 uppercase">
                                                Tanggal & Sesi</th>
                                            <th
                                                class="px-5 py-3 text-xs font-bold tracking-wider text-gray-500 uppercase">
                                                Jenis Pakan</th>
                                            <th
                                                class="px-5 py-3 text-xs font-bold tracking-wider text-gray-500 uppercase">
                                                Nama Pakan</th>
                                            <th
                                                class="px-5 py-3 text-xs font-bold tracking-wider text-gray-500 uppercase">
                                                Jumlah Diberikan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-if="dombaData?.pemberian_pakan?.length > 0">
                                            <template x-for="(pakan, i) in dombaData.pemberian_pakan"
                                                :key="i">
                                                <tr class="transition-colors hover:bg-gray-50/50">
                                                    <td class="px-5 py-4 whitespace-nowrap">
                                                        <div class="font-semibold text-gray-800"
                                                            x-text="formatTgl(pakan.tanggal_pemberian)"></div>
                                                        <div class="text-xs mt-0.5 flex items-center gap-1 font-bold"
                                                            :class="pakan.sesi === 'pagi' ? 'text-amber-500' :
                                                                'text-indigo-500'">
                                                            <svg x-show="pakan.sesi === 'pagi'" class="w-3.5 h-3.5"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                                                </path>
                                                            </svg>
                                                            <svg x-show="pakan.sesi === 'sore'" class="w-3.5 h-3.5"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                                                </path>
                                                            </svg>
                                                            <span class="capitalize" x-text="pakan.sesi"></span>
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-4">
                                                        <span
                                                            class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-gray-100 text-gray-600 uppercase tracking-widest whitespace-nowrap"
                                                            x-text="pakan.jenis.replace('_', ' ')"></span>
                                                    </td>
                                                    <td class="px-5 py-4 font-bold text-gray-900"
                                                        x-text="pakan.nama_pakan"></td>
                                                    <td class="px-5 py-4">
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="font-black text-amber-600"
                                                                x-text="parseFloat(pakan.jumlah_gram).toLocaleString('id-ID')"></span>
                                                            <span
                                                                class="text-xs font-semibold text-gray-500">gram</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </template>
                                        <template x-if="!dombaData?.pemberian_pakan?.length">
                                            <tr>
                                                <td colspan="4"
                                                    class="px-5 py-8 text-sm italic text-center text-gray-400">
                                                    Belum ada log pemberian pakan yang tercatat untuk domba ini.
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- ════ TAB: SILSILAH ════ --}}
                    <div x-show="activeTab === 'silsilah'" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                            <div class="p-6 bg-white border border-gray-200 lg:col-span-2 rounded-xl">
                                <h4
                                    class="flex items-center gap-2 mb-4 text-sm font-bold tracking-widest text-gray-900 uppercase">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                    </svg>
                                    Silsilah Domba
                                </h4>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div
                                        class="flex items-center justify-between gap-3 p-4 border border-gray-200 rounded-lg">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="flex items-center justify-center flex-shrink-0 w-10 h-10 bg-green-100 rounded-full">
                                                <span class="font-bold text-green-700">♀</span>
                                            </div>
                                            <div class="min-w-0">
                                                <label
                                                    class="block text-xs font-bold tracking-wider text-gray-500 uppercase">Induk
                                                    (Dam)</label>
                                                <span class="block text-sm font-bold truncate"
                                                    x-text="dombaData?.induk?.ear_tag_id ? dombaData.induk.ear_tag_id + ' (' + (dombaData.induk.nama || '-') + ')' : '-'"></span>
                                            </div>
                                        </div>
                                        <button type="button"
                                            class="flex-shrink-0 px-3 py-1 text-xs font-black uppercase transition-colors border rounded border-secondary text-secondary hover:bg-green-50"
                                            x-show="dombaData?.induk">Lihat →</button>
                                    </div>

                                    <div
                                        class="flex items-center justify-between gap-3 p-4 border border-gray-200 rounded-lg">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="flex items-center justify-center flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full">
                                                <span class="font-bold text-blue-700">♂</span>
                                            </div>
                                            <div class="min-w-0">
                                                <label
                                                    class="block text-xs font-bold tracking-wider text-gray-500 uppercase">Pejantan
                                                    (Sire)</label>
                                                <span class="block text-sm font-bold truncate"
                                                    x-text="dombaData?.ayah?.ear_tag_id ? dombaData.ayah.ear_tag_id + ' (' + (dombaData.ayah.nama || '-') + ')' : '-'"></span>
                                            </div>
                                        </div>
                                        <button type="button"
                                            class="flex-shrink-0 px-3 py-1 text-xs font-black uppercase transition-colors border rounded border-secondary text-secondary hover:bg-blue-50"
                                            x-show="dombaData?.ayah">Lihat →</button>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex flex-col items-center justify-center p-6 text-center border border-gray-200 rounded-xl bg-gray-50">
                                <label class="mb-2 text-xs font-bold tracking-wider text-gray-500 uppercase">Inbreeding
                                    Coefficient</label>
                                <div class="mb-2 text-4xl font-black text-primary">-</div>
                                <span
                                    class="px-3 py-1 text-xs font-bold text-green-800 bg-green-100 rounded-full">Aman</span>
                                <p class="mt-4 text-xs leading-relaxed text-gray-500">Akan dihitung di modul Silsilah
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ══ SECTION 4: AKSI CEPAT ══ --}}
            <section class="space-y-4">
                <h4 class="text-sm font-bold tracking-widest text-gray-900 uppercase">Aksi Cepat &amp; Log</h4>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
                    @foreach ([
        ['label' => 'Timbang Berat', 'path' => '/pertumbuhan', 'color' => 'text-primary', 'icon' => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3'],
        ['label' => 'Rekam Medis', 'path' => '/kesehatan', 'color' => 'text-accent', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['label' => 'Jadwal Pakan', 'path' => '/pakan-individual', 'color' => 'text-secondary', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ['label' => 'Data Silsilah', 'path' => '/silsilah/', 'color' => 'text-primary', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ['label' => 'Vaksinasi', 'path' => '/kesehatan?tab=vaksinasi', 'color' => 'text-accent', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
    ] as $aksi)
                        <a :href="@if ($aksi['label'] === 'Data Silsilah') '{{ $aksi['path'] }}' + dombaData?.ear_tag_id @else '{{ $aksi['path'] }}' + ('{{ $aksi['path'] }}'.includes('?') ? '&' : '?') + 'domba=' + dombaData?.ear_tag_id @endif"
                            class="flex flex-col items-center p-4 text-center transition-shadow bg-white border border-gray-200 rounded-xl hover:shadow-md">
                            <svg class="w-8 h-8 {{ $aksi['color'] }} mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $aksi['icon'] }}" />
                            </svg>
                            <span
                                class="mb-3 text-xs font-bold leading-tight text-gray-700">{{ $aksi['label'] }}</span>
                            <button type="button"
                                class="w-full py-1.5 border border-gray-300 text-xs font-black uppercase rounded hover:bg-gray-50 transition-colors">Buka
                                →</button>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>

        {{-- ── FOOTER ── --}}
        <footer
            class="flex flex-wrap items-center justify-between gap-4 p-6 bg-white border-t border-gray-200 rounded-b-xl">
            <a :href="dombaData ? '/domba/' + dombaData.ear_tag_id + '/export-pdf' : '#'" target="_blank"
                :class="{ 'opacity-50 cursor-not-allowed pointer-events-none': !dombaData }"
                class="inline-flex items-center gap-2 px-6 py-2.5 border-2 border-gray-300 text-gray-600 text-sm font-bold rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Export PDF Profil
            </a>

            <div class="flex gap-3 ml-auto">
                <button type="button" @click="modalDetail = false"
                    class="px-8 py-2.5 border-2 border-gray-300 text-gray-600 text-sm font-bold rounded-lg hover:bg-gray-50 transition-colors">Tutup</button>
                <button type="button" @click="modalDetail = false; editDomba(dombaData?.ear_tag_id)"
                    class="px-8 py-2.5 bg-primary text-white text-sm font-bold rounded-lg hover:opacity-90 transition-all flex items-center gap-2 shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Data Domba
                </button>
            </div>
        </footer>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush
