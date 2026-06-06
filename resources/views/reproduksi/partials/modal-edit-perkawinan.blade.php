{{-- Modal: Edit Perkawinan --}}
<div x-show="showEditPerkawinan" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     @keydown.escape.window="showEditPerkawinan = false">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl flex flex-col overflow-hidden border border-outline-variant/30"
         @click.outside="showEditPerkawinan = false">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-outline-variant/30 flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-on-surface">Edit Perkawinan</h2>
                <p class="text-[10px] font-mono text-outline uppercase tracking-wider mt-0.5" x-text="'ID: KWN-' + editData.kawin_id"></p>
            </div>
            <button type="button" @click="showEditPerkawinan = false" class="p-1.5 hover:bg-surface-container rounded-lg text-outline">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <div class="bg-surface-container-low px-6 py-2 border-b border-outline-variant/30">
            <p class="text-[10px] font-bold text-outline uppercase tracking-widest">EDIT MODE — Data terisi dari perkawinan yang dipilih</p>
        </div>

        {{-- PUT form --}}
        <form id="edit-perkawinan-form" method="POST" :action="'/reproduksi/' + editData.kawin_id" class="overflow-y-auto flex-1">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">

                {{-- Read-only info --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-surface-container-low border border-outline-variant rounded-xl flex flex-col items-center text-center">
                        <p class="text-[10px] font-bold text-outline uppercase tracking-wider mb-1">PEJANTAN</p>
                        <p class="text-xl font-black text-primary" x-text="editData.pejantan_id"></p>
                        <p class="text-xs text-on-surface-variant mt-0.5" x-text="editData.pejantan_ras ?? ''"></p>
                    </div>
                    <div class="p-4 bg-surface-container-low border border-outline-variant rounded-xl flex flex-col items-center text-center">
                        <p class="text-[10px] font-bold text-outline uppercase tracking-wider mb-1">INDUKAN</p>
                        <p class="text-xl font-black text-primary" x-text="editData.indukan_id"></p>
                        <p class="text-xs text-on-surface-variant mt-0.5" x-text="editData.indukan_ras ?? ''"></p>
                    </div>
                </div>
                <p class="text-xs text-center text-outline italic">Pasangan tidak dapat diubah — hanya tanggal, metode, dan status.</p>

                {{-- Tanggal & Metode --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-on-surface">Tanggal Kawin <span class="text-error">*</span></label>
                        <input type="date" name="tanggal_perkawinan" :value="editData.tanggal_perkawinan" required
                               class="w-full px-3.5 py-2.5 border border-outline-variant rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-on-surface">Metode <span class="text-error">*</span></label>
                        <select name="metode" required
                                class="w-full px-3.5 py-2.5 border border-outline-variant rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white">
                            <option value="alami"              :selected="editData.metode === 'alami'">Alami</option>
                            <option value="inseminasi_buatan"  :selected="editData.metode === 'inseminasi_buatan'">Inseminasi Buatan (IB)</option>
                        </select>
                    </div>
                </div>

                {{-- Status --}}
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-on-surface">Status <span class="text-error">*</span></label>
                    <div class="flex gap-2 flex-wrap">
                        @foreach(['menunggu_konfirmasi' => 'Menunggu Konfirmasi', 'bunting' => 'Bunting', 'tidak_bunting' => 'Tidak Bunting', 'lahir' => 'Lahir', 'gagal' => 'Gagal'] as $val => $lbl)
                        <label class="flex-1 min-w-[100px]">
                            <input type="radio" name="status" value="{{ $val }}" :checked="editData.status === '{{ $val }}'" class="sr-only peer">
                            <div class="text-center py-2 px-3 border border-outline-variant rounded-lg text-xs font-bold cursor-pointer
                                        peer-checked:bg-gray-900 peer-checked:text-white peer-checked:border-gray-900
                                        hover:bg-surface-container-low transition-colors text-on-surface-variant">
                                {{ $lbl }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

            </div>
        </form>

        {{-- Footer --}}
        <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant/30 flex justify-between items-center flex-shrink-0">
            <form method="POST" :action="'/reproduksi/' + editData.kawin_id"
                  @submit.prevent="if(confirm('Hapus data perkawinan ini?')) $el.submit()">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-secondary border border-secondary/30 rounded-lg hover:bg-secondary/5 transition-colors">
                    <span class="material-symbols-outlined text-base">delete</span> Hapus
                </button>
            </form>
            <div class="flex gap-3">
                <button type="button" @click="showEditPerkawinan = false"
                        class="px-5 py-2.5 border border-outline-variant text-on-surface-variant font-semibold text-sm rounded-lg hover:bg-surface-container transition-colors">
                    Batal
                </button>
                <button type="submit" form="edit-perkawinan-form"
                        class="px-6 py-2.5 bg-primary text-white font-bold text-sm rounded-lg shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>
