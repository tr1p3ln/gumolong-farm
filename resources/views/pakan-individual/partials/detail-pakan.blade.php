{{--
    PARTIAL: resources/views/pakan-individual/partials/detail-pakan.blade.php
    Panggil dengan: @include('pakan-individual.partials.detail-pakan')
    Buka modal: openDetailPakan('EAR_TAG_ID')
--}}

<div id="modalDetailPakan"
    class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-xl shadow-xl flex flex-col" style="max-height:92vh;">

        {{-- ══ HEADER ══ --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <h2 id="dpTitle" class="text-base font-bold text-gray-800">Detail Pakan Individual</h2>
                <p id="dpSubtitle" class="text-[11px] text-gray-400 mt-0.5">UC-06 (Read) | ID: —</p>
            </div>
            <button type="button" onclick="closeDetailPakan()"
                class="w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 transition text-xl leading-none">&times;</button>
        </div>

        {{-- ══ BODY ══ --}}
        <div class="flex-1 overflow-y-auto">

            {{-- Loading state --}}
            <div id="dpLoading" class="flex items-center justify-center py-16">
                <div class="flex flex-col items-center gap-3">
                    <svg class="animate-spin w-7 h-7 text-primary" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    <p class="text-xs text-gray-400">Memuat data...</p>
                </div>
            </div>

            {{-- Content (hidden saat loading) --}}
            <div id="dpContent" class="hidden px-6 py-5 space-y-5">

                {{-- ── SECTION 1: Info Domba + Stats ── --}}
                <div class="flex gap-4">

                    {{-- Card domba kiri --}}
                    <div class="w-44 flex-shrink-0 border border-gray-200 rounded-lg p-4 flex flex-col items-center gap-2 bg-gray-50">
                        {{-- Avatar placeholder (no image) --}}
                        <div class="w-16 h-16 rounded-lg bg-gray-200 flex items-center justify-center">
                            <svg class="w-9 h-9 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4C10.9 4 10 4.9 10 6s.9 2 2 2 2-.9 2-2-.9-2-2-2zM6.5 8C5.1 8 4 9.1 4 10.5S5.1 13 6.5 13H9v5h6v-5h2.5C18.9 13 20 11.9 20 10.5S18.9 8 17.5 8c-.7 0-1.4.3-1.9.8C14.9 8.3 13.5 8 12 8s-2.9.3-3.6.8C7.9 8.3 7.2 8 6.5 8z"/>
                            </svg>
                        </div>
                        <div class="text-center">
                            <p id="dpEarTag" class="text-base font-bold text-gray-900 font-mono">—</p>
                            <p id="dpNama" class="text-xs text-gray-500 mt-0.5">—</p>
                        </div>
                        <div class="flex flex-wrap justify-center gap-1 mt-1">
                            <span id="dpKategoriRas" class="text-[10px] bg-gray-200 text-gray-600 rounded-full px-2 py-0.5 font-medium">—</span>
                            <span id="dpBerat" class="text-[10px] bg-gray-200 text-gray-600 rounded-full px-2 py-0.5 font-medium">— kg</span>
                        </div>
                        <div id="dpStatusRekomendasi" class="mt-1 flex items-center gap-1 text-[10px] text-green-600 font-semibold">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Status: Sesuai Rekomendasi
                        </div>
                    </div>

                    {{-- Stats kanan --}}
                    <div class="flex-1 flex flex-col gap-3">

                        {{-- Total Pakan + Kenaikan BB --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="border border-gray-200 rounded-lg p-3">
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-1">Total Pakan (30hr)</p>
                                <p id="dpTotalPakan" class="text-2xl font-bold text-gray-900">— <span class="text-sm font-normal text-gray-500">kg</span></p>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-3">
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-1">Kenaikan BB</p>
                                <div class="flex items-baseline gap-1.5">
                                    <p id="dpKenaikanBB" class="text-2xl font-bold text-gray-900">—</p>
                                    <span class="text-sm font-normal text-gray-500">kg</span>
                                    <span id="dpKenaikanPersen" class="text-[10px] font-semibold text-green-600"></span>
                                </div>
                            </div>
                        </div>

                        {{-- FCR Card --}}
                        <div id="dpFcrCard" class="rounded-lg p-4 bg-green-600">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-green-200 mb-1">Feed Conversion Ratio (FCR)</p>
                            <div class="flex items-end justify-between">
                                <p id="dpFcrValue" class="text-4xl font-bold text-white">—</p>
                                <div class="text-right">
                                    <span id="dpFcrBadge" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-white/20 text-white">—</span>
                                    <p id="dpFcrTarget" class="text-[10px] text-green-200 mt-1">Target: &lt; 5.00</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ── SECTION 2: Rekomendasi Komposisi Pakan ── --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-sm">🌿</span>
                            <p class="text-sm font-bold text-gray-800">Rekomendasi Komposisi Pakan</p>
                        </div>
                        <span class="text-xs text-gray-400">TOTAL: <span id="dpTotalHarian">—</span> g/hari</span>
                    </div>

                    <div id="dpKomposisiList" class="border border-gray-200 rounded-lg p-4 space-y-3">
                        <p class="text-xs text-gray-400 italic">Memuat komposisi...</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- ══ FOOTER ══ --}}
        <div class="flex justify-between items-center px-6 py-4 border-t border-gray-100 flex-shrink-0 bg-gray-50/50">
            <button type="button" onclick="closeDetailPakan()"
                class="px-5 py-2.5 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                Tutup
            </button>
            <button type="button" id="dpBtnCatat"
                class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                + Catat Pemberian
            </button>
        </div>

    </div>
</div>

<script>
const dpWarnaMap = {
    rumput: '#639922', konsentrat: '#378ADD',
    silase: '#BA7517', dedak: '#888780', ampas_tahu: '#888780'
};

window.openDetailPakan = function(earTagId, dombaData = null) {
    // Reset & show loading
    document.getElementById('dpLoading').classList.remove('hidden');
    document.getElementById('dpContent').classList.add('hidden');

    const modal = document.getElementById('modalDetailPakan');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';

    // Set header sementara
    if (dombaData) {
        document.getElementById('dpTitle').textContent = `Detail Pakan Individual — ${dombaData.ear_tag_id} (${dombaData.nama})`;
    }

    // Set tombol catat pemberian
    document.getElementById('dpBtnCatat').onclick = function() {
        closeDetailPakan();
        if (dombaData) {
            window.openCatatPakan(dombaData);
        } else {
            window.openCatatPakan({ ear_tag_id: earTagId, nama: earTagId, kategori: '' });
        }
    };

    // Fetch stats dari controller
    fetch(`/pakan-individual/${earTagId}/stats`)
        .then(r => r.json())
        .then(data => {
            dpFillData(earTagId, dombaData, data);
            document.getElementById('dpLoading').classList.add('hidden');
            document.getElementById('dpContent').classList.remove('hidden');
        })
        .catch(() => {
            document.getElementById('dpLoading').innerHTML =
                '<p class="text-xs text-red-400 py-8 text-center">Gagal memuat data.</p>';
        });
};

window.closeDetailPakan = function() {
    const modal = document.getElementById('modalDetailPakan');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
};

function dpFillData(earTagId, dombaData, data) {
    // Header
    const nama = dombaData?.nama ?? earTagId;
    document.getElementById('dpTitle').textContent = `Detail Pakan Individual — ${earTagId} (${nama})`;
    document.getElementById('dpSubtitle').textContent = `UC-06 (Read) | ID: ${earTagId}`;

    // Info domba
    document.getElementById('dpEarTag').textContent = earTagId;
    document.getElementById('dpNama').textContent   = nama;
    document.getElementById('dpKategoriRas').textContent =
        dombaData ? `${ucfirst(dombaData.kategori)}${dombaData.ras ? ' — ' + dombaData.ras : ''}` : '—';
    document.getElementById('dpBerat').textContent =
        dombaData?.berat_kg ? parseFloat(dombaData.berat_kg).toFixed(1) + ' kg' : '— kg';

    // Total pakan — tampilkan dengan 2 desimal
    const totalPakan = data.total_pakan_30hr;
    document.getElementById('dpTotalPakan').innerHTML = totalPakan
        ? `${Number(totalPakan).toFixed(2)} <span class="text-sm font-normal text-gray-500">kg</span>`
        : `— <span class="text-sm font-normal text-gray-500">kg</span>`;

    // Kenaikan BB — pakai delta_bobot_kg dari service (lebih akurat)
    const kenaikan = data.kenaikan_bb ?? data.delta_bobot_kg ?? null;
    document.getElementById('dpKenaikanBB').textContent = kenaikan !== null
        ? Number(kenaikan).toFixed(2)
        : '—';

    if (kenaikan && data.berat_awal_kg) {
        // Persen kenaikan dari berat awal
        const persen = ((kenaikan / data.berat_awal_kg) * 100).toFixed(1);
        document.getElementById('dpKenaikanPersen').textContent = `↑${persen}%`;
    } else {
        document.getElementById('dpKenaikanPersen').textContent = '';
    }

    // FCR Card
    const fcr    = data.fcr;
    const status = data.fcr_status ?? (
        fcr === null  ? null  :
        fcr < 5       ? 'sangat_efisien' :
        fcr <= 7      ? 'normal'         :
        fcr <= 9      ? 'kurang_efisien' : 'perlu_evaluasi'
    );
    const fcrCardColors = {
        sangat_efisien: 'bg-green-600',
        normal:         'bg-blue-600',
        kurang_efisien: 'bg-yellow-500',
        perlu_evaluasi: 'bg-red-600',
    };
    const statusLabel = data.fcr_status_label ?? {
        sangat_efisien: 'Sangat Efisien',
        normal:         'Normal',
        kurang_efisien: 'Kurang Efisien',
        perlu_evaluasi: 'Perlu Evaluasi',
    }[status] ?? 'Belum ada data';

    const fcrCard = document.getElementById('dpFcrCard');
    fcrCard.className = `rounded-lg p-4 ${fcrCardColors[status] ?? 'bg-gray-400'}`;
    document.getElementById('dpFcrValue').textContent  = fcr !== null ? Number(fcr).toFixed(2) : '—';
    document.getElementById('dpFcrBadge').textContent  = statusLabel;
    document.getElementById('dpFcrTarget').textContent = 'Target: < 5.00';

    // Komposisi pakan
    const list = document.getElementById('dpKomposisiList');
    if (data.rekomendasi && data.rekomendasi.length) {
        let html = '';
        let totalGram = 0;
        data.rekomendasi.forEach(item => {
            const warna = dpWarnaMap[item.jenis] ?? '#aaaaaa';
            totalGram  += parseFloat(item.total_gram);
            html += `
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-sm font-semibold text-gray-700">${ucfirst(item.jenis)}</span>
                    <span class="text-sm font-bold text-gray-800" style="color:${warna}">
                        ${Number(item.total_gram).toLocaleString('id-ID')}g (${item.persen}%)
                    </span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div class="h-full rounded-full transition-all" style="width:${item.persen}%;background:${warna}"></div>
                </div>
            </div>`;
        });
        list.innerHTML = html;
        document.getElementById('dpTotalHarian').textContent =
            Number(data.total_ideal_gram ?? totalGram).toLocaleString('id-ID');
    } else {
        list.innerHTML = '<p class="text-xs text-gray-400 italic text-center py-2">Belum ada data komposisi pakan.</p>';
        document.getElementById('dpTotalHarian').textContent = '—';
    }
}

function ucfirst(str) {
    if (!str) return '—';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Tutup klik backdrop
document.getElementById('modalDetailPakan').addEventListener('click', function(e) {
    if (e.target === this) closeDetailPakan();
});
</script>