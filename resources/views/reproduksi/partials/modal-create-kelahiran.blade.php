{{-- Modal: Catat Kelahiran --}}
<div x-show="showCreateKelahiran" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     @keydown.escape.window="showCreateKelahiran = false">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden border border-gray-100"
         @click.outside="showCreateKelahiran = false">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-start bg-white">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Catat Kelahiran</h2>
                <p class="text-[10px] font-mono text-gray-400 uppercase tracking-wider mt-0.5">FR-7.4 · tabel KELAHIRAN + ANAK_LAHIR</p>
            </div>
            <button type="button" @click="showCreateKelahiran = false" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <div class="overflow-y-auto flex-1">
            <form method="POST" :action="'/reproduksi/' + kelahiranData.kawin_id + '/kelahiran'">
                @csrf

                <div class="p-6 space-y-6">

                    {{-- Perkawinan Info --}}
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Perkawinan Terpilih</p>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold">ID</p>
                                <p class="font-mono font-bold text-primary" x-text="'KWN-' + kelahiranData.kawin_id"></p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold">Pasangan</p>
                                <p class="font-semibold text-gray-800 text-xs" x-text="kelahiranData.pejantan_id + ' × ' + kelahiranData.indukan_id"></p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold">Estimasi HPL</p>
                                <p class="font-bold text-primary" x-text="kelahiranData.estimasi_lahir || '—'"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Tanggal Kelahiran --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-700">Tanggal Kelahiran <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_kelahiran" value="{{ today()->toDateString() }}" required
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                    </div>

                    {{-- Jumlah & Bobot --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-700">Anak Hidup <span class="text-red-500">*</span></label>
                            <input type="number" name="jml_anak_hidup" min="0" value="1" required
                                   x-model.number="kelahiranAnakHidup"
                                   @input="syncAnakRows()"
                                   class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-center font-bold"/>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-700">Anak Mati</label>
                            <input type="number" name="jml_anak_mati" min="0" value="0"
                                   x-model.number="kelahiranAnakMati"
                                   class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-center font-bold"/>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-700">Bobot Rata-rata (kg)</label>
                            <input type="number" name="bobot_rata_rata" step="0.01" min="0" placeholder="0.00"
                                   class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-center"/>
                        </div>
                    </div>

                    {{-- Anak Lahir Rows --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-semibold text-gray-700">Detail Anak Lahir (Hidup)</label>
                            <span class="text-[10px] text-gray-400 font-mono">tabel anak_lahir</span>
                        </div>

                        <template x-for="(anak, idx) in anakRows" :key="idx">
                            <div class="p-3.5 border border-gray-200 rounded-xl bg-gray-50 space-y-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-bold text-gray-600" x-text="'Anak #' + (idx + 1)"></p>
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">Hidup</span>
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-semibold text-gray-500 uppercase">Ear Tag <span class="font-normal">(opsional)</span></label>
                                        <input type="text" :name="'anak[' + idx + '][ear_tag_id]'"
                                               placeholder="e.g. SHP-0099"
                                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white"/>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-semibold text-gray-500 uppercase">Jenis Kelamin <span class="text-red-400">*</span></label>
                                        <select :name="'anak[' + idx + '][jenis_kelamin]'" required
                                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white">
                                            <option value="jantan">Jantan</option>
                                            <option value="betina">Betina</option>
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-semibold text-gray-500 uppercase">Bobot (kg)</label>
                                        <input type="number" :name="'anak[' + idx + '][bobot_lahir]'" step="0.01" min="0" placeholder="0.00"
                                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white"/>
                                    </div>
                                </div>
                                <input type="hidden" :name="'anak[' + idx + '][kondisi]'" value="hidup">
                            </div>
                        </template>

                        <p x-show="anakRows.length === 0" class="text-xs text-gray-400 italic text-center py-2">Jumlah anak hidup = 0, tidak ada detail anak.</p>
                    </div>

                    {{-- Catatan --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-700">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <textarea name="catatan" rows="2" placeholder="Catatan tambahan tentang kelahiran..."
                                  class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"></textarea>
                    </div>

                    {{-- Info --}}
                    <div class="p-3.5 bg-blue-50 border border-blue-100 rounded-lg flex gap-2.5">
                        <span class="material-symbols-outlined text-blue-500 text-base mt-0.5 shrink-0">info</span>
                        <p class="text-xs text-blue-800">
                            Menyimpan kelahiran akan mengubah status perkawinan menjadi <strong>Lahir</strong> secara otomatis.
                        </p>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="showCreateKelahiran = false"
                            class="px-5 py-2.5 border border-gray-300 text-gray-600 font-semibold text-sm rounded-lg hover:bg-gray-100 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 bg-primary text-white font-bold text-sm rounded-lg shadow-lg shadow-green-900/20 hover:bg-green-800 transition-all active:scale-95 flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">child_care</span> Simpan Kelahiran
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
