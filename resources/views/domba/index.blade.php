@extends('layouts.app')

@section('page-title', 'Data Domba')

@section('content')

{{-- ═══════════════════════════════════════════════════
    Parent Alpine scope — semua modal membaca/menulis
    properti di sini via scope chain Alpine v3
═══════════════════════════════════════════════════ --}}
<div x-data="{
    modalTambah: false,
    modalEdit:   false,
    modalDetail: false,
    modalHapus:  false,
    selectedId:  null,

    openTambah() { this.modalTambah = true; },

    openEdit(id) {
        this.selectedId = id;
        this.modalEdit  = true;
    },

    openDetail(id) {
        this.selectedId  = id;
        this.modalDetail = true;
    },

    openHapus(id) {
        this.selectedId  = id;
        this.modalHapus  = true;
    },

    editDomba(id) {
        this.selectedId  = id;
        this.modalDetail = false;
        this.modalEdit   = true;
    }
}">

    {{-- ═══ MODALS ═══ --}}
    @include('domba.partials.modal-tambah')
    @include('domba.partials.modal-edit')
    @include('domba.partials.modal-detail')

    {{-- Modal Hapus (simple confirm) --}}
    <div
        x-show="modalHapus"
        x-data="{
            deleting: false,
            async konfirmHapus(id) {
                if (!id) return;
                this.deleting = true;
                try {
                    const res = await fetch('/domba/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json'
                        }
                    });
                    const json = await res.json();
                    if (json.success) {
                        window.location.reload();
                    } else {
                        alert(json.message ?? 'Gagal menghapus.');
                        this.deleting = false;
                    }
                } catch {
                    alert('Gagal terhubung ke server.');
                    this.deleting = false;
                }
            }
        }"
        @keydown.escape.window="if(modalHapus) { modalHapus = false; }"
        x-cloak
        class="fixed inset-0 z-[60] bg-black/50 flex items-center justify-center px-4"
        style="display: none;">
        <div class="absolute inset-0" @click="modalHapus = false"></div>
        <div class="relative z-10 w-full max-w-sm bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden"
             @click.stop>
            <div class="px-6 pt-6 pb-4">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Hapus Domba</h3>
                <p class="text-sm text-gray-600">
                    Yakin ingin menghapus domba
                    <span class="font-mono font-bold text-accent" x-text="selectedId"></span>?
                    Data akan diarsipkan (soft delete) dan dapat dipulihkan oleh Super Admin.
                </p>
            </div>
            <div class="px-6 pb-5 flex justify-end gap-3">
                <button type="button"
                        @click="modalHapus = false"
                        class="px-4 py-2.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                    Batal
                </button>
                <button type="button"
                        @click="konfirmHapus(selectedId)"
                        :disabled="deleting"
                        class="px-4 py-2.5 bg-accent text-white text-sm font-semibold rounded-md hover:opacity-90 transition disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center gap-2">
                    <svg x-show="deleting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="deleting ? 'Menghapus...' : 'Ya, Hapus'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══ SUMMARY CARDS ═══ --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
        @foreach ([
            ['label' => 'Total',     'value' => $summary['total'],     'bg' => 'bg-gray-100',   'text' => 'text-gray-700'],
            ['label' => 'Aktif',     'value' => $summary['aktif'],     'bg' => 'bg-green-100',  'text' => 'text-primary'],
            ['label' => 'Karantina', 'value' => $summary['karantina'], 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
            ['label' => 'Terjual',   'value' => $summary['terjual'],   'bg' => 'bg-blue-100',   'text' => 'text-blue-700'],
            ['label' => 'Mati',      'value' => $summary['mati'],      'bg' => 'bg-red-100',    'text' => 'text-red-700'],
        ] as $card)
            <div class="rounded-lg p-4 {{ $card['bg'] }}">
                <p class="text-2xl font-bold {{ $card['text'] }}">{{ $card['value'] }}</p>
                <p class="text-xs font-semibold mt-0.5 uppercase tracking-wider {{ $card['text'] }} opacity-70">
                    {{ $card['label'] }}
                </p>
            </div>
        @endforeach
    </div>

    {{-- ═══ FILTER BAR ═══ --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <form method="GET" action="{{ route('domba.index') }}" class="flex flex-wrap gap-3 items-end">

            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Ear tag / nama…"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
            </div>

            <div class="w-36">
                <label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                <select name="kategori"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white">
                    <option value="">Semua</option>
                    @foreach (['cempe','dara','indukan','pejantan'] as $k)
                        <option value="{{ $k }}" @selected(request('kategori') === $k)>{{ ucfirst($k) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-36">
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white">
                    <option value="">Semua</option>
                    @foreach (['aktif','karantina','terjual','mati'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                    class="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-md hover:opacity-90 transition">
                Filter
            </button>

            @if(request()->hasAny(['search','kategori','status']))
                <a href="{{ route('domba.index') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-md hover:bg-gray-50 transition">
                    Reset
                </a>
            @endif

            <div class="ml-auto">
                <button type="button"
                        @click="openTambah()"
                        class="px-5 py-2 bg-primary text-white text-sm font-semibold rounded-md hover:opacity-90 transition shadow-sm">
                    + Tambah Domba
                </button>
            </div>
        </form>
    </div>

    {{-- ═══ TABEL DATA ═══ --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">
                            Ear Tag
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Nama
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-20">
                            JK
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Ras
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">
                            Kategori
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">
                            Status
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Kandang
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                            Bobot (kg)
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($dombas as $domba)
                        <tr class="hover:bg-gray-50 transition-colors cursor-pointer"
                            @click="openDetail('{{ $domba->ear_tag_id }}')">

                            <td class="px-4 py-3 font-mono font-semibold text-gray-800">
                                {{ $domba->ear_tag_id }}
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $domba->nama ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $domba->jenis_kelamin === 'jantan' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                    {{ $domba->jenis_kelamin === 'jantan' ? '♂ J' : '♀ B' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $domba->ras }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ ucfirst($domba->kategori) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusCls = match($domba->status) {
                                        'aktif'     => 'bg-green-100 text-primary',
                                        'karantina' => 'bg-yellow-100 text-yellow-700',
                                        'terjual'   => 'bg-blue-100 text-blue-700',
                                        'mati'      => 'bg-red-100 text-red-700',
                                        default     => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusCls }}">
                                    {{ ucfirst($domba->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $domba->kandang?->nama_kandang ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800">
                                {{ $domba->bobot_terakhir ? number_format($domba->bobot_terakhir, 1) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center" @click.stop>
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button"
                                            @click="openEdit('{{ $domba->ear_tag_id }}')"
                                            class="text-xs text-primary hover:underline font-semibold px-1 py-0.5">
                                        Edit
                                    </button>
                                    @if(auth()->user()?->role === 'super_admin')
                                        <span class="text-gray-300">·</span>
                                        <button type="button"
                                                @click="openHapus('{{ $domba->ear_tag_id }}')"
                                                class="text-xs text-accent hover:underline font-semibold px-1 py-0.5">
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-400 text-sm">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p>Belum ada data domba.</p>
                                    <button type="button" @click="openTambah()"
                                            class="text-xs text-primary font-semibold hover:underline">
                                        + Tambah domba pertama
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($dombas->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $dombas->links() }}
            </div>
        @endif
    </div>

</div>{{-- /x-data parent --}}

@endsection
