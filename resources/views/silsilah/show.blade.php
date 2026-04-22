@extends('layouts.app')
@section('page-title', 'Pedigree Tree')

@push('styles')
<style>
    .pedigree-tree {
        display: flex;
        align-items: center;
        gap: 0;
        overflow-x: auto;
        padding: 24px;
        min-height: 300px;
    }
    .pedigree-generation {
        display: flex;
        flex-direction: column;
        justify-content: space-around;
        min-width: 200px;
    }
    .pedigree-node {
        background: white;
        border: 2px solid #E5E7EB;
        border-radius: 8px;
        padding: 10px 14px;
        margin: 8px 0;
        position: relative;
        cursor: pointer;
        transition: all 0.2s;
        min-height: 60px;
    }
    .pedigree-node:hover {
        border-color: #2E7D32;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.15);
    }
    .pedigree-node.is-subject {
        border-color: #2E7D32;
        border-width: 3px;
        background: #F0FFF0;
    }
    .pedigree-node.is-female {
        border-left: 4px solid #B14B6F;
    }
    .pedigree-node.is-male {
        border-left: 4px solid #3B82F6;
    }
    .pedigree-node.is-unknown {
        border: 2px dashed #D1D5DB;
        background: #F9FAFB;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .pedigree-connector {
        width: 40px;
        display: flex;
        align-items: center;
        position: relative;
    }
    .pedigree-connector::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: #D1D5DB;
    }
    .generation-label {
        text-align: center;
        font-size: 10px;
        font-weight: 700;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 12px;
    }
</style>
@endpush

@section('content')
<div x-data="{
    earTagId: '{{ $earTagId }}',
    dombaData: null,
    pedigree: null,
    coiData: null,
    loading: true,
    activeTab: 'tree',

    async loadData() {
        try {
            const res = await fetch('/silsilah/' + this.earTagId, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            this.dombaData = data.domba;
            this.pedigree  = data.pedigree;
            this.coiData   = {
                coi:       data.coi,
                coi_persen: data.coi_persen,
                status:    data.status_inbreeding
            };
        } catch (err) {
            console.error(err);
        } finally {
            this.loading = false;
        }
    },

    getStatusColor(status) {
        if (!status || status === 'Tidak Ada Inbreeding') return 'text-green-700 bg-green-100';
        if (status.includes('Rendah'))  return 'text-green-700 bg-green-100';
        if (status.includes('Sedang'))  return 'text-amber-700 bg-amber-100';
        return 'text-red-700 bg-red-100';
    },

    navigateTo(earTagId) {
        if (earTagId) window.location.href = '/silsilah/' + earTagId;
    }
}" x-init="loadData()">

{{-- BREADCRUMB --}}
<div class="mb-6 flex items-center gap-2">
    <a href="{{ route('silsilah.index') }}"
       class="text-sm font-bold text-[#607F5B] hover:text-[#2E7D32] flex items-center gap-1 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Daftar Silsilah
    </a>
    <span class="text-gray-400">/</span>
    <span class="text-sm text-gray-600 font-mono">{{ $earTagId }}</span>
</div>

{{-- LOADING --}}
<div x-show="loading" class="text-center py-20">
    <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-[#2E7D32]"></div>
    <p class="mt-4 text-gray-500">Memuat pedigree tree...</p>
</div>

{{-- MAIN CONTENT --}}
<div x-show="!loading" class="space-y-6">

    {{-- HEADER DOMBA --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-4xl font-black text-[#2E7D32] font-mono" x-text="dombaData?.ear_tag_id"></span>
                    <span class="px-3 py-1 text-sm font-bold border border-gray-300 text-gray-600 rounded-full capitalize"
                          x-text="dombaData?.kategori"></span>
                    <span class="px-3 py-1 text-sm font-bold border border-[#2E7D32] text-[#2E7D32] rounded-full capitalize"
                          x-text="dombaData?.status"></span>
                </div>
                <p class="text-gray-600 text-lg" x-text="dombaData?.nama || 'Tanpa Nama'"></p>
                <p class="text-sm text-gray-400 mt-1">
                    <span x-text="dombaData?.jenis_kelamin === 'betina' ? '♀ Betina' : '♂ Jantan'"></span>
                    · <span x-text="dombaData?.ras"></span>
                    · <span x-text="dombaData?.kandang?.nama_kandang || '-'"></span>
                </p>
            </div>

            {{-- COI CARD --}}
            <div class="text-center border border-gray-200 rounded-xl p-5 min-w-[160px]">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                    Inbreeding Coefficient
                </p>
                <p class="text-4xl font-black"
                   :class="(coiData?.coi ?? 0) < 0.0625 ? 'text-[#2E7D32]' : 'text-[#B14B6F]'"
                   x-text="`${coiData?.coi_persen ?? 0}%`"></p>
                <span class="mt-2 inline-block px-3 py-0.5 text-xs font-bold rounded-full"
                      :class="getStatusColor(coiData?.status)"
                      x-text="coiData?.status ?? '-'"></span>
            </div>
        </div>
    </div>

    {{-- TAB NAVIGATION --}}
    <div class="flex gap-1 bg-gray-100 rounded-xl p-1">
        <button @click="activeTab = 'tree'"
                :class="activeTab === 'tree' ? 'bg-white shadow-sm text-[#2E7D32] font-bold' : 'text-gray-500'"
                class="flex-1 py-2.5 text-sm font-medium rounded-lg transition-all">
            Pohon Silsilah (Pedigree Tree)
        </button>
        <button @click="activeTab = 'tabel'"
                :class="activeTab === 'tabel' ? 'bg-white shadow-sm text-[#2E7D32] font-bold' : 'text-gray-500'"
                class="flex-1 py-2.5 text-sm font-medium rounded-lg transition-all">
            Tabel Leluhur
        </button>
        <button @click="activeTab = 'analisis'"
                :class="activeTab === 'analisis' ? 'bg-white shadow-sm text-[#2E7D32] font-bold' : 'text-gray-500'"
                class="flex-1 py-2.5 text-sm font-medium rounded-lg transition-all">
            Analisis Inbreeding
        </button>
    </div>

    {{-- ════════════════════════════
         TAB 1: PEDIGREE TREE VISUAL
    ════════════════════════════ --}}
    <div x-show="activeTab === 'tree'" class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-bold text-gray-900">Pohon Silsilah — 4 Generasi</h3>
            <p class="text-xs text-gray-500 mt-0.5">
                Kiri = Domba Subjek | Kanan = Leluhur
                <span class="ml-3 inline-flex items-center gap-2">
                    <span class="inline-block w-3 h-3 rounded bg-rose-300"></span>Betina (♀)
                    <span class="inline-block w-3 h-3 rounded bg-blue-300 ml-1"></span>Jantan (♂)
                    <span class="border border-dashed border-gray-300 inline-block w-3 h-3 rounded ml-1"></span>Tidak Diketahui
                </span>
            </p>
        </div>

        <div class="overflow-x-auto">
            <div class="pedigree-tree" style="min-width: 900px;">

                {{-- GENERASI 0 — SUBJEK --}}
                <div class="pedigree-generation">
                    <div class="generation-label">Subjek</div>
                    <template x-if="pedigree">
                        <div class="pedigree-node is-subject"
                             :class="pedigree.jenis_kelamin === 'betina' ? 'is-female' : 'is-male'">
                            <p class="text-xs font-bold text-gray-500 uppercase">
                                <span x-text="pedigree.jenis_kelamin === 'betina' ? '♀' : '♂'"></span>
                                <span x-text="pedigree.jenis_kelamin"></span>
                            </p>
                            <p class="font-bold text-[#2E7D32] font-mono text-sm" x-text="pedigree.ear_tag_id"></p>
                            <p class="text-xs text-gray-500" x-text="pedigree.nama || '-'"></p>
                        </div>
                    </template>
                </div>

                <div class="pedigree-connector"></div>

                {{-- GENERASI 1 — ORANG TUA --}}
                <div class="pedigree-generation">
                    <div class="generation-label">Orang Tua (Gen 1)</div>

                    <template x-if="pedigree?.induk">
                        <div class="pedigree-node is-female"
                             @click="navigateTo(pedigree.induk.ear_tag_id)">
                            <p class="text-xs font-bold text-rose-400 uppercase">♀ Induk (Ibu)</p>
                            <p class="font-bold font-mono text-sm text-gray-800" x-text="pedigree.induk.ear_tag_id"></p>
                            <p class="text-xs text-gray-500" x-text="pedigree.induk.nama || '-'"></p>
                        </div>
                    </template>
                    <template x-if="!pedigree?.induk">
                        <div class="pedigree-node is-unknown">
                            <p class="text-xs text-gray-400 italic">Induk tidak diketahui</p>
                        </div>
                    </template>

                    <template x-if="pedigree?.ayah">
                        <div class="pedigree-node is-male"
                             @click="navigateTo(pedigree.ayah.ear_tag_id)">
                            <p class="text-xs font-bold text-blue-400 uppercase">♂ Pejantan (Ayah)</p>
                            <p class="font-bold font-mono text-sm text-gray-800" x-text="pedigree.ayah.ear_tag_id"></p>
                            <p class="text-xs text-gray-500" x-text="pedigree.ayah.nama || '-'"></p>
                        </div>
                    </template>
                    <template x-if="!pedigree?.ayah">
                        <div class="pedigree-node is-unknown">
                            <p class="text-xs text-gray-400 italic">Pejantan tidak diketahui</p>
                        </div>
                    </template>
                </div>

                <div class="pedigree-connector"></div>

                {{-- GENERASI 2 — KAKEK/NENEK --}}
                <div class="pedigree-generation">
                    <div class="generation-label">Kakek/Nenek (Gen 2)</div>

                    <template x-if="pedigree?.induk?.induk">
                        <div class="pedigree-node is-female" style="font-size: 11px;"
                             @click="navigateTo(pedigree.induk.induk.ear_tag_id)">
                            <p class="font-bold text-rose-400">♀ Nenek (Ibu's Ibu)</p>
                            <p class="font-bold font-mono" x-text="pedigree.induk.induk.ear_tag_id"></p>
                            <p class="text-gray-500" x-text="pedigree.induk.induk.nama || '-'"></p>
                        </div>
                    </template>
                    <template x-if="!pedigree?.induk?.induk">
                        <div class="pedigree-node is-unknown" style="font-size: 10px;">
                            <p class="text-gray-400 italic">Tidak diketahui</p>
                        </div>
                    </template>

                    <template x-if="pedigree?.induk?.ayah">
                        <div class="pedigree-node is-male" style="font-size: 11px;"
                             @click="navigateTo(pedigree.induk.ayah.ear_tag_id)">
                            <p class="font-bold text-blue-400">♂ Kakek (Ibu's Ayah)</p>
                            <p class="font-bold font-mono" x-text="pedigree.induk.ayah.ear_tag_id"></p>
                            <p class="text-gray-500" x-text="pedigree.induk.ayah.nama || '-'"></p>
                        </div>
                    </template>
                    <template x-if="!pedigree?.induk?.ayah">
                        <div class="pedigree-node is-unknown" style="font-size: 10px;">
                            <p class="text-gray-400 italic">Tidak diketahui</p>
                        </div>
                    </template>

                    <template x-if="pedigree?.ayah?.induk">
                        <div class="pedigree-node is-female" style="font-size: 11px;"
                             @click="navigateTo(pedigree.ayah.induk.ear_tag_id)">
                            <p class="font-bold text-rose-400">♀ Nenek (Ayah's Ibu)</p>
                            <p class="font-bold font-mono" x-text="pedigree.ayah.induk.ear_tag_id"></p>
                            <p class="text-gray-500" x-text="pedigree.ayah.induk.nama || '-'"></p>
                        </div>
                    </template>
                    <template x-if="!pedigree?.ayah?.induk">
                        <div class="pedigree-node is-unknown" style="font-size: 10px;">
                            <p class="text-gray-400 italic">Tidak diketahui</p>
                        </div>
                    </template>

                    <template x-if="pedigree?.ayah?.ayah">
                        <div class="pedigree-node is-male" style="font-size: 11px;"
                             @click="navigateTo(pedigree.ayah.ayah.ear_tag_id)">
                            <p class="font-bold text-blue-400">♂ Kakek (Ayah's Ayah)</p>
                            <p class="font-bold font-mono" x-text="pedigree.ayah.ayah.ear_tag_id"></p>
                            <p class="text-gray-500" x-text="pedigree.ayah.ayah.nama || '-'"></p>
                        </div>
                    </template>
                    <template x-if="!pedigree?.ayah?.ayah">
                        <div class="pedigree-node is-unknown" style="font-size: 10px;">
                            <p class="text-gray-400 italic">Tidak diketahui</p>
                        </div>
                    </template>
                </div>

            </div>
        </div>

        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            <p class="text-xs text-gray-400 italic">
                Klik pada node untuk melihat pedigree tree domba tersebut |
                Tree menampilkan sampai 4 generasi ke atas
            </p>
        </div>
    </div>

    {{-- ════════════════════════════
         TAB 2: TABEL LELUHUR
    ════════════════════════════ --}}
    <div x-show="activeTab === 'tabel'" class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-bold text-gray-900">Tabel Leluhur Lengkap</h3>
            <p class="text-xs text-gray-500 mt-0.5">Semua ancestor dari database (via WITH RECURSIVE PostgreSQL)</p>
        </div>

        <div class="p-6 space-y-6">
            {{-- Generasi 1 --}}
            <div>
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                    Generasi 1 — Orang Tua Langsung
                </h4>
                <div class="grid grid-cols-2 gap-4">
                    <div class="border border-rose-200 rounded-lg p-4 bg-rose-50">
                        <p class="text-xs font-bold text-rose-500 mb-2">♀ INDUK (IBU)</p>
                        <template x-if="pedigree?.induk">
                            <div>
                                <p class="font-bold font-mono text-gray-800" x-text="pedigree.induk.ear_tag_id"></p>
                                <p class="text-sm text-gray-600" x-text="pedigree.induk.nama || '-'"></p>
                                <p class="text-xs text-gray-400 mt-1" x-text="pedigree.induk.ras"></p>
                            </div>
                        </template>
                        <template x-if="!pedigree?.induk">
                            <p class="text-sm text-gray-400 italic">Tidak diketahui</p>
                        </template>
                    </div>
                    <div class="border border-blue-200 rounded-lg p-4 bg-blue-50">
                        <p class="text-xs font-bold text-blue-500 mb-2">♂ PEJANTAN (AYAH)</p>
                        <template x-if="pedigree?.ayah">
                            <div>
                                <p class="font-bold font-mono text-gray-800" x-text="pedigree.ayah.ear_tag_id"></p>
                                <p class="text-sm text-gray-600" x-text="pedigree.ayah.nama || '-'"></p>
                                <p class="text-xs text-gray-400 mt-1" x-text="pedigree.ayah.ras"></p>
                            </div>
                        </template>
                        <template x-if="!pedigree?.ayah">
                            <p class="text-sm text-gray-400 italic">Tidak diketahui</p>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Generasi 2 --}}
            <div>
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                    Generasi 2 — Kakek & Nenek
                </h4>
                <div class="grid grid-cols-4 gap-3">
                    <div class="border border-gray-200 rounded-lg p-3">
                        <p class="text-xs font-bold text-gray-500 mb-2">♀ Nenek (dari Ibu)</p>
                        <template x-if="pedigree?.induk?.induk">
                            <div>
                                <p class="font-bold font-mono text-xs text-gray-800" x-text="pedigree.induk.induk.ear_tag_id"></p>
                                <p class="text-xs text-gray-500" x-text="pedigree.induk.induk.nama || '-'"></p>
                            </div>
                        </template>
                        <template x-if="!pedigree?.induk?.induk">
                            <p class="text-xs text-gray-400 italic">Tidak diketahui</p>
                        </template>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3">
                        <p class="text-xs font-bold text-gray-500 mb-2">♂ Kakek (dari Ibu)</p>
                        <template x-if="pedigree?.induk?.ayah">
                            <div>
                                <p class="font-bold font-mono text-xs text-gray-800" x-text="pedigree.induk.ayah.ear_tag_id"></p>
                                <p class="text-xs text-gray-500" x-text="pedigree.induk.ayah.nama || '-'"></p>
                            </div>
                        </template>
                        <template x-if="!pedigree?.induk?.ayah">
                            <p class="text-xs text-gray-400 italic">Tidak diketahui</p>
                        </template>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3">
                        <p class="text-xs font-bold text-gray-500 mb-2">♀ Nenek (dari Ayah)</p>
                        <template x-if="pedigree?.ayah?.induk">
                            <div>
                                <p class="font-bold font-mono text-xs text-gray-800" x-text="pedigree.ayah.induk.ear_tag_id"></p>
                                <p class="text-xs text-gray-500" x-text="pedigree.ayah.induk.nama || '-'"></p>
                            </div>
                        </template>
                        <template x-if="!pedigree?.ayah?.induk">
                            <p class="text-xs text-gray-400 italic">Tidak diketahui</p>
                        </template>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-3">
                        <p class="text-xs font-bold text-gray-500 mb-2">♂ Kakek (dari Ayah)</p>
                        <template x-if="pedigree?.ayah?.ayah">
                            <div>
                                <p class="font-bold font-mono text-xs text-gray-800" x-text="pedigree.ayah.ayah.ear_tag_id"></p>
                                <p class="text-xs text-gray-500" x-text="pedigree.ayah.ayah.nama || '-'"></p>
                            </div>
                        </template>
                        <template x-if="!pedigree?.ayah?.ayah">
                            <p class="text-xs text-gray-400 italic">Tidak diketahui</p>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════
         TAB 3: ANALISIS INBREEDING
    ════════════════════════════ --}}
    <div x-show="activeTab === 'analisis'" class="bg-white border border-gray-200 rounded-xl p-6 space-y-6">
        <h3 class="font-bold text-gray-900">Analisis Inbreeding Coefficient</h3>

        <div class="border-2 rounded-xl p-8 text-center"
             :class="(coiData?.coi ?? 0) < 0.0625 ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">
                Wright's Inbreeding Coefficient (F)
            </p>
            <p class="text-6xl font-black"
               :class="(coiData?.coi ?? 0) < 0.0625 ? 'text-[#2E7D32]' : 'text-[#B14B6F]'"
               x-text="`${coiData?.coi_persen ?? 0}%`"></p>
            <p class="text-sm text-gray-500 mt-2" x-text="`F = ${coiData?.coi ?? 0}`"></p>
            <span class="mt-4 inline-block px-6 py-2 text-sm font-bold rounded-full"
                  :class="getStatusColor(coiData?.status)"
                  x-text="coiData?.status ?? 'Tidak Ada Data'"></span>
        </div>

        <div class="bg-gray-50 rounded-xl p-6">
            <h4 class="font-bold text-gray-900 mb-4">Interpretasi & Referensi Threshold</h4>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-green-400"></div>
                        <span class="text-sm font-medium">F = 0%</span>
                    </div>
                    <span class="text-sm text-gray-600">Tidak ada hubungan kekerabatan</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-green-300"></div>
                        <span class="text-sm font-medium">F &lt; 6.25% (1/16)</span>
                    </div>
                    <span class="text-sm text-gray-600">Rendah — Aman untuk dikawinkan</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-amber-400"></div>
                        <span class="text-sm font-medium">F 6.25–12.5% (1/8)</span>
                    </div>
                    <span class="text-sm text-gray-600">Sedang — Perhatian, pertimbangkan pilihan lain</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-orange-400"></div>
                        <span class="text-sm font-medium">F 12.5–25% (1/4)</span>
                    </div>
                    <span class="text-sm text-gray-600">Tinggi — Berisiko, tidak disarankan</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-red-500"></div>
                        <span class="text-sm font-medium">F &gt; 25%</span>
                    </div>
                    <span class="text-sm text-gray-600">Sangat Tinggi — Hindari, dampak genetik serius</span>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-xs font-bold text-blue-700 mb-1">Metode Perhitungan</p>
            <p class="text-xs text-blue-600">
                Menggunakan <strong>Wright's Path Coefficient Method</strong>.
                Formula: F = Σ [ (0.5)^(L₁+L₂+1) × (1+Fₐ) ]
                untuk setiap common ancestor A, dimana L₁ dan L₂ adalah jarak
                generasi dari kedua orang tua ke ancestor bersama.
            </p>
        </div>
    </div>

</div>{{-- /x-show --}}
</div>{{-- /x-data --}}
@endsection
