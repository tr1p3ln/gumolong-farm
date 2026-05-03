{{-- Modal: Edit Kelahiran --}}
<div x-show="showEditKelahiran" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     @keydown.escape.window="showEditKelahiran = false">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl flex flex-col overflow-hidden border border-gray-100"
         @click.outside="showEditKelahiran = false">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Edit Kelahiran</h2>
                <p class="text-[10px] font-mono text-gray-400 uppercase tracking-wider mt-0.5" x-text="'ID: LHR-' + editKelahiranData.lahir_id"></p>
            </div>
            <button type="button" @click="showEditKelahiran = false" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <div class="bg-gray-50 px-6 py-2 border-b border-gray-200">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">EDIT MODE — Data diisi dari kelahiran yang dipilih</p>
        </div>

        {{-- PUT form (fields only, no footer) --}}
        <form id="edit-kelahiran-form" method="POST" :action="'/reproduksi/' + editKelahiranData.kawin_id + '/kelahiran/' + editKelahiranData.lahir_id" class="overflow-y-auto flex-1">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-5">

                {{-- Info Perkawinan --}}
                <div class="p-3.5 bg-gray-50 border border-gray-200 rounded-xl">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Perkawinan Terkait</p>
                    <p class="text-sm font-semibold text-gray-700" x-text="editKelahiranData.pejantan_id + ' × ' + editKelahiranData.indukan_id"></p>
                </div>
                <p class="text-xs text-gray-400 italic text-center">Perkawinan terkait tidak dapat diubah.</p>

                {{-- Tanggal Kelahiran --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-700">Tanggal Kelahiran <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_kelahiran" :value="editKelahiranData.tanggal_kelahiran" required
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                </div>

                {{-- Jumlah & Bobot --}}
                <div class="grid grid-cols-3 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-700">Anak Hidup <span class="text-red-500">*</span></label>
                        <input type="number" name="jml_anak_hidup" min="0" :value="editKelahiranData.jml_anak_hidup" required
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-center font-bold"/>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-700">Anak Mati</label>
                        <input type="number" name="jml_anak_mati" min="0" :value="editKelahiranData.jml_anak_mati"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-center font-bold"/>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-700">Bobot Rata-rata (kg)</label>
                        <input type="number" name="bobot_rata_rata" step="0.01" min="0" :value="editKelahiranData.bobot_rata_rata"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-center"/>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-700">Catatan</label>
                    <textarea name="catatan" rows="2"
                              class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"
                              x-model="editKelahiranData.catatan"></textarea>
                </div>

            </div>
        </form>

        {{-- Footer (outside PUT form — delete form is a sibling, not nested) --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center flex-shrink-0">
            <form method="POST"
                  :action="'/reproduksi/' + editKelahiranData.kawin_id + '/kelahiran/' + editKelahiranData.lahir_id"
                  @submit.prevent="if(confirm('Hapus data kelahiran ini? Status perkawinan akan dikembalikan ke Bunting.')) $el.submit()">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-secondary border border-secondary/30 rounded-lg hover:bg-secondary/5 transition-colors">
                    <span class="material-symbols-outlined text-base">delete</span> Hapus
                </button>
            </form>
            <div class="flex gap-3">
                <button type="button" @click="showEditKelahiran = false"
                        class="px-5 py-2.5 border border-gray-300 text-gray-600 font-semibold text-sm rounded-lg hover:bg-gray-100 transition-colors">
                    Batal
                </button>
                <button type="submit" form="edit-kelahiran-form"
                        class="px-6 py-2.5 bg-primary text-white font-bold text-sm rounded-lg shadow-lg shadow-green-900/20 hover:bg-green-800 transition-all active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>
