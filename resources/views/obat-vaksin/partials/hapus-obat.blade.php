{{-- PARTIAL: obat-vaksin/partials/hapus-obat.blade.php --}}

<div id="modalHapusObat"
    class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">

    <div class="relative z-10 w-full max-w-sm bg-white rounded-xl shadow-2xl border border-gray-100"
         onclick="event.stopPropagation()">

        <div class="px-6 pt-6 pb-4">
            {{-- Icon --}}
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <h3 class="text-base font-bold text-gray-900 mb-1">Hapus Obat / Vaksin</h3>
            <p class="text-sm text-gray-600">
                Yakin ingin menghapus
                <span class="font-semibold text-gray-900" id="hapusNamaObat">—</span>
                <span class="font-mono text-xs text-gray-400" id="hapusIdObat"></span>?
                <br>
                <span class="text-xs text-gray-400 mt-1 block">Tindakan ini tidak dapat dibatalkan.</span>
            </p>
        </div>

        <div class="px-6 pb-5 flex justify-end gap-3">
            <button type="button" onclick="closeHapusObatModal()"
                class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                Batal
            </button>

            <form id="formHapusObat" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" id="hapusSubmitBtn"
                    class="px-4 py-2.5 bg-accent hover:opacity-90 text-white text-sm font-semibold rounded-lg transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Ya, Hapus
                </button>
            </form>
        </div>

    </div>
</div>