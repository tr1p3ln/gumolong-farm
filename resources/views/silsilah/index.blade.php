@extends('layouts.app')
@section('page-title', 'Silsilah & Pedigree')

@section('content')
<div x-data="{
    modalCekInbreeding: false,
    modalRekomendasiPejantan: false,

    cekForm: { induk_id: '', pejantan_id: '' },
    cekResult: null,
    cekLoading: false,

    rekForm: { induk_id: '' },
    rekResult: null,
    rekLoading: false,

    async cekInbreeding() {
        if (!this.cekForm.induk_id || !this.cekForm.pejantan_id) return;
        this.cekLoading = true;
        this.cekResult = null;
        try {
            const res = await fetch('/silsilah/cek-inbreeding', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(this.cekForm)
            });
            this.cekResult = await res.json();
        } catch (err) {
            console.error(err);
        } finally {
            this.cekLoading = false;
        }
    },

    async rekomendasiPejantan() {
        if (!this.rekForm.induk_id) return;
        this.rekLoading = true;
        this.rekResult = null;
        try {
            const res = await fetch('/silsilah/rekomendasi-pejantan?induk_id=' + encodeURIComponent(this.rekForm.induk_id), {
                headers: { 'Accept': 'application/json' }
            });
            this.rekResult = await res.json();
        } catch (err) {
            console.error(err);
        } finally {
            this.rekLoading = false;
        }
    }
}">

{{-- ════════════════════════════════════
     PAGE HEADER
════════════════════════════════════ --}}
<div class="flex justify-between items-start mb-6">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Silsilah & Pedigree</h1>
        <p class="text-gray-500 text-sm mt-1">
            Kelola pedigree domba dan deteksi inbreeding —
            <span class="bg-gray-100 px-1.5 py-0.5 rounded text-xs font-mono">tabel DOMBA (self-ref)</span>
        </p>
    </div>
    <div class="flex gap-3">
        <button @click="modalCekInbreeding = true"
                class="flex items-center gap-2 px-4 py-2.5 bg-[#B14B6F] text-white text-sm
                       font-bold rounded-lg hover:bg-rose-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Cek Inbreeding
        </button>
        <button @click="modalRekomendasiPejantan = true"
                class="flex items-center gap-2 px-4 py-2.5 bg-[#2E7D32] text-white text-sm
                       font-bold rounded-lg hover:bg-green-800 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Rekomendasi Pejantan
        </button>
    </div>
</div>

{{-- ════════════════════════════════════
     INFO CARDS
════════════════════════════════════ --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Domba dengan Silsilah</label>
        <p class="text-3xl font-black text-[#2E7D32] mt-2">{{ $dombas->total() }}</p>
        <p class="text-xs text-gray-500 mt-1">di database</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Domba dengan Kedua Orang Tua</label>
        <p class="text-3xl font-black text-[#607F5B] mt-2">
            {{ $dombas->getCollection()->filter(fn($d) => $d->induk_id && $d->ayah_id)->count() }}
        </p>
        <p class="text-xs text-gray-500 mt-1">halaman ini</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Domba tanpa Data Silsilah</label>
        <p class="text-3xl font-black text-[#B14B6F] mt-2">
            {{ $dombas->getCollection()->filter(fn($d) => !$d->induk_id && !$d->ayah_id)->count() }}
        </p>
        <p class="text-xs text-gray-500 mt-1">halaman ini</p>
    </div>
</div>

{{-- ════════════════════════════════════
     FILTER BAR
════════════════════════════════════ --}}
<div class="bg-white border border-gray-200 rounded-xl p-4 mb-6">
    <form action="{{ route('silsilah.index') }}" method="GET" class="flex gap-3">
        <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari Ear Tag atau Nama domba..."
                   class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm
                          focus:ring-2 focus:ring-[#2E7D32] focus:border-transparent outline-none">
        </div>
        <select name="jenis_kelamin"
                class="bg-white border border-gray-200 rounded-lg text-sm py-2 px-3
                       focus:ring-2 focus:ring-[#2E7D32] outline-none">
            <option value="">Semua Jenis Kelamin</option>
            <option value="jantan" {{ request('jenis_kelamin') === 'jantan' ? 'selected' : '' }}>Jantan</option>
            <option value="betina" {{ request('jenis_kelamin') === 'betina' ? 'selected' : '' }}>Betina</option>
        </select>
        <button type="submit"
                class="px-5 py-2 bg-[#2E7D32] text-white text-sm font-bold rounded-lg hover:bg-green-800 transition-colors">
            Filter
        </button>
        <a href="{{ route('silsilah.index') }}"
           class="px-4 py-2 border border-gray-300 text-gray-600 text-sm font-bold rounded-lg hover:bg-gray-50 transition-colors">
            Reset
        </a>
    </form>
</div>

{{-- ════════════════════════════════════
     TABEL
════════════════════════════════════ --}}
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Ear Tag / Domba</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Induk (Ibu)</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Pejantan (Ayah)</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Kategori</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Kelengkapan Silsilah</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($dombas as $domba)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                            {{ $domba->jenis_kelamin === 'betina' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $domba->jenis_kelamin === 'betina' ? '♀' : '♂' }}
                        </div>
                        <div>
                            <p class="font-bold text-[#2E7D32] font-mono">{{ $domba->ear_tag_id }}</p>
                            <p class="text-sm text-gray-600">{{ $domba->nama ?? '-' }}</p>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-5">
                    @if($domba->induk)
                        <div class="flex items-center gap-2">
                            <span class="text-rose-500">♀</span>
                            <div>
                                <p class="text-sm font-bold text-gray-800 font-mono">{{ $domba->induk->ear_tag_id }}</p>
                                <p class="text-xs text-gray-500">{{ $domba->induk->nama ?? '-' }}</p>
                            </div>
                        </div>
                    @else
                        <span class="text-xs text-gray-400 italic">Tidak diketahui</span>
                    @endif
                </td>

                <td class="px-6 py-5">
                    @if($domba->ayah)
                        <div class="flex items-center gap-2">
                            <span class="text-blue-500">♂</span>
                            <div>
                                <p class="text-sm font-bold text-gray-800 font-mono">{{ $domba->ayah->ear_tag_id }}</p>
                                <p class="text-xs text-gray-500">{{ $domba->ayah->nama ?? '-' }}</p>
                            </div>
                        </div>
                    @else
                        <span class="text-xs text-gray-400 italic">Tidak diketahui</span>
                    @endif
                </td>

                <td class="px-6 py-5">
                    <span class="text-sm text-gray-700 capitalize">{{ $domba->kategori }}</span>
                </td>

                <td class="px-6 py-5">
                    @php
                        $hasInduk = !is_null($domba->induk_id);
                        $hasAyah  = !is_null($domba->ayah_id);
                    @endphp
                    @if($hasInduk && $hasAyah)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 border border-green-200 text-green-700 text-xs font-bold rounded-full">
                            ✓ Lengkap
                        </span>
                    @elseif($hasInduk || $hasAyah)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold rounded-full">
                            ⚠ Sebagian
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-500 text-xs font-bold rounded-full">
                            — Tidak Ada
                        </span>
                    @endif
                </td>

                <td class="px-6 py-5 text-right">
                    <a href="{{ route('silsilah.show', $domba->ear_tag_id) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-1.5 border border-[#2E7D32]
                              text-[#2E7D32] text-xs font-bold rounded-lg hover:bg-green-50 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Lihat Pedigree
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                    Belum ada data domba.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
            Menampilkan {{ $dombas->firstItem() ?? 0 }}–{{ $dombas->lastItem() ?? 0 }}
            dari {{ $dombas->total() }} data
        </p>
        {{ $dombas->links() }}
    </div>
</div>

{{-- ════════════════════════════════════
     MODAL CEK INBREEDING
════════════════════════════════════ --}}
<div x-show="modalCekInbreeding"
     @keydown.escape.window="modalCekInbreeding = false"
     class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
     style="display: none;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-200 flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Cek Inbreeding (FR-7.3)</h2>
                <p class="text-xs text-gray-500 mt-1">Periksa hubungan kekerabatan antara induk dan pejantan</p>
            </div>
            <button @click="modalCekInbreeding = false; cekResult = null"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-8 space-y-6">
            <div class="bg-[#FAFAF7] border-l-4 border-[#2E7D32] p-4 flex gap-3">
                <svg class="w-5 h-5 text-[#2E7D32] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-gray-700">
                    Masukkan ID Induk Betina dan ID Pejantan untuk menghitung
                    <strong>Inbreeding Coefficient (COI)</strong> menggunakan Wright's Path Method.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                        ID Induk Betina <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" x-model="cekForm.induk_id"
                           placeholder="Contoh: B-001"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-[#2E7D32] focus:border-transparent outline-none font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                        ID Pejantan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" x-model="cekForm.pejantan_id"
                           placeholder="Contoh: J-001"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-[#2E7D32] focus:border-transparent outline-none font-mono">
                </div>
            </div>

            <button @click="cekInbreeding()" :disabled="cekLoading"
                    class="w-full py-3 bg-[#B14B6F] text-white text-sm font-bold rounded-lg
                           hover:bg-rose-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!cekLoading">Hitung Inbreeding Coefficient</span>
                <span x-show="cekLoading">Menghitung...</span>
            </button>

            <div x-show="cekResult" class="space-y-4">
                <div :class="cekResult?.aman ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'"
                     class="border-2 rounded-xl p-6 text-center">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">
                        Inbreeding Coefficient (COI)
                    </p>
                    <p class="text-5xl font-black mb-2"
                       :class="cekResult?.aman ? 'text-[#2E7D32]' : 'text-[#B14B6F]'"
                       x-text="`${cekResult?.coi_persen}%`"></p>
                    <span class="px-4 py-1 text-sm font-bold rounded-full"
                          :class="cekResult?.aman ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                          x-text="cekResult?.status"></span>
                </div>

                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Rekomendasi</p>
                    <p class="text-sm text-gray-700" x-text="cekResult?.rekomendasi"></p>
                </div>

                <div class="grid grid-cols-4 gap-2 text-center text-xs">
                    <div class="p-2 bg-green-50 border border-green-200 rounded">
                        <p class="font-bold text-green-700">0%</p>
                        <p class="text-green-600">Tidak Ada</p>
                    </div>
                    <div class="p-2 bg-blue-50 border border-blue-200 rounded">
                        <p class="font-bold text-blue-700">&lt; 6.25%</p>
                        <p class="text-blue-600">Rendah Aman</p>
                    </div>
                    <div class="p-2 bg-amber-50 border border-amber-200 rounded">
                        <p class="font-bold text-amber-700">6.25–12.5%</p>
                        <p class="text-amber-600">Sedang</p>
                    </div>
                    <div class="p-2 bg-red-50 border border-red-200 rounded">
                        <p class="font-bold text-red-700">&gt; 12.5%</p>
                        <p class="text-red-600">Tinggi Hindari</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════
     MODAL REKOMENDASI PEJANTAN
════════════════════════════════════ --}}
<div x-show="modalRekomendasiPejantan"
     @keydown.escape.window="modalRekomendasiPejantan = false"
     class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
     style="display: none;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-8 py-6 border-b border-gray-200 flex justify-between items-start flex-shrink-0">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Rekomendasi Pejantan (FR-7.4)</h2>
                <p class="text-xs text-gray-500 mt-1">Pejantan diurutkan dari COI terkecil (terbaik) ke terbesar</p>
            </div>
            <button @click="modalRekomendasiPejantan = false; rekResult = null"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-8 space-y-6 overflow-y-auto">
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                    ID Induk Betina <span class="text-rose-500">*</span>
                </label>
                <div class="flex gap-3">
                    <input type="text" x-model="rekForm.induk_id"
                           placeholder="Contoh: B-001"
                           class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-[#2E7D32] focus:border-transparent outline-none font-mono">
                    <button @click="rekomendasiPejantan()" :disabled="rekLoading"
                            class="px-6 py-2 bg-[#2E7D32] text-white text-sm font-bold rounded-lg
                                   hover:bg-green-800 disabled:opacity-50 transition-colors">
                        <span x-show="!rekLoading">Cari Rekomendasi</span>
                        <span x-show="rekLoading">Mencari...</span>
                    </button>
                </div>
            </div>

            <div x-show="rekResult">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                    Hasil Rekomendasi Pejantan untuk Induk
                    <span class="text-[#2E7D32] font-mono" x-text="rekResult?.induk_id"></span>
                </p>

                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">#</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Pejantan</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Ras</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Kandang</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">COI</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(p, index) in rekResult?.rekomendasi" :key="p.ear_tag_id">
                                <tr :class="p.aman ? 'hover:bg-green-50' : 'hover:bg-red-50 opacity-60'">
                                    <td class="px-4 py-3">
                                        <span class="text-xs font-bold"
                                              :class="index === 0 ? 'text-amber-500' : 'text-gray-400'"
                                              x-text="index + 1"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-bold font-mono text-[#2E7D32]" x-text="p.ear_tag_id"></p>
                                        <p class="text-xs text-gray-500" x-text="p.nama || '-'"></p>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600" x-text="p.ras"></td>
                                    <td class="px-4 py-3 text-gray-600" x-text="p.kandang || '-'"></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="font-bold font-mono text-sm"
                                              :class="p.aman ? 'text-[#2E7D32]' : 'text-[#B14B6F]'"
                                              x-text="`${p.coi_persen}%`"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 text-xs font-bold rounded-full"
                                              :class="p.aman ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                              x-text="p.aman ? 'Aman ✓' : 'Hindari ✗'"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <p class="text-xs text-gray-400 mt-3 italic">
                    * Pejantan diurutkan dari COI terkecil (paling direkomendasikan) | COI &lt; 6.25% = Aman
                </p>
            </div>
        </div>
    </div>
</div>

</div>{{-- /x-data --}}
@endsection
