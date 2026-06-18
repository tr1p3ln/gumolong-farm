{{--
    PARTIAL: resources/views/pakan-individual/partials/catat-pakan.blade.php
--}}

<div id="modalCatatPakan"
    class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-xl shadow-xl flex flex-col" style="max-height:95vh; min-height:500px;">

        {{-- ══ HEADER ══ --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h2 class="text-base font-bold text-gray-800">Catat Pemberian Pakan</h2>
            </div>
            <button type="button" onclick="closeCatatPakan()"
                class="w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 transition text-xl leading-none">&times;</button>
        </div>

        {{-- ══ BODY ══ --}}
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4 pb-6">

            {{-- STATE: PREFILLED banner --}}
            <div id="cpBannerDomba" class="hidden items-center gap-2 bg-green-50 border border-green-200 rounded-lg px-4 py-2.5">
                <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-green-800">Domba dipilih otomatis dari baris tabel — <span id="cpBannerEarTag" class="font-bold"></span></p>
            </div>

            {{-- ── Info Domba Card ── --}}
            <div id="cpDombaCard" class="hidden border border-gray-200 rounded-lg p-4">
                <div class="flex items-start gap-4">
                    {{-- Avatar --}}
                    <div class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4C10.9 4 10 4.9 10 6s.9 2 2 2 2-.9 2-2-.9-2-2-2zM6.5 8C5.1 8 4 9.1 4 10.5S5.1 13 6.5 13H9v5h6v-5h2.5C18.9 13 20 11.9 20 10.5S18.9 8 17.5 8c-.7 0-1.4.3-1.9.8C14.9 8.3 13.5 8 12 8s-2.9.3-3.6.8C7.9 8.3 7.2 8 6.5 8z"/>
                        </svg>
                    </div>
                    {{-- Info --}}
                    <div class="flex-1 grid grid-cols-3 gap-3">
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-0.5">Ear Tag/Kode</p>
                            <p id="cpDombaEarTag" class="text-sm font-bold text-gray-900 font-mono">—</p>
                            <p id="cpDombaId" class="text-[10px] text-gray-400">—</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-0.5">Kategori/Ras</p>
                            <p id="cpDombaKategori" class="text-sm font-semibold text-gray-800">—</p>
                            <p id="cpDombaRas" class="text-[10px] text-gray-400">—</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-0.5">Berat Terakhir</p>
                            <p id="cpDombaBerat" class="text-sm font-bold text-gray-900">—</p>
                            <p class="text-[10px] text-gray-400">terbaru</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Pilih Domba (kalau tidak prefilled) ── --}}
            <div id="cpSelectDombaWrap">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                    Domba (Ear Tag) <span class="text-red-500 normal-case font-normal">*wajib</span>
                </label>
                <select id="cp_ear_tag"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary"
                    onchange="cpOnDombaChange(this)">
                    <option value="">Pilih domba...</option>
                    @foreach($dombaList as $d)
                        <option value="{{ $d->ear_tag_id }}"
                            data-nama="{{ $d->nama }}"
                            data-kategori="{{ $d->kategori }}">
                            {{ $d->ear_tag_id }} — {{ $d->nama }} ({{ ucfirst($d->kategori) }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- ── Jenis Pakan ── --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                    Jenis Pakan <span class="text-red-500 normal-case font-normal">*wajib</span>
                </label>
                <select id="cp_pakan_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary"
                    onchange="cpUpdateStokBadge(this)">
                    <option value="">Pilih pakan...</option>
                    @foreach($pakanList as $p)
                        <option value="{{ $p->pakan_id }}"
                            data-nama="{{ $p->nama_pakan }}"
                            data-stok="{{ $p->jumlah_stok ?? 0 }}"
                            data-jenis="{{ $p->jenis }}">
                            {{ $p->nama_pakan }} — Stok: {{ number_format($p->jumlah_stok ?? 0, 0) }} kg
                        </option>
                    @endforeach
                </select>
                {{-- Stok badge --}}
                <div id="cpStokBadge" class="hidden mt-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span id="cpStokText" class="text-xs text-green-700 font-semibold">—</span>
                </div>
            </div>

            {{-- ── Sesi Pemberian ── --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                    Sesi Pemberian <span class="text-red-500 normal-case font-normal">*wajib</span>
                </label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(['pagi' => 'Pagi', 'sore' => 'Sore'] as $val => $label)
                        <button type="button"
                            onclick="cpSelectSesi('{{ $val }}')"
                            data-sesi="{{ $val }}"
                            class="cp-sesi-btn py-2.5 border-2 border-gray-200 rounded-lg text-sm font-semibold text-gray-600 hover:border-primary/40 transition">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" id="cp_sesi">
            </div>

            {{-- ── Jumlah + Tanggal ── --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                        Jumlah (gram) <span class="text-red-500 normal-case font-normal">*wajib</span>
                    </label>
                    <input type="number" id="cp_jumlah" min="1" step="1" placeholder="Contoh: 450"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        oninput="cpCekStok(); cpUpdatePreview()">
                    <div id="cpWarningStok" class="hidden mt-1.5 flex items-center gap-1 text-xs text-red-600">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        Melebihi stok!
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                        Tanggal <span class="text-red-500 normal-case font-normal">*wajib</span>
                    </label>
                    <input type="date" id="cp_tanggal" value="{{ date('Y-m-d') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>

            {{-- ── Rekomendasi Pakan ── --}}
            <div id="cpRekomenSection" class="hidden border border-gray-100 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-2.5 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-600">Rekomendasi Pakan</span>
                    <span class="text-[10px] bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full">Ideal: 1.000 g/hari</span>
                </div>
                {{-- Progress bar komposisi --}}
                <div class="px-4 py-3">
                    <div id="cpKomposisiBar" class="w-full rounded-full h-3 overflow-hidden flex mb-3 bg-gray-100"></div>
                    <div id="cpKomposisiLegend" class="grid grid-cols-2 gap-x-4 gap-y-1"></div>
                </div>
            </div>

            {{-- ── Ringkasan: Total Pakan, Kenaikan BB, FCR ── --}}
            <div id="cpSummarySection" class="hidden">
                <div class="grid grid-cols-3 gap-3">
                    <div class="border border-gray-200 rounded-lg p-3 text-center">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-1">Total Pakan 30hr</p>
                        <p id="cpSumPakan" class="text-lg font-bold text-gray-900">—</p>
                        <p class="text-[10px] text-gray-400">kg</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3 text-center">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-1">Kenaikan BB</p>
                        <p id="cpSumBB" class="text-lg font-bold text-gray-900">—</p>
                        <p class="text-[10px] text-gray-400">kg</p>
                    </div>
                    <div id="cpSumFcrCard" class="border border-gray-200 rounded-lg p-3 text-center">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-1">FCR Terhitung</p>
                        <p id="cpSumFcr" class="text-lg font-bold text-gray-900">—</p>
                        <span id="cpSumFcrBadge" class="text-[10px] font-semibold px-1.5 py-0.5 rounded">—</span>
                    </div>
                </div>
            </div>

            {{-- ── Update Stok Preview ── --}}
            <div id="cpStokPreview" class="hidden border border-gray-100 rounded-lg px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Update Stok Pakan</p>
                            <p id="cpStokPreviewText" class="text-xs text-gray-700 mt-0.5">—</p>
                        </div>
                    </div>
                    <span id="cpStokPreviewBerkurang" class="text-xs font-bold text-red-600">—</span>
                </div>
            </div>

            {{-- ── Dicatat Oleh ── --}}
            <div class="border border-gray-100 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Dicatat Oleh</p>
                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name ?? 'Admin Farm' }}</p>
                </div>
            </div>

        </div>

        {{-- ══ FOOTER ══ --}}
        <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 flex-shrink-0 bg-gray-50/50">
            <button type="button" onclick="closeCatatPakan()"
                class="px-5 py-2.5 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                Batal
            </button>
            <button type="button" id="cpBtnSimpan" onclick="cpSubmit()"
            class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition shadow-sm flex items-center gap-2"
            style="background-color: #16a34a; color: white;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span id="cpBtnText">Simpan Pemberian Pakan</span>
            </button>
        </div>

    </div>
</div>

<script>
const cpWarnaMap = {
    rumput: '#639922', konsentrat: '#378ADD',
    silase: '#BA7517', dedak: '#888780', ampas_tahu: '#888780'
};

// ── Buka modal ────────────────────────────────────────────────────────
window.openCatatPakan = function(dombaData = null) {
    cpReset();

    if (dombaData) {
        // Pre-fill dari baris tabel
        document.getElementById('cp_ear_tag').value = dombaData.ear_tag_id;
        document.getElementById('cpBannerEarTag').textContent = dombaData.ear_tag_id;
        document.getElementById('cpBannerDomba').classList.remove('hidden');
        document.getElementById('cpBannerDomba').classList.add('flex');
        document.getElementById('cpSelectDombaWrap').classList.add('hidden');

        // Isi card domba
        cpFillDombaCard(dombaData);

        // Load stats FCR
        cpLoadStats(dombaData.ear_tag_id);
    }

    document.getElementById('modalCatatPakan').classList.remove('hidden');
    document.getElementById('modalCatatPakan').classList.add('flex');
    document.body.style.overflow = 'hidden';
};

// ── Tutup modal ───────────────────────────────────────────────────────
window.closeCatatPakan = function() {
    document.getElementById('modalCatatPakan').classList.add('hidden');
    document.getElementById('modalCatatPakan').classList.remove('flex');
    document.body.style.overflow = '';
    cpReset();
};

// ── Isi card info domba ───────────────────────────────────────────────
function cpFillDombaCard(data) {
    document.getElementById('cpDombaCard').classList.remove('hidden');
    document.getElementById('cpDombaEarTag').textContent  = data.ear_tag_id ?? '—';
    document.getElementById('cpDombaId').textContent      = 'ET-' + (data.ear_tag_id ?? '—');
    document.getElementById('cpDombaKategori').textContent = data.kategori
        ? data.kategori.charAt(0).toUpperCase() + data.kategori.slice(1) : '—';
    document.getElementById('cpDombaRas').textContent     = data.ras ?? '—';
    document.getElementById('cpDombaBerat').textContent   = data.berat_kg
        ? parseFloat(data.berat_kg).toFixed(1) + ' kg' : '—';
}

// ── Load stats FCR dari endpoint ──────────────────────────────────────
function cpLoadStats(earTagId) {
    fetch(`/pakan-individual/${earTagId}/stats`)
        .then(r => r.json())
        .then(data => {
            // Summary cards
            document.getElementById('cpSumPakan').textContent = data.total_pakan_30hr ?? '—';
            document.getElementById('cpSumBB').textContent    = data.kenaikan_bb ?? '—';

            const fcr    = data.fcr;
            const status = fcr === null ? null : fcr <= 6 ? 'efisien' : fcr <= 8 ? 'normal' : 'boros';
            const fcrColors = { efisien: 'bg-green-100 text-green-700', normal: 'bg-blue-100 text-blue-700', boros: 'bg-red-100 text-red-700' };

            document.getElementById('cpSumFcr').textContent       = fcr !== null ? fcr.toFixed(2) : '—';
            document.getElementById('cpSumFcrBadge').textContent  = status ? status.charAt(0).toUpperCase() + status.slice(1) : '—';
            document.getElementById('cpSumFcrBadge').className    = `text-[10px] font-semibold px-1.5 py-0.5 rounded ${fcrColors[status] ?? 'bg-gray-100 text-gray-500'}`;
            document.getElementById('cpSummarySection').classList.remove('hidden');

            // Rekomendasi komposisi
            if (data.rekomendasi && data.rekomendasi.length) {
                cpRenderKomposisi(data.rekomendasi);
                document.getElementById('cpRekomenSection').classList.remove('hidden');
            }
        })
        .catch(() => {});
}

// ── Render bar komposisi pakan ────────────────────────────────────────
function cpRenderKomposisi(items) {
    let barHtml    = '';
    let legendHtml = '';
    items.forEach(item => {
        const warna = cpWarnaMap[item.jenis] ?? '#aaaaaa';
        barHtml += `<div style="width:${item.persen}%;background:${warna}" title="${item.jenis}: ${item.persen}%"></div>`;
        legendHtml += `
        <div class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:${warna}"></span>
            <span class="text-[11px] text-gray-600">${item.jenis.charAt(0).toUpperCase() + item.jenis.slice(1)}</span>
            <span class="text-[11px] font-semibold text-gray-800 ml-auto">${item.persen}%</span>
        </div>`;
    });
    document.getElementById('cpKomposisiBar').innerHTML    = barHtml;
    document.getElementById('cpKomposisiLegend').innerHTML = legendHtml;
}

// ── Domba dipilih manual dari select ─────────────────────────────────
window.cpOnDombaChange = function(select) {
    const opt = select.options[select.selectedIndex];
    if (!select.value) return;
    const data = {
        ear_tag_id: select.value,
        nama:       opt.dataset.nama,
        kategori:   opt.dataset.kategori,
    };
    cpFillDombaCard(data);
    cpLoadStats(select.value);
};

// ── Pilih sesi ────────────────────────────────────────────────────────
window.cpSelectSesi = function(sesi) {
    document.getElementById('cp_sesi').value = sesi;
    document.querySelectorAll('.cp-sesi-btn').forEach(btn => {
        const active = btn.dataset.sesi === sesi;
        btn.classList.toggle('bg-gray-900',   active);
        btn.classList.toggle('text-white',    active);
        btn.classList.toggle('border-gray-900', active);
        btn.classList.toggle('border-gray-200', !active);
        btn.classList.toggle('text-gray-600',   !active);
        btn.classList.toggle('bg-white',        !active);
    });
};

// ── Update stok badge ─────────────────────────────────────────────────
window.cpUpdateStokBadge = function(select) {
    const opt    = select.options[select.selectedIndex];
    const badge  = document.getElementById('cpStokBadge');
    const stokKg = parseFloat(opt.dataset.stok || 0);

    if (select.value) {
        document.getElementById('cpStokText').textContent =
            `Stok ${opt.dataset.nama}: ${stokKg.toLocaleString('id-ID')} kg — cukup untuk pemberian hari ini`;
        badge.classList.remove('hidden');
        badge.classList.add('flex');
    } else {
        badge.classList.add('hidden');
        badge.classList.remove('flex');
    }
    cpCekStok();
    cpUpdatePreview();
};

// ── Cek stok vs jumlah input ──────────────────────────────────────────
window.cpCekStok = function() {
    const sel        = document.getElementById('cp_pakan_id');
    const jumlahGram = parseFloat(document.getElementById('cp_jumlah').value || 0);
    const warning    = document.getElementById('cpWarningStok');
    const btn        = document.getElementById('cpBtnSimpan');

    if (!sel.value) return;
    const stokGram = parseFloat(sel.options[sel.selectedIndex].dataset.stok || 0) * 1000;
    const lebih    = jumlahGram > 0 && stokGram > 0 && jumlahGram > stokGram;

    warning.classList.toggle('hidden', !lebih);
    warning.classList.toggle('flex',    lebih);
    btn.disabled = lebih;
};

// ── Update preview stok berkurang ─────────────────────────────────────
window.cpUpdatePreview = function() {
    const sel        = document.getElementById('cp_pakan_id');
    const jumlahGram = parseFloat(document.getElementById('cp_jumlah').value || 0);
    const preview    = document.getElementById('cpStokPreview');

    if (!sel.value || jumlahGram <= 0) {
        preview.classList.add('hidden');
        return;
    }

    const opt      = sel.options[sel.selectedIndex];
    const stokKg   = parseFloat(opt.dataset.stok || 0);
    const kurangKg = jumlahGram / 1000;
    const sisaKg   = stokKg - kurangKg;

    document.getElementById('cpStokPreviewText').textContent =
        `${opt.dataset.nama}: ${stokKg.toLocaleString('id-ID', {minimumFractionDigits:2})} kg → ${sisaKg.toLocaleString('id-ID', {minimumFractionDigits:2})} kg`;
    document.getElementById('cpStokPreviewBerkurang').textContent =
        `${jumlahGram.toLocaleString('id-ID')} g BERKURANG`;

    preview.classList.remove('hidden');
};

// ── Submit AJAX ────────────────────────────────────────────────────────
window.cpSubmit = function() {
    const earTag  = document.getElementById('cp_ear_tag').value;
    const pakanId = document.getElementById('cp_pakan_id').value;
    const tanggal = document.getElementById('cp_tanggal').value;
    const sesi    = document.getElementById('cp_sesi').value;
    const jumlah  = document.getElementById('cp_jumlah').value;

    const errors = [];
    if (!earTag)               errors.push('Pilih domba.');
    if (!pakanId)              errors.push('Pilih jenis pakan.');
    if (!tanggal)              errors.push('Isi tanggal.');
    if (!sesi)                 errors.push('Pilih sesi (Pagi / Sore).');
    if (!jumlah || jumlah <= 0) errors.push('Isi jumlah pakan.');
    if (errors.length) { alert(errors.join('\n')); return; }

    const btn    = document.getElementById('cpBtnSimpan');
    const btnTxt = document.getElementById('cpBtnText');
    btn.disabled = true;
    btnTxt.textContent = 'Menyimpan...';

    fetch("{{ route('pakan-individual.store') }}", {
        method:  'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept':       'application/json',
        },
        body: JSON.stringify({
            ear_tag_id:        earTag,
            pakan_id:          pakanId,
            tanggal_pemberian: tanggal,
            sesi:              sesi,
            jumlah_gram:       jumlah,
        }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeCatatPakan();
            window.showToast(data.message || 'Pemberian pakan berhasil dicatat.', 'success');
            setTimeout(() => window.location.reload(), 900);
        } else {
            window.showToast('Gagal: ' + (data.message || 'Terjadi kesalahan.'), 'error');
            btn.disabled = false;
            btnTxt.textContent = 'Simpan Pemberian Pakan';
        }
    })
    .catch(err => {
        alert('Error: ' + err.message);
        btn.disabled = false;
        btnTxt.textContent = 'Simpan Pemberian Pakan';
    });
};

// ── Reset form ─────────────────────────────────────────────────────────
function cpReset() {
    document.getElementById('cp_ear_tag').value  = '';
    document.getElementById('cp_pakan_id').value = '';
    document.getElementById('cp_tanggal').value  = new Date().toISOString().split('T')[0];
    document.getElementById('cp_sesi').value     = '';
    document.getElementById('cp_jumlah').value   = '';

    document.getElementById('cpBannerDomba').classList.add('hidden');
    document.getElementById('cpBannerDomba').classList.remove('flex');
    document.getElementById('cpDombaCard').classList.add('hidden');
    document.getElementById('cpSelectDombaWrap').classList.remove('hidden');
    document.getElementById('cpStokBadge').classList.add('hidden');
    document.getElementById('cpStokBadge').classList.remove('flex');
    document.getElementById('cpWarningStok').classList.add('hidden');
    document.getElementById('cpWarningStok').classList.remove('flex');
    document.getElementById('cpRekomenSection').classList.add('hidden');
    document.getElementById('cpSummarySection').classList.add('hidden');
    document.getElementById('cpStokPreview').classList.add('hidden');
    document.getElementById('cpBtnSimpan').disabled = false;
    document.getElementById('cpBtnText').textContent = 'Simpan Pemberian Pakan';

    document.querySelectorAll('.cp-sesi-btn').forEach(btn => {
        btn.classList.remove('bg-gray-900', 'text-white', 'border-gray-900');
        btn.classList.add('border-gray-200', 'text-gray-600', 'bg-white');
    });
}

document.getElementById('modalCatatPakan').addEventListener('click', function(e) {
    if (e.target === this) closeCatatPakan();
});
</script>