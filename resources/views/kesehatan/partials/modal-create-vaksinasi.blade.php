{{-- Modal: Tambah Vaksinasi --}}
<div x-show="showCreateVaksinasi" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     @keydown.escape.window="showCreateVaksinasi = false">

    <div class="bg-surface w-full max-w-2xl max-h-[92vh] shadow-2xl rounded-xl overflow-hidden flex flex-col border border-surface-container"
         @click.outside="showCreateVaksinasi = false">

        <header class="px-8 py-6 bg-white border-b border-surface-container flex justify-between items-start flex-shrink-0">
            <div class="space-y-1">
                <h2 class="text-xl font-bold text-on-surface">Catat Vaksinasi</h2>
            </div>
            <button type="button" @click="showCreateVaksinasi = false"
                    class="text-outline hover:text-on-surface transition-colors p-1 rounded-lg hover:bg-surface-container-low">
                <span class="material-symbols-outlined text-2xl">close</span>
            </button>
        </header>

        <form method="POST" action="{{ route('kesehatan.vaksinasi.store') }}" class="overflow-y-auto flex-1">
            @csrf
            <main class="p-8 space-y-6">

                {{-- Domba --}}
                <section class="space-y-3">
                    <label class="block text-xs font-semibold text-on-surface">
                        Domba <span class="text-error ml-1">*</span>
                    </label>
                    <div class="relative" x-show="!selectedDombaVk">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-xl pointer-events-none">search</span>
                        <input type="text" x-model="dombaVkSearch"
                               placeholder="Cari Ear Tag atau nama domba..."
                               class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none text-sm"/>
                    </div>
                    <div x-show="dombaVkSearch.length > 0 && !selectedDombaVk"
                         class="border border-outline-variant rounded-lg divide-y divide-surface-container max-h-44 overflow-y-auto bg-white shadow-lg">
                        <template x-for="d in filteredDombaVk.slice(0,8)" :key="d.ear_tag_id">
                            <div @click="selectedDombaVk = d; dombaVkSearch = d.ear_tag_id"
                                 class="px-4 py-2.5 flex items-center gap-3 hover:bg-surface-container-low cursor-pointer transition-colors">
                                <span class="font-bold text-primary text-sm" x-text="d.ear_tag_id"></span>
                                <span class="text-xs text-outline" x-text="d.nama ? '· ' + d.nama : ''"></span>
                                <span class="ml-auto text-xs bg-surface-container px-2 py-0.5 rounded" x-text="d.ras"></span>
                                <span class="text-xs px-2 py-0.5 rounded font-bold bg-secondary-container text-on-secondary-container" x-text="d.kategori"></span>
                            </div>
                        </template>
                        <div x-show="filteredDombaVk.length === 0" class="px-4 py-3 text-sm text-outline text-center italic">
                            Tidak ada domba ditemukan
                        </div>
                    </div>
                    <div x-show="selectedDombaVk" class="flex items-center gap-4 p-4 bg-white border border-primary/20 rounded-xl shadow-sm">
                        <div class="flex-1">
                            <p class="text-[10px] font-bold text-outline uppercase mb-1">Ear Tag</p>
                            <p class="font-bold text-primary text-lg" x-text="selectedDombaVk?.ear_tag_id"></p>
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] font-bold text-outline uppercase mb-1">Nama</p>
                            <p class="text-on-surface font-medium text-sm" x-text="selectedDombaVk?.nama || '—'"></p>
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] font-bold text-outline uppercase mb-1">Kategori</p>
                            <span class="px-3 py-1 bg-secondary-container text-on-secondary-container text-[11px] font-bold rounded-full"
                                  x-text="selectedDombaVk?.kategori"></span>
                        </div>
                        <button type="button" @click="selectedDombaVk = null; dombaVkSearch = ''"
                                class="text-outline hover:text-error transition-colors">
                            <span class="material-symbols-outlined text-lg">close</span>
                        </button>
                    </div>
                    <input type="hidden" name="ear_tag_id" :value="selectedDombaVk?.ear_tag_id">
                </section>

                {{-- Vaksin --}}
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-on-surface">Vaksin / Obat <span class="text-error">*</span></label>
                    <select name="obat_id" required
                            class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none bg-white text-sm">
                        <option value="">— Pilih Vaksin —</option>
                        @foreach($vaksinList as $v)
                        <option value="{{ $v->obat_id }}">{{ $v->nama_obat }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tanggal --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-on-surface">Tanggal Vaksinasi <span class="text-error">*</span></label>
                        <input type="date" name="tanggal_vaksinasi" required
                               class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none text-sm bg-white"/>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-on-surface">Jadwal Berikutnya <span class="text-xs font-normal text-outline">(opsional)</span></label>
                        <input type="date" name="tanggal_berikutnya"
                               class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none text-sm bg-white"/>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-on-surface">Catatan <span class="text-xs font-normal text-outline">(opsional)</span></label>
                    <textarea name="catatan" rows="2" placeholder="Catatan tambahan..."
                              class="w-full px-4 py-2.5 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none text-sm bg-white resize-none"></textarea>
                </div>

            </main>

            <footer class="px-8 py-5 bg-white border-t border-surface-container flex justify-end items-center gap-4 flex-shrink-0">
                <button type="button" @click="showCreateVaksinasi = false"
                        class="px-6 py-2.5 border border-outline text-on-surface-variant font-bold text-sm rounded-lg hover:bg-surface-container transition-all">
                    Batal
                </button>
                <button type="submit"
                        class="px-8 py-2.5 bg-primary text-white font-bold text-sm rounded-lg shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all active:scale-95 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">vaccines</span>
                    Simpan Vaksinasi
                </button>
            </footer>
        </form>
    </div>
</div>
