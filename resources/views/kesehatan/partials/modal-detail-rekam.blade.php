{{-- Modal: Detail Rekam Medis (UC-05 Read) --}}
<div x-show="showDetailRekam" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     @keydown.escape.window="showDetailRekam = false">

    <div class="bg-surface-container-lowest w-full max-w-4xl max-h-[92vh] flex flex-col overflow-hidden shadow-2xl rounded-lg border border-outline-variant"
         @click.outside="showDetailRekam = false">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-surface-container-highest flex justify-between items-start flex-shrink-0">
            <div class="space-y-1">
                <h2 class="text-2xl font-extrabold text-on-surface leading-tight"
                    x-text="'Rekam Medis — ' + detailRekamData.ear_tag_id + (detailRekamData.nama_domba ? ' (' + detailRekamData.nama_domba + ')' : '')"></h2>
                <p class="text-xs text-on-surface-variant font-medium tracking-tight"
                   x-text="'UC-05 (Read) | ID: RKM-' + detailRekamData.rekam_id + ' | Kamus Data: MEDICAL_RECORD + PEMAKAIAN_OBAT'"></p>
            </div>
            <button type="button" @click="showDetailRekam = false"
                    class="p-1 hover:bg-surface-container rounded-full transition-colors text-on-surface-variant">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        {{-- Banner --}}
        <div class="bg-surface-container-high px-6 py-2 border-b border-surface-container-highest flex-shrink-0">
            <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">READ ONLY — DETAIL REKAM MEDIS</span>
        </div>

        <div class="overflow-y-auto flex-1">
            <div class="p-6 space-y-6">

                {{-- Section 1: Karantina Context (shown only when karantina) --}}
                <div x-show="detailRekamData.domba_status === 'karantina'"
                     class="bg-surface-container-low border border-secondary-container rounded-lg p-5 flex gap-4 items-start">
                    <span class="material-symbols-outlined text-primary mt-0.5 flex-shrink-0">info</span>
                    <p class="text-sm text-on-surface leading-relaxed">
                        Domba ini sedang dalam status <strong class="text-primary font-bold">Karantina</strong>.
                        Ditempatkan di <strong class="text-primary font-bold" x-text="detailRekamData.nama_kandang ?? '—'"></strong>.
                        Untuk memindahkan, gunakan tombol <em>Pindah Kandang</em> di tab Karantina.
                    </p>
                </div>

                {{-- Section 2: Informasi Rekam Medis --}}
                <div class="border border-outline-variant rounded-lg overflow-hidden bg-white">
                    <div class="px-5 py-3 border-b border-outline-variant bg-surface-container-lowest">
                        <h3 class="text-xs font-black tracking-widest text-on-surface-variant uppercase">INFORMASI REKAM MEDIS</h3>
                    </div>
                    <div class="p-5 space-y-6">
                        {{-- Row 1 --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-[10px] font-bold text-outline uppercase mb-1">EAR TAG / KODE</label>
                                <span class="text-sm font-semibold text-on-surface" x-text="detailRekamData.ear_tag_id ?? '—'"></span>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-outline uppercase mb-1">NAMA DOMBA</label>
                                <span class="text-sm font-semibold text-on-surface" x-text="detailRekamData.nama_domba ?? '—'"></span>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-outline uppercase mb-1">KATEGORI</label>
                                <span class="text-sm font-semibold text-on-surface"
                                      x-text="(detailRekamData.kategori ?? '—') + (detailRekamData.ras ? ' — ' + detailRekamData.ras : '')"></span>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-outline uppercase mb-1">REKAM ID</label>
                                <span class="text-sm font-semibold text-on-surface" x-text="'RKM-' + detailRekamData.rekam_id"></span>
                            </div>
                        </div>
                        {{-- Row 2 --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-4 border-t border-surface-container">
                            <div>
                                <label class="block text-[10px] font-bold text-outline uppercase mb-1">TANGGAL SAKIT</label>
                                <span class="text-sm font-semibold text-on-surface" x-text="detailRekamData.tanggal_sakit ?? '—'"></span>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-outline uppercase mb-1">STATUS</label>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold"
                                      :class="{
                                          'bg-inverse-surface text-inverse-on-surface': detailRekamData.status === 'sakit',
                                          'bg-tertiary-container/30 text-tertiary': detailRekamData.status === 'dalam_perawatan',
                                          'bg-secondary-container text-on-secondary-container': detailRekamData.status === 'sembuh',
                                          'bg-surface-container-high text-outline': detailRekamData.status === 'mati'
                                      }"
                                      x-text="{sakit:'Sakit',dalam_perawatan:'Dalam Perawatan',sembuh:'Sembuh',mati:'Mati'}[detailRekamData.status] ?? detailRekamData.status">
                                </span>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-outline uppercase mb-1">TANGGAL SEMBUH</label>
                                <template x-if="detailRekamData.tanggal_sembuh">
                                    <span class="text-sm font-semibold text-on-surface" x-text="detailRekamData.tanggal_sembuh"></span>
                                </template>
                                <template x-if="!detailRekamData.tanggal_sembuh">
                                    <span class="text-sm font-medium text-outline italic">— masih sakit</span>
                                </template>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-outline uppercase mb-1">KARANTINA</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold"
                                      :class="detailRekamData.domba_status === 'karantina'
                                          ? 'bg-secondary-container text-on-secondary-container'
                                          : 'bg-surface-container text-outline'"
                                      x-text="detailRekamData.domba_status === 'karantina'
                                          ? 'Ya — ' + (detailRekamData.nama_kandang ?? 'Isolasi')
                                          : 'Tidak'">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Gejala & Diagnosa --}}
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="border border-outline-variant rounded-lg p-5 bg-white">
                        <label class="block text-[10px] font-bold text-outline uppercase mb-3">GEJALA</label>
                        <p class="text-sm text-on-surface leading-relaxed" x-text="detailRekamData.gejala ?? '—'"></p>
                    </div>
                    <div class="border border-outline-variant rounded-lg p-5 bg-white">
                        <label class="block text-[10px] font-bold text-outline uppercase mb-3">DIAGNOSA</label>
                        <template x-if="detailRekamData.diagnosa">
                            <p class="text-sm text-on-surface leading-relaxed" x-text="detailRekamData.diagnosa"></p>
                        </template>
                        <template x-if="!detailRekamData.diagnosa">
                            <p class="text-sm text-outline italic">— belum ada diagnosa</p>
                        </template>
                    </div>
                </div>

                {{-- Section 4: Obat Diberikan --}}
                <div class="space-y-3">
                    <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">OBAT DIBERIKAN (KAMUS DATA: TABEL PEMAKAIAN_OBAT)</label>
                    <template x-if="detailRekamData.pemakaian && detailRekamData.pemakaian.length > 0">
                        <div class="border border-outline-variant rounded-lg overflow-hidden">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-surface-container-high border-b border-outline-variant">
                                    <tr>
                                        <th class="px-4 py-3 text-[10px] font-black text-on-surface-variant uppercase">Nama Obat</th>
                                        <th class="px-4 py-3 text-[10px] font-black text-on-surface-variant uppercase">Jumlah</th>
                                        <th class="px-4 py-3 text-[10px] font-black text-on-surface-variant uppercase">Tgl Pemberian</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-surface-container-highest">
                                    <template x-for="p in detailRekamData.pemakaian" :key="p.pakai_id">
                                        <tr class="bg-white hover:bg-surface-container-lowest transition-colors">
                                            <td class="px-4 py-3 text-sm font-semibold text-on-surface" x-text="p.nama_obat"></td>
                                            <td class="px-4 py-3 text-sm text-on-surface">
                                                <span x-text="p.jumlah"></span>
                                                <span class="text-outline" x-text="' ' + (p.satuan ?? '')"></span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-on-surface" x-text="p.tanggal_pakai ?? '—'"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                    <template x-if="!detailRekamData.pemakaian || detailRekamData.pemakaian.length === 0">
                        <p class="text-sm text-outline italic border border-outline-variant rounded-lg px-4 py-3 bg-white">Tidak ada pemakaian obat.</p>
                    </template>
                </div>

                {{-- Footer Metadata --}}
                <div class="pt-2">
                    <p class="text-[11px] text-outline text-center"
                       x-text="'Rekam ID: RKM-' + detailRekamData.rekam_id + ' | Tgl Input: ' + (detailRekamData.created_at ?? '—')"></p>
                </div>

            </div>
        </div>

        {{-- Footer Buttons --}}
        <div class="px-6 py-5 bg-surface-container-lowest border-t border-surface-container-highest flex justify-end gap-3 flex-shrink-0">
            <button type="button" @click="showDetailRekam = false"
                    class="px-6 py-2.5 text-sm font-bold text-on-surface border border-outline rounded hover:bg-surface-container transition-all">
                Tutup
            </button>
            <button type="button"
                    @click="showDetailRekam = false; openEditRekam(detailRekamData.rekam_id)"
                    class="px-6 py-2.5 text-sm font-bold text-white bg-primary rounded hover:opacity-90 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                Edit Rekam Medis
            </button>
        </div>

    </div>
</div>
