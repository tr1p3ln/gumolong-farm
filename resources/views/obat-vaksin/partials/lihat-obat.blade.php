{{-- PARTIAL: obat-vaksin/partials/lihat-obat.blade.php --}}

<div id="modalLihatObat"
    class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">

    <div class="bg-white w-full max-w-md rounded-xl shadow-xl flex flex-col" style="max-height:90vh;"
         onclick="event.stopPropagation()">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h2 class="text-base font-bold text-gray-800" id="lihatHeaderNama">Detail Obat / Vaksin</h2>
                <p class="text-[11px] text-gray-400 mt-0.5 font-mono" id="lihatModalSubtitle">—</p>
            </div>
            <button onclick="closeLihatObatModal()"
                class="w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 transition text-xl leading-none">
                &times;
            </button>
        </div>

        {{-- Loading --}}
        <div id="lihatLoadingState" class="flex-1 flex items-center justify-center py-16">
            <div class="flex flex-col items-center gap-3">
                <svg class="animate-spin w-8 h-8 text-primary" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <p class="text-sm text-gray-400">Memuat data...</p>
            </div>
        </div>

        {{-- Content --}}
        <div id="lihatContent" class="hidden overflow-y-auto flex-1">

            {{-- Tab bar --}}
            <div class="px-6 pt-4 pb-0">
                <div class="inline-flex rounded-lg border border-gray-200 overflow-hidden text-xs font-medium">
                    <button type="button" onclick="lihatSwitchTab('detail')" id="lihatTabDetail"
                        class="px-4 py-2 bg-primary text-white transition">DETAIL VIEW</button>
                </div>
            </div>

            <div class="px-6 py-5 space-y-5">

                {{-- Row 1: ID Obat + Nama --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">ID Obat</p>
                        <p class="text-sm font-bold text-gray-900 font-mono" id="lihatId">—</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Nama</p>
                        <p class="text-sm font-semibold text-gray-900" id="lihatNama">—</p>
                    </div>
                </div>

                {{-- Row 2: Tipe + Status Stok --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Tipe</p>
                        <span id="lihatTipeBadge"
                            class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">—</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Status Stok</p>
                        <span id="lihatStatusBadge"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">—</span>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- Row 3: Stok Saat Ini + Stok Minimum --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Stok Saat Ini</p>
                        <p class="text-3xl font-bold text-green-600" id="lihatStok">—</p>
                        <p class="text-xs text-gray-500 mt-0.5" id="lihatSatuan">—</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Stok Minimum</p>
                        <p class="text-sm font-semibold text-gray-700 mt-1" id="lihatStokMin">—</p>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- Row 4: Tgl Expired + Interval Vaksinasi --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Tgl Expired</p>
                        <div class="flex items-center gap-1.5 mt-1">
                            <svg class="w-3.5 h-3.5 text-red-400 flex-shrink-0" id="lihatExpiredIcon" fill="currentColor" viewBox="0 0 20 20" style="display:none">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm font-semibold text-gray-800" id="lihatExpired">—</p>
                        </div>
                        <div id="lihatExpiredCountdownBox" class="hidden mt-1">
                            <p class="text-xs font-bold" id="lihatExpiredCountdown">—</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Interval Vaksinasi</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1" id="lihatInterval">—</p>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- Row 5: Harga Beli + Total Nilai Stok --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Harga Beli / Satuan</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1" id="lihatHarga">—</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Total Nilai Stok</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1" id="lihatTotalNilai">—</p>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- Keterangan / Dosis Anjuran --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Keterangan / Dosis Anjuran</p>
                    <div class="bg-gray-50 rounded-lg px-4 py-3">
                        <p class="text-sm text-gray-700 leading-relaxed" id="lihatKeterangan">—</p>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- Riwayat Penggunaan Terakhir --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Riwayat Penggunaan Terakhir</p>
                    <div id="lihatRiwayat">
                        {{-- Placeholder — akan diisi setelah fitur catat pemakaian selesai --}}
                        <div class="flex items-center gap-3 py-2">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-xs text-gray-400 italic">Belum ada riwayat pemakaian.</p>
                        </div>
                    </div>
                </div>

                {{-- Meta --}}
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Ditambahkan</p>
                    <p class="text-xs text-gray-500" id="lihatCreatedAt">—</p>
                </div>

            </div>
        </div>

        {{-- Footer --}}
        <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 flex-shrink-0 bg-gray-50/60 rounded-b-xl">
            <button type="button" onclick="closeLihatObatModal()"
                class="px-5 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-100 transition">
                Tutup
            </button>
            <button type="button" id="lihatToEditBtn"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:opacity-90 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Data
            </button>
        </div>

    </div>
</div>