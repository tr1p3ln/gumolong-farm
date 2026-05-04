{{-- Modal: Edit Vaksinasi --}}
<div x-show="showEditVaksinasi" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     @keydown.escape.window="showEditVaksinasi = false">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl flex flex-col overflow-hidden border border-gray-100"
         @click.outside="showEditVaksinasi = false">

        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-start flex-shrink-0">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Edit Vaksinasi</h2>
                <p class="text-[10px] font-mono text-gray-400 uppercase tracking-wider mt-0.5"
                   x-text="'ID: VK-' + editVaksinasiData.vaksin_id + ' · ' + editVaksinasiData.ear_tag_id"></p>
            </div>
            <button type="button" @click="showEditVaksinasi = false" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <form id="edit-vaksinasi-form" method="POST" :action="'/kesehatan/vaksinasi/' + editVaksinasiData.vaksin_id" class="overflow-y-auto flex-1">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-5">

                {{-- Info --}}
                <div class="p-3.5 bg-gray-50 border border-gray-200 rounded-xl">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Domba & Vaksin</p>
                    <p class="text-sm font-bold text-primary" x-text="editVaksinasiData.ear_tag_id"></p>
                    <p class="text-xs text-gray-500" x-text="(editVaksinasiData.nama_domba ?? '') + ' · ' + (editVaksinasiData.nama_obat ?? '')"></p>
                </div>
                <p class="text-xs text-gray-400 italic text-center">Domba dan vaksin tidak dapat diubah.</p>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-700">Tanggal Vaksinasi <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_vaksinasi" :value="editVaksinasiData.tanggal_vaksinasi" required
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-700">Jadwal Berikutnya</label>
                    <input type="date" name="tanggal_berikutnya" :value="editVaksinasiData.tanggal_berikutnya"
                           class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-700">Catatan</label>
                    <textarea name="catatan" rows="2"
                              class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"
                              x-model="editVaksinasiData.catatan"></textarea>
                </div>

            </div>
        </form>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center flex-shrink-0">
            <form method="POST" :action="'/kesehatan/vaksinasi/' + editVaksinasiData.vaksin_id"
                  @submit.prevent="if(confirm('Hapus data vaksinasi ini?')) $el.submit()">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-error border border-error/30 rounded-lg hover:bg-error/5 transition-colors">
                    <span class="material-symbols-outlined text-base">delete</span> Hapus
                </button>
            </form>
            <div class="flex gap-3">
                <button type="button" @click="showEditVaksinasi = false"
                        class="px-5 py-2.5 border border-gray-300 text-gray-600 font-semibold text-sm rounded-lg hover:bg-gray-100 transition-colors">
                    Batal
                </button>
                <button type="submit" form="edit-vaksinasi-form"
                        class="px-6 py-2.5 bg-primary text-white font-bold text-sm rounded-lg shadow-lg shadow-green-900/20 hover:bg-green-800 transition-all active:scale-95">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
