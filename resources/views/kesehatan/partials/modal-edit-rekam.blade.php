{{-- Modal: Edit Rekam Medis --}}
<div x-show="showEditRekam" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     @keydown.escape.window="showEditRekam = false">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl flex flex-col overflow-hidden border border-outline-variant/30 max-h-[92vh]"
         @click.outside="showEditRekam = false">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-outline-variant/30 flex justify-between items-start flex-shrink-0">
            <div>
                <h2 class="text-xl font-bold text-on-surface">Edit Rekam Medis</h2>
                <p class="text-[10px] font-mono text-outline uppercase tracking-wider mt-0.5"
                   x-text="'ID: RKM-' + editRekamData.rekam_id + ' · ' + editRekamData.ear_tag_id"></p>
            </div>
            <button type="button" @click="showEditRekam = false" class="p-1.5 hover:bg-surface-container rounded-lg text-outline">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        {{-- PUT form --}}
        <form id="edit-rekam-form" method="POST" :action="'/kesehatan/' + editRekamData.rekam_id" class="overflow-y-auto flex-1">
            @csrf
            @method('PUT')

            {{-- Amber warning banner --}}
            <div class="px-6 pt-5">
                <div class="flex items-start gap-3 p-3.5 bg-amber-50 border border-amber-200 rounded-xl">
                    <span class="material-symbols-outlined text-amber-600 flex-shrink-0 text-lg mt-0.5">warning</span>
                    <p class="text-xs font-medium text-amber-800">
                        Perubahan status ke <strong>Sembuh</strong> atau <strong>Mati</strong> akan memperbarui status domba secara otomatis.
                        Menambah pemakaian obat baru akan mengurangi stok.
                    </p>
                </div>
            </div>

            <div class="p-6 space-y-5">

                {{-- Info card (4 cols) --}}
                <div class="grid grid-cols-4 gap-3">
                    <div class="p-3.5 bg-primary/5 border border-primary/20 rounded-xl">
                        <p class="text-[10px] font-bold text-outline uppercase tracking-wider mb-1">EAR TAG</p>
                        <p class="text-lg font-black text-primary" x-text="editRekamData.ear_tag_id"></p>
                        <p class="text-[10px] text-outline mt-0.5" x-text="editRekamData.ras ?? ''"></p>
                    </div>
                    <div class="p-3.5 bg-surface-container-low border border-outline-variant rounded-xl">
                        <p class="text-[10px] font-bold text-outline uppercase tracking-wider mb-1">NAMA</p>
                        <p class="text-sm font-bold text-on-surface" x-text="editRekamData.nama_domba ?? '—'"></p>
                        <p class="text-[10px] text-outline mt-0.5" x-text="editRekamData.kategori ?? ''"></p>
                    </div>
                    <div class="p-3.5 bg-surface-container-low border border-outline-variant rounded-xl">
                        <p class="text-[10px] font-bold text-outline uppercase tracking-wider mb-1">STATUS DOMBA</p>
                        <p class="text-sm font-bold" :class="{
                            'text-error': editRekamData.domba_status === 'karantina',
                            'text-primary': editRekamData.domba_status === 'aktif',
                            'text-outline': editRekamData.domba_status === 'mati'
                        }" x-text="editRekamData.domba_status ? editRekamData.domba_status.charAt(0).toUpperCase() + editRekamData.domba_status.slice(1) : '—'"></p>
                    </div>
                    <div class="p-3.5 bg-surface-container-low border border-outline-variant rounded-xl">
                        <p class="text-[10px] font-bold text-outline uppercase tracking-wider mb-1">KANDANG</p>
                        <p class="text-sm font-bold text-on-surface" x-text="editRekamData.nama_kandang ?? '—'"></p>
                    </div>
                </div>

                {{-- Tanggal Sakit + Tanggal Sembuh (2-col) --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-on-surface">Tanggal Sakit <span class="text-error">*</span></label>
                        <input type="date" name="tanggal_sakit" x-model="editRekamData.tanggal_sakit" required
                               class="w-full px-3.5 py-2.5 border border-outline-variant rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-on-surface">Tanggal Sembuh / Mati</label>
                        <input type="date" name="tanggal_sembuh" x-model="editRekamData.tanggal_sembuh"
                               class="w-full px-3.5 py-2.5 border border-outline-variant rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-on-surface">Gejala <span class="text-error">*</span></label>
                    <textarea name="gejala" rows="2" required
                              class="w-full px-3.5 py-2.5 border border-outline-variant rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"
                              x-model="editRekamData.gejala"></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-on-surface">Diagnosa</label>
                    <textarea name="diagnosa" rows="2"
                              class="w-full px-3.5 py-2.5 border border-outline-variant rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"
                              x-model="editRekamData.diagnosa"></textarea>
                </div>

                {{-- Status (radio pills) --}}
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-on-surface">Status Rekam Medis <span class="text-error">*</span></label>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach(['sakit' => 'Sakit', 'dalam_perawatan' => 'Dalam Perawatan', 'sembuh' => 'Sembuh', 'mati' => 'Mati'] as $val => $lbl)
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="{{ $val }}"
                                   x-model="editRekamData.status"
                                   @change="editRekamStatus = '{{ $val }}'"
                                   class="sr-only peer">
                            <div class="text-center py-2.5 px-3 border border-outline-variant rounded-lg text-xs font-bold
                                        peer-checked:bg-gray-900 peer-checked:text-white peer-checked:border-gray-900
                                        hover:bg-surface-container-low transition-colors text-on-surface-variant">
                                {{ $lbl }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Karantina toggle --}}
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-on-surface">Tandai Karantina</label>
                    <div class="flex gap-3">
                        <label class="cursor-pointer flex-1">
                            <input type="radio" name="tandai_karantina" value="ya" x-model="editKarantina" class="sr-only peer">
                            <div class="flex items-center justify-center gap-2 py-2.5 border border-outline-variant rounded-lg text-xs font-bold
                                        peer-checked:bg-error/10 peer-checked:border-error peer-checked:text-error
                                        hover:bg-surface-container-low transition-colors text-on-surface-variant">
                                <span class="material-symbols-outlined text-sm">health_and_safety</span> Ya, Karantina
                            </div>
                        </label>
                        <label class="cursor-pointer flex-1">
                            <input type="radio" name="tandai_karantina" value="tidak" x-model="editKarantina" class="sr-only peer">
                            <div class="flex items-center justify-center gap-2 py-2.5 border border-outline-variant rounded-lg text-xs font-bold
                                        peer-checked:bg-surface-container peer-checked:border-outline peer-checked:text-on-surface-variant
                                        hover:bg-surface-container-low transition-colors text-on-surface-variant">
                                <span class="material-symbols-outlined text-sm">check_circle</span> Tidak
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Kandang karantina (shown when editKarantina = 'ya') --}}
                <div x-show="editKarantina === 'ya'" class="space-y-1.5">
                    <label class="text-xs font-semibold text-on-surface">Kandang Karantina</label>
                    <select name="kandang_karantina_id"
                            class="w-full px-3.5 py-2.5 border border-outline-variant rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option value="">— Kandang Saat Ini —</option>
                        @foreach($kandangList as $kd)
                        <option value="{{ $kd->kandang_id }}" x-bind:selected="editRekamData.kandang_id == {{ $kd->kandang_id }}">
                            {{ $kd->nama_kandang }} ({{ $kd->tipe }}, kap. {{ $kd->kapasitas }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Pemakaian Obat --}}
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-semibold text-on-surface">Pemakaian Obat</label>
                        <button type="button" @click="addEditObatRow()"
                                class="flex items-center gap-1 px-3 py-1.5 text-[10px] font-bold text-primary border border-primary/30 rounded-lg hover:bg-primary/5 transition-colors">
                            <span class="material-symbols-outlined text-sm">add</span> Tambah Obat
                        </button>
                    </div>

                    {{-- Existing pemakaian rows (server-rendered, read-only with hapus) --}}
                    @php $existingPemakaian = $pemakaianMap->get(0, collect()); @endphp
                    <template x-if="editRekamData.pemakaian && editRekamData.pemakaian.length > 0">
                        <div class="border border-outline-variant rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-surface-container-low border-b border-outline-variant">
                                    <tr>
                                        <th class="px-4 py-2.5 text-left text-[10px] font-bold text-outline uppercase tracking-wider">Nama Obat</th>
                                        <th class="px-4 py-2.5 text-center text-[10px] font-bold text-outline uppercase tracking-wider">Jumlah</th>
                                        <th class="px-4 py-2.5 text-center text-[10px] font-bold text-outline uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant/20">
                                    <template x-for="p in editRekamData.pemakaian" :key="p.pakai_id">
                                        <tr>
                                            <td class="px-4 py-2.5 font-medium text-on-surface" x-text="p.nama_obat"></td>
                                            <td class="px-4 py-2.5 text-center">
                                                <span class="font-bold" x-text="p.jumlah"></span>
                                                <span class="text-outline text-xs" x-text="' ' + (p.satuan ?? '')"></span>
                                            </td>
                                            <td class="px-4 py-2.5 text-center">
                                                <button type="button"
                                                        @click="hapusPemakaian(p.pakai_id)"
                                                        class="text-[10px] font-bold text-error hover:bg-error/5 px-2 py-1 rounded border border-error/20 transition-colors">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                    <p x-show="!editRekamData.pemakaian || editRekamData.pemakaian.length === 0"
                       class="text-xs text-outline italic">Belum ada pemakaian obat.</p>

                    {{-- New obat rows (Alpine) --}}
                    <template x-for="(row, idx) in editObatRows" :key="idx">
                        <div class="flex gap-2 items-center">
                            <select :name="'obat_ids[' + idx + ']'" x-model="row.obat_id" required
                                    class="flex-1 px-3 py-2 border border-outline-variant rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option value="">— Pilih Obat —</option>
                                @foreach($obatList as $ob)
                                <option value="{{ $ob->obat_id }}">{{ $ob->nama_obat }} (stok: {{ $ob->stok }} {{ $ob->satuan }})</option>
                                @endforeach
                            </select>
                            <input type="number" :name="'obat_jumlah[' + idx + ']'" x-model.number="row.jumlah"
                                   min="1" placeholder="Jml"
                                   class="w-20 px-3 py-2 border border-outline-variant rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                            <button type="button" @click="removeEditObatRow(idx)"
                                    class="p-2 text-error hover:bg-error/5 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-lg">close</span>
                            </button>
                        </div>
                    </template>
                </div>

            </div>
        </form>

        {{-- Footer --}}
        <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant/30 flex justify-between items-center flex-shrink-0">
            <div class="flex gap-2">
                {{-- Hapus record --}}
                <form method="POST" :action="'/kesehatan/' + editRekamData.rekam_id"
                      @submit.prevent="if(confirm('Hapus rekam medis ini beserta seluruh pemakaian obat?')) $el.submit()">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-error border border-error/30 rounded-lg hover:bg-error/5 transition-colors">
                        <span class="material-symbols-outlined text-base">delete</span> Hapus
                    </button>
                </form>
                {{-- Tandai Sembuh shortcut --}}
                <button type="button" @click="tandaiSembuh()"
                        class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-primary border border-primary/30 rounded-lg hover:bg-primary/5 transition-colors">
                    <span class="material-symbols-outlined text-base">check_circle</span> Tandai Sembuh
                </button>
            </div>
            <div class="flex gap-3">
                <button type="button" @click="showEditRekam = false"
                        class="px-5 py-2.5 border border-outline-variant text-on-surface-variant font-semibold text-sm rounded-lg hover:bg-surface-container transition-colors">
                    Batal
                </button>
                <button type="submit" form="edit-rekam-form"
                        class="px-6 py-2.5 bg-primary text-white font-bold text-sm rounded-lg shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </div>

    </div>
</div>
