{{-- Modal: Edit Vaksinasi --}}
<div x-show="showEditVaksinasi" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     @keydown.escape.window="showEditVaksinasi = false">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl flex flex-col overflow-hidden border border-outline-variant/30"
         @click.outside="showEditVaksinasi = false">

        <div class="px-6 py-5 border-b border-outline-variant/30 flex justify-between items-start flex-shrink-0">
            <div>
                <h2 class="text-xl font-bold text-on-surface">Edit Vaksinasi</h2>
                <p class="text-[10px] font-mono text-outline uppercase tracking-wider mt-0.5"
                   x-text="'ID: VK-' + editVaksinasiData.vaksin_id + ' · ' + editVaksinasiData.ear_tag_id"></p>
            </div>
            <button type="button" @click="showEditVaksinasi = false" class="p-1.5 hover:bg-surface-container rounded-lg text-outline">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <form id="edit-vaksinasi-form" method="POST" :action="'/kesehatan/vaksinasi/' + editVaksinasiData.vaksin_id" class="overflow-y-auto flex-1">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-5">

                {{-- Info --}}
                <div class="p-3.5 bg-surface-container-low border border-outline-variant rounded-xl">
                    <p class="text-[10px] font-bold text-outline uppercase mb-1">Domba & Vaksin</p>
                    <p class="text-sm font-bold text-primary" x-text="editVaksinasiData.ear_tag_id"></p>
                    <p class="text-xs text-on-surface-variant" x-text="(editVaksinasiData.nama_domba ?? '') + ' · ' + (editVaksinasiData.nama_obat ?? '')"></p>
                </div>
                <p class="text-xs text-outline italic text-center">Domba dan vaksin tidak dapat diubah.</p>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-on-surface">Tanggal Vaksinasi <span class="text-error">*</span></label>
                    <input type="date" name="tanggal_vaksinasi" :value="editVaksinasiData.tanggal_vaksinasi" required
                           class="w-full px-3.5 py-2.5 border border-outline-variant rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-on-surface">Jadwal Berikutnya</label>
                    <input type="date" name="tanggal_berikutnya" :value="editVaksinasiData.tanggal_berikutnya"
                           class="w-full px-3.5 py-2.5 border border-outline-variant rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-on-surface">Catatan</label>
                    <textarea name="catatan" rows="2"
                              class="w-full px-3.5 py-2.5 border border-outline-variant rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"
                              x-model="editVaksinasiData.catatan"></textarea>
                </div>

            </div>
        </form>

        <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant/30 flex justify-between items-center flex-shrink-0">
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
                        class="px-5 py-2.5 border border-outline-variant text-on-surface-variant font-semibold text-sm rounded-lg hover:bg-surface-container transition-colors">
                    Batal
                </button>
                <button type="submit" form="edit-vaksinasi-form"
                        class="px-6 py-2.5 bg-primary text-white font-bold text-sm rounded-lg shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all active:scale-95">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
