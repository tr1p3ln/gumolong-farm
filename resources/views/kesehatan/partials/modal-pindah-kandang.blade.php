{{-- Modal: Pindah Kandang (dari Karantina) --}}
<div x-show="showPindahKandang" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     @keydown.escape.window="showPindahKandang = false">

    <div class="w-full max-w-2xl bg-white rounded-xl shadow-2xl overflow-hidden border border-outline-variant max-h-[92vh] flex flex-col"
         @click.outside="showPindahKandang = false">

        {{-- Header --}}
        <header class="px-8 pt-8 pb-0 relative flex-shrink-0">
            <button type="button" @click="showPindahKandang = false"
                    class="absolute top-8 right-8 text-outline hover:text-on-surface transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
            <div class="space-y-1">
                <h2 class="text-xl font-bold text-on-surface">
                    Pindah Kandang
                </h2>
            </div>
            <div class="mt-6 border-b border-outline-variant/30"></div>
        </header>

        <form id="pindah-kandang-form" method="POST" :action="'/kesehatan/karantina/' + pindahData.ear_tag_id + '/pindah'"
              class="overflow-y-auto flex-1">
            @csrf
            @method('PATCH')

            <div class="px-8 py-6 space-y-6">

                {{-- Section 1: Sheep Info --}}
                <section class="p-5 bg-surface-container-low border border-outline-variant rounded-lg">
                    <label class="block text-xs font-semibold text-on-surface-variant mb-3 uppercase tracking-wider">Domba yang dipindahkan:</label>
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-secondary-container flex items-center justify-center">
                            <span class="material-symbols-outlined text-on-secondary-container">pets</span>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-on-surface"
                                x-text="pindahData.ear_tag_id + (pindahData.nama ? ' — ' + pindahData.nama : '')"></h3>
                            <div class="mt-1 space-y-0.5">
                                <p class="text-sm text-on-surface-variant flex items-center gap-1.5"
                                   x-show="pindahData.tanggal_masuk">
                                    <span class="material-symbols-outlined text-xs">calendar_today</span>
                                    <span x-text="'Karantina sejak: ' + (pindahData.tanggal_masuk ?? '') + (pindahData.durasi_hari ? ' (' + pindahData.durasi_hari + ' hari)' : '')"></span>
                                </p>
                                <p class="text-sm text-on-surface-variant flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-xs">meeting_room</span>
                                    <span x-text="'Kandang saat ini: ' + (pindahData.nama_kandang ?? '—')"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Section 2: Kandang Tujuan --}}
                <section class="space-y-2">
                    <div class="flex items-baseline gap-2">
                        <label class="text-xs font-semibold text-on-surface">Kandang Tujuan <span class="text-error">*</span></label>
                    </div>
                    <select name="kandang_id" required
                            class="w-full h-10 pl-4 pr-4 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all cursor-pointer text-sm text-on-surface">
                        <option value="">— Pilih Kandang Tujuan —</option>
                        @foreach($kandangList as $k)
                        <option value="{{ $k->kandang_id }}">
                            {{ $k->nama_kandang }} ({{ $k->tipe }}, kap. {{ $k->kapasitas }})
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-outline italic">Jika dipindah ke kandang non-isolasi, status karantina domba akan dicabut secara otomatis.</p>
                </section>

                {{-- Section 3: Status Rekam Medis --}}
                <section class="space-y-2">
                    <label class="text-xs font-semibold text-on-surface block">Update Status Rekam Medis</label>
                    <select name="status_rekam"
                            class="w-full h-10 pl-4 pr-4 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm text-on-surface">
                        <option value="">— Tidak diubah —</option>
                        <option value="sembuh">Sembuh — pindah ke kandang utama</option>
                        <option value="dalam_perawatan">Dalam Perawatan — pemantauan lanjutan</option>
                    </select>
                    <p class="text-xs text-outline italic">Opsional — memperbarui status rekam medis terakhir domba ini.</p>
                </section>

                {{-- Section 4: Tanggal Pindah --}}
                <section class="space-y-2">
                    <div class="flex items-baseline gap-2">
                        <label class="text-xs font-semibold text-on-surface">Tanggal Pindah</label>
                        <span class="text-[10px] text-error font-medium italic">(wajib)</span>
                    </div>
                    <input type="date" name="tanggal_pindah" required
                           class="w-full h-10 px-4 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm text-on-surface"/>
                </section>

                {{-- Section 5: Catatan --}}
                <section class="space-y-2">
                    <div class="flex items-baseline gap-2">
                        <label class="text-xs font-semibold text-on-surface">Catatan</label>
                        <span class="text-[10px] text-on-surface-variant font-medium">(opsional)</span>
                    </div>
                    <textarea name="catatan" rows="3"
                              class="w-full px-3.5 py-2.5 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none text-sm text-on-surface placeholder:text-outline"
                              placeholder="Tambahkan observasi kondisi terakhir domba..."></textarea>
                </section>

            </div>
        </form>

        {{-- Footer --}}
        <footer class="px-8 py-5 bg-surface-container-low border-t border-outline-variant/30 flex items-center justify-end gap-3 flex-shrink-0">
            <button type="button" @click="showPindahKandang = false"
                    class="px-6 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant font-semibold text-sm hover:bg-surface-container active:scale-95 transition-all">
                Batal
            </button>
            <button type="submit" form="pindah-kandang-form"
                    class="px-6 py-2.5 rounded-lg bg-primary text-white font-bold text-sm shadow-lg shadow-primary/20 hover:opacity-90 active:scale-95 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Konfirmasi Pindah
            </button>
        </footer>

    </div>
</div>
