{{-- Modal: Konfirmasi Kebuntingan --}}
<div x-show="showKonfirmasi" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     @keydown.escape.window="showKonfirmasi = false">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden border border-gray-100"
         @click.outside="showKonfirmasi = false">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Konfirmasi Kebuntingan</h2>
                <p class="text-[10px] font-mono text-gray-400 uppercase tracking-wider mt-0.5">FR-7.3 · Konfirmasi Hasil Kawin</p>
            </div>
            <button type="button" @click="showKonfirmasi = false" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <div class="overflow-y-auto flex-1">
            <form method="POST" :action="'/reproduksi/' + konfirmasiData.kawin_id + '/konfirmasi'">
                @csrf

                <div class="p-6 space-y-5">

                    {{-- Info Card --}}
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-3">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Data Perkawinan</p>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold">ID Kawin</p>
                                <p class="font-mono font-bold text-gray-800" x-text="'KWN-' + konfirmasiData.kawin_id"></p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold">Pasangan</p>
                                <p class="font-semibold text-gray-800" x-text="konfirmasiData.pejantan_id + ' × ' + konfirmasiData.indukan_id"></p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold">Tgl Kawin</p>
                                <p class="font-semibold text-gray-800" x-text="konfirmasiData.tanggal_perkawinan"></p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold">Estimasi HPL</p>
                                <p class="font-semibold text-primary" x-text="konfirmasiData.estimasi_lahir || '—'"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Hasil Konfirmasi --}}
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-gray-700">Hasil Konfirmasi <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            <label>
                                <input type="radio" name="hasil" value="bunting" required class="sr-only peer">
                                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all
                                            peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50 text-center">
                                    <span class="material-symbols-outlined text-3xl text-green-500 mb-1 block" style="font-variation-settings:'FILL' 1">pregnant_woman</span>
                                    <p class="font-bold text-sm text-gray-800">Bunting</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Indukan dinyatakan bunting</p>
                                </div>
                            </label>
                            <label>
                                <input type="radio" name="hasil" value="tidak_bunting" required class="sr-only peer">
                                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all
                                            peer-checked:border-gray-500 peer-checked:bg-gray-50 hover:bg-gray-50 text-center">
                                    <span class="material-symbols-outlined text-3xl text-gray-400 mb-1 block">cancel</span>
                                    <p class="font-bold text-sm text-gray-800">Tidak Bunting</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Perkawinan tidak berhasil</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Metode Konfirmasi --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-700">Metode Konfirmasi <span class="text-red-500">*</span></label>
                        <select name="metode_konfirmasi" required
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white">
                            <option value="">-- Pilih Metode --</option>
                            <option value="USG">USG (Ultrasonografi)</option>
                            <option value="observasi_fisik">Observasi Fisik</option>
                        </select>
                    </div>

                    {{-- Tanggal Konfirmasi --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-700">Tanggal Konfirmasi <span class="text-red-500">*</span></label>
                        <input type="date" name="tgl_konfirmasi" value="{{ today()->toDateString() }}" required
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                    </div>

                    {{-- Catatan --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-700">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <textarea name="catatan_konfirmasi" rows="3" placeholder="Tambahkan catatan hasil konfirmasi..."
                                  class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"></textarea>
                    </div>

                    {{-- Dikonfirmasi Oleh --}}
                    <div class="p-3.5 bg-blue-50 border border-blue-100 rounded-lg flex items-center gap-3">
                        <span class="material-symbols-outlined text-blue-500 text-lg" style="font-variation-settings:'FILL' 1">verified_user</span>
                        <div>
                            <p class="text-[10px] font-bold text-blue-600 uppercase">Dikonfirmasi Oleh</p>
                            <p class="text-sm font-semibold text-blue-900">{{ auth()->user()->nama ?? auth()->user()->name }}</p>
                        </div>
                        <input type="hidden" name="dikonfirmasi_oleh" value="{{ auth()->id() }}">
                    </div>

                    {{-- System Impact --}}
                    <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-lg flex gap-2.5">
                        <span class="material-symbols-outlined text-amber-500 text-base mt-0.5 shrink-0">info</span>
                        <p class="text-xs text-amber-800">
                            Memilih <strong>Bunting</strong> akan memindahkan record ke tab Kebuntingan &amp; HPL.
                            Memilih <strong>Tidak Bunting</strong> akan menutup siklus ini.
                        </p>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="showKonfirmasi = false"
                            class="px-5 py-2.5 border border-gray-300 text-gray-600 font-semibold text-sm rounded-lg hover:bg-gray-100 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 bg-primary text-white font-bold text-sm rounded-lg shadow-lg shadow-green-900/20 hover:bg-green-800 transition-all active:scale-95 flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">check_circle</span> Simpan Konfirmasi
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
