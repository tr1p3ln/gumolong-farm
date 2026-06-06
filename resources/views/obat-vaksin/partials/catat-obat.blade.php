{{-- PARTIAL: obat-vaksin/partials/catat-obat.blade.php --}}

<form id="formCatatObat" method="POST" action="{{ route('obat-vaksin.storePemakaian') }}"
    class="flex flex-col h-full min-h-0">
    @csrf
    <input type="hidden" name="obat_id" id="catat_obat_id">
    <input type="hidden" name="rekam_id" id="catat_rekam_id">

    {{-- SCROLLABLE BODY --}}
    <div class="flex-1 min-h-0 overflow-y-auto">

        {{-- SECTION HEADER --}}
        <div class="bg-gray-50 border-b px-6 py-2 shrink-0">
            <h3 style="font-size:10px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; color:#6b7280; margin:0;">
                Form Catat Pakai
            </h3>
        </div>

        <div class="px-6 py-5" style="display:flex; flex-direction:column; gap:1.25rem;">

            {{-- INFO BOX --}}
            <div style="border:1px solid #e5e7eb; background:#f9fafb; border-radius:6px; padding:12px 14px; display:flex; gap:10px; align-items:flex-start;">
                <svg style="width:16px;height:16px;color:#15803d;flex-shrink:0;margin-top:2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p style="font-size:13px;color:#6b7280;margin:0;line-height:1.55;">
                    Penggunaan obat wajib dikaitkan ke Rekam Medis domba untuk memastikan integritas data kesehatan digital estate Anda.
                </p>
            </div>

            {{-- OBAT / VAKSIN --}}
            <div>
                <label style="display:block;font-size:10px;font-weight:500;letter-spacing:0.08em;text-transform:uppercase;color:#6b7280;margin-bottom:6px;">
                    Obat / Vaksin
                </label>
                <input type="text" id="catat_nama_obat" readonly
                    style="width:100%;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:6px;padding:9px 12px;font-size:14px;color:#374151;box-sizing:border-box;cursor:default;">
                <p id="catat_tipe_sediaan" style="font-size:11px;font-style:italic;color:#9ca3af;margin:5px 0 0;">
                    Tipe sediaan: —
                </p>
            </div>

            {{-- REKAM MEDIS TERKAIT --}}
            <div>
                <label style="display:block;font-size:10px;font-weight:500;letter-spacing:0.08em;text-transform:uppercase;color:#6b7280;margin-bottom:6px;">
                    Rekam Medis Terkait
                    <span style="text-transform:none;color:#ef4444;font-size:11px;">*wajib</span>
                </label>

                {{-- Search row --}}
                <div style="display:flex;gap:8px;align-items:stretch;" id="rekamSearchRow">
                    <input type="text" id="rekamSearchInput"
                        placeholder="Ketik ID rekam medis atau ear tag..."
                        style="flex:1;min-width:0;border:1px solid #d1d5db;border-radius:6px;padding:9px 12px;font-size:14px;color:#111827;background:#fff;box-sizing:border-box;outline:none;"
                        onfocus="this.style.boxShadow='0 0 0 2px #16a34a40';this.style.borderColor='#16a34a';"
                        onblur="this.style.boxShadow='none';this.style.borderColor='#d1d5db';"
                        oninput="catatRekamSearch(this.value)">
                    <button type="button" onclick="catatRekamSearch(document.getElementById('rekamSearchInput').value)"
                        style="flex-shrink:0;padding:9px 16px;background:#15803d;border:none;border-radius:6px;color:white;font-size:13px;font-weight:500;cursor:pointer;display:flex;align-items:center;gap:6px;white-space:nowrap;"
                        onmouseover="this.style.background='#166534'" onmouseout="this.style.background='#15803d'">
                        <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Cari
                    </button>
                </div>

                {{-- Dropdown hasil search --}}
                <div id="rekamDropdown"
                    style="display:none;border:1px solid #e5e7eb;border-radius:6px;margin-top:4px;background:#fff;box-shadow:0 4px 12px rgba(0,0,0,0.08);max-height:180px;overflow-y:auto;">
                </div>

                {{-- Selected rekam card --}}
                <div id="rekamSelectedCard" style="display:none;margin-top:8px;border:1px solid #d1fae5;background:#f0fdf4;border-radius:6px;padding:10px 14px;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;flex:1;">
                            <div>
                                <p style="font-size:10px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#6b7280;margin:0 0 3px;">Rekam ID</p>
                                <p id="card_rekam_id" style="font-size:13px;font-weight:600;color:#111827;margin:0;font-family:monospace;"></p>
                            </div>
                            <div>
                                <p style="font-size:10px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#6b7280;margin:0 0 3px;">Ear Tag</p>
                                <p id="card_ear_tag" style="font-size:13px;font-weight:600;color:#15803d;margin:0;font-family:monospace;"></p>
                            </div>
                            <div>
                                <p style="font-size:10px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#6b7280;margin:0 0 3px;">Tgl Sakit</p>
                                <p id="card_tgl_sakit" style="font-size:13px;color:#374151;margin:0;"></p>
                            </div>
                            <div style="grid-column:span 2;">
                                <p style="font-size:10px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#6b7280;margin:0 0 3px;">Gejala</p>
                                <p id="card_gejala" style="font-size:12px;color:#6b7280;margin:0;line-height:1.4;"></p>
                            </div>
                            <div>
                                <p style="font-size:10px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#6b7280;margin:0 0 3px;">Status</p>
                                <span id="card_status" style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:20px;background:#dcfce7;color:#166534;"></span>
                            </div>
                        </div>
                        <button type="button" onclick="clearRekamSelection()"
                            style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:2px;margin-left:8px;flex-shrink:0;"
                            title="Ganti rekam medis">
                            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <p id="rekamHint" style="font-size:11px;color:#9ca3af;font-style:italic;margin:5px 0 0;">
                    Cari menggunakan ID rekam medis (angka) atau ear tag domba (contoh: SFT-001)
                </p>
            </div>

            {{-- EAR TAG (auto-fill dari rekam medis) --}}
            <div>
                <label style="display:block;font-size:10px;font-weight:500;letter-spacing:0.08em;text-transform:uppercase;color:#6b7280;margin-bottom:6px;">
                    Domba (Ear Tag)
                </label>
                <input type="text" id="catat_ear_tag" readonly
                    style="width:100%;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:6px;padding:9px 12px;font-size:14px;color:#374151;box-sizing:border-box;cursor:default;"
                    placeholder="Terisi otomatis dari rekam medis">
            </div>

            {{-- TANGGAL + DOSIS + CARA (2 kolom) --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label style="display:block;font-size:10px;font-weight:500;letter-spacing:0.08em;text-transform:uppercase;color:#6b7280;margin-bottom:6px;">
                        Tanggal Pemberian
                        <span style="text-transform:none;color:#ef4444;font-size:11px;">*wajib</span>
                    </label>
                    <input type="date" name="tanggal_pemberian"
                        style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:9px 12px;font-size:14px;color:#111827;background:#fff;box-sizing:border-box;outline:none;"
                        onfocus="this.style.boxShadow='0 0 0 2px #16a34a40';this.style.borderColor='#16a34a';"
                        onblur="this.style.boxShadow='none';this.style.borderColor='#d1d5db';">
                </div>
                <div>
                    <label style="display:block;font-size:10px;font-weight:500;letter-spacing:0.08em;text-transform:uppercase;color:#6b7280;margin-bottom:6px;">
                        Jumlah Dosis
                        <span style="text-transform:none;color:#ef4444;font-size:11px;">*wajib</span>
                    </label>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <input type="number" step="0.1" name="jumlah_dosis"
                            style="flex:1;min-width:0;border:1px solid #d1d5db;border-radius:6px;padding:9px 12px;font-size:14px;color:#111827;background:#fff;box-sizing:border-box;outline:none;"
                            onfocus="this.style.boxShadow='0 0 0 2px #16a34a40';this.style.borderColor='#16a34a';"
                            onblur="this.style.boxShadow='none';this.style.borderColor='#d1d5db';">
                        <span id="catat_satuan_label" style="font-size:13px;color:#6b7280;flex-shrink:0;">ml</span>
                    </div>
                </div>
            </div>

            <div>
                <label style="display:block;font-size:10px;font-weight:500;letter-spacing:0.08em;text-transform:uppercase;color:#6b7280;margin-bottom:6px;">
                    Cara Pemberian
                </label>
                <input type="text" name="cara_pemberian"
                    placeholder="e.g. Intramuscular"
                    style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:9px 12px;font-size:14px;color:#111827;background:#fff;box-sizing:border-box;outline:none;"
                    onfocus="this.style.boxShadow='0 0 0 2px #16a34a40';this.style.borderColor='#16a34a';"
                    onblur="this.style.boxShadow='none';this.style.borderColor='#d1d5db';">
            </div>

            {{-- CATATAN --}}
            <div>
                <label style="display:block;font-size:10px;font-weight:500;letter-spacing:0.08em;text-transform:uppercase;color:#6b7280;margin-bottom:6px;">
                    Catatan Pemberian
                </label>
                <textarea rows="3" name="catatan"
                    placeholder="Tambahkan observasi singkat pasca pemberian..."
                    style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:9px 12px;font-size:14px;color:#111827;background:#fff;resize:none;box-sizing:border-box;font-family:inherit;line-height:1.5;outline:none;"
                    onfocus="this.style.boxShadow='0 0 0 2px #16a34a40';this.style.borderColor='#16a34a';"
                    onblur="this.style.boxShadow='none';this.style.borderColor='#d1d5db';"></textarea>
            </div>

        </div>
    </div>

    {{-- FOOTER (fixed di bawah, tidak ikut scroll) --}}
    <div class="border-t bg-white shrink-0"
        style="display:flex;justify-content:flex-end;gap:10px;padding:14px 24px;">
        <button type="button" onclick="closeCatatPakaiModal()"
            style="padding:9px 24px;background:transparent;border:1px solid #d1d5db;border-radius:6px;color:#4b5563;font-size:13px;font-weight:500;letter-spacing:0.04em;cursor:pointer;"
            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
            CANCEL
        </button>
        <button type="submit"
            style="padding:9px 24px;background:#1e293b;border:none;border-radius:6px;color:white;font-size:13px;font-weight:500;letter-spacing:0.04em;cursor:pointer;"
            onmouseover="this.style.background='#0f172a'" onmouseout="this.style.background='#1e293b'">
            SIMPAN PENGGUNAAN
        </button>
    </div>

</form>

