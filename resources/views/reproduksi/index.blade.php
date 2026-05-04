@extends('layouts.app')
@section('page-title', 'Reproduksi')

@section('content')
<div class="-m-8 bg-[#F8F9F5]"
     x-data="reproduksiApp()"
     x-init="init()">

    {{-- ── Flash Messages ────────────────────────────────────────────────── --}}
    @if(session('success'))
    <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3 text-green-800 text-sm font-medium">
        <span class="material-symbols-outlined text-green-500">check_circle</span>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3 text-red-800 text-sm font-medium">
        <span class="material-symbols-outlined text-red-500">error</span>
        {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ── Page Header ──────────────────────────────────────────────────── --}}
    <div class="px-6 pt-6 pb-2 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-black text-on-surface">Reproduksi &amp; Pembiakan</h1>
            <p class="text-xs text-outline mt-0.5">Kamus Data: tabel PERKAWINAN, KELAHIRAN, ANAK_LAHIR — FR-7.1, FR-7.2, FR-7.3</p>
        </div>
        <button @click="showCreatePerkawinan = true"
                class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-xl shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all active:scale-95">
            <span class="text-lg leading-none">+</span>
            Catat Perkawinan
        </button>
    </div>

    {{-- ── KPI Cards ────────────────────────────────────────────────────── --}}
    <div class="px-6 py-5 grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Card 1: Kebuntingan Aktif --}}
        <div class="bg-white rounded-xl border border-surface-variant p-5 shadow-sm flex flex-col gap-1">
            <p class="text-[10px] font-bold text-outline uppercase tracking-widest">Kebuntingan Aktif</p>
            <p class="text-4xl font-black text-on-surface mt-1">{{ $kpiBunting }}</p>
            <p class="text-xs text-on-surface-variant">indukan sedang bunting</p>
            <p class="text-[10px] text-outline mt-auto pt-2">Kamus Data: ENUM status=&#39;bunting&#39; — PERKAWINAN</p>
        </div>

        {{-- Card 2: HPL dalam 14 Hari --}}
        <div class="bg-white rounded-xl border border-surface-variant p-5 shadow-sm flex flex-col gap-1">
            <p class="text-[10px] font-bold text-outline uppercase tracking-widest">HPL Dalam 14 Hari</p>
            <p class="text-4xl font-black text-on-surface mt-1">{{ $kpiHpl14 }}</p>
            <p class="text-xs text-on-surface-variant">indukan segera melahirkan</p>
            <button @click="activeTab = 'kebuntingan'"
                    class="text-xs font-bold text-primary hover:underline text-left mt-1">
                Lihat HPL →
            </button>
            <p class="text-[10px] text-outline mt-auto pt-1">FR-7.2 — tabel PERKAWINAN kolom estimasi_lahir</p>
        </div>

        {{-- Card 3: Kelahiran Bulan Ini --}}
        <div class="bg-white rounded-xl border border-surface-variant p-5 shadow-sm flex flex-col gap-1">
            <p class="text-[10px] font-bold text-outline uppercase tracking-widest">Kelahiran Bulan Ini</p>
            <p class="text-4xl font-black text-on-surface mt-1">{{ $kpiKelahiranBulan }}</p>
            <p class="text-xs text-on-surface-variant">kejadian kelahiran</p>
            <p class="text-xs font-bold text-on-surface">Anak lahir hidup: {{ $kpiAnakHidup }} ekor</p>
            <p class="text-[10px] text-outline mt-auto pt-1">Kamus Data: tabel KELAHIRAN — tanggal_kelahiran</p>
        </div>

        {{-- Card 4: Tingkat Keberhasilan --}}
        <div class="bg-white rounded-xl border border-surface-variant p-5 shadow-sm flex flex-col gap-1">
            <p class="text-[10px] font-bold text-outline uppercase tracking-widest">Tingkat Keberhasilan</p>
            <p class="text-4xl font-black text-on-surface mt-1">{{ $kpiSuccessRate }}%</p>
            {{-- Progress bar --}}
            <div class="w-full h-1.5 bg-surface-container-high rounded-full overflow-hidden my-1">
                <div class="h-1.5 bg-on-surface rounded-full transition-all"
                     style="width: {{ $kpiSuccessRate }}%"></div>
            </div>
            <p class="text-xs text-on-surface-variant">
                Lahir: <span class="font-bold text-on-surface">{{ $kpiLahirTotal }}</span>
                &nbsp;|&nbsp;
                Gagal: <span class="font-bold text-on-surface">{{ $kpiGagalTotal }}</span>
                &nbsp;|&nbsp;
                Proses: <span class="font-bold text-on-surface">{{ $kpiProsesTotal }}</span>
            </p>
            <p class="text-[10px] text-outline mt-auto pt-1">Kamus Data: ENUM status — PERKAWINAN</p>
        </div>

    </div>

    {{-- ── Tab Navigation ───────────────────────────────────────────────── --}}
    <div class="px-6 mb-0">
        <div class="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit">
            <button @click="activeTab = 'perkawinan'"
                    :class="activeTab === 'perkawinan' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="px-5 py-2 rounded-lg text-sm font-semibold transition-all">
                Perkawinan
            </button>
            <button @click="activeTab = 'kebuntingan'"
                    :class="activeTab === 'kebuntingan' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="px-5 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-1.5">
                Kebuntingan &amp; HPL
                @if($kpiBunting > 0)
                <span class="px-1.5 py-0.5 bg-primary text-white text-[10px] font-bold rounded-full">{{ $kpiBunting }}</span>
                @endif
            </button>
            <button @click="activeTab = 'kelahiran'"
                    :class="activeTab === 'kelahiran' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="px-5 py-2 rounded-lg text-sm font-semibold transition-all">
                Kelahiran
            </button>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 1: PERKAWINAN                                                   --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'perkawinan'" x-cloak class="p-6 space-y-4">

        {{-- Filter Bar --}}
        <form method="GET" action="{{ route('reproduksi.index') }}" class="flex flex-wrap gap-3">
            <input type="hidden" name="tab" value="perkawinan">
            <div class="relative flex-1 min-w-[180px]">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">search</span>
                <input type="text" name="pk_search" value="{{ request('pk_search') }}"
                       placeholder="Cari ear tag, metode..."
                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
            <select name="pk_status"
                    class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">Semua Status</option>
                <option value="menunggu_konfirmasi" {{ request('pk_status') === 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                <option value="bunting"             {{ request('pk_status') === 'bunting'             ? 'selected' : '' }}>Bunting</option>
                <option value="tidak_bunting"       {{ request('pk_status') === 'tidak_bunting'       ? 'selected' : '' }}>Tidak Bunting</option>
                <option value="lahir"               {{ request('pk_status') === 'lahir'               ? 'selected' : '' }}>Lahir</option>
                <option value="gagal"               {{ request('pk_status') === 'gagal'               ? 'selected' : '' }}>Gagal</option>
            </select>
            <select name="pk_metode"
                    class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">Semua Metode</option>
                <option value="alami"             {{ request('pk_metode') === 'alami'             ? 'selected' : '' }}>Alami</option>
                <option value="inseminasi_buatan" {{ request('pk_metode') === 'inseminasi_buatan' ? 'selected' : '' }}>Inseminasi Buatan</option>
            </select>
            <button type="submit"
                    class="px-5 py-2.5 bg-gray-900 text-white font-bold text-sm rounded-xl hover:bg-gray-700 transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['pk_search','pk_status','pk_metode']))
            <a href="{{ route('reproduksi.index') }}?tab=perkawinan"
               class="px-4 py-2.5 border border-gray-300 text-gray-600 font-semibold text-sm rounded-xl hover:bg-gray-50 transition-colors">
                Reset
            </a>
            @endif
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pejantan</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Indukan</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tgl Kawin</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Metode</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Est. HPL</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($perkawinan as $p)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-gray-500">KWN-{{ $p->kawin_id }}</td>
                            <td class="px-4 py-3 font-bold text-primary">{{ $p->pejantan_id }}</td>
                            <td class="px-4 py-3 font-bold text-secondary">{{ $p->indukan_id }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($p->tanggal_perkawinan)->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                @if($p->metode === 'alami')
                                <span class="px-2 py-0.5 bg-green-50 text-green-700 text-[10px] font-bold rounded-full">Alami</span>
                                @else
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-full">IB</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600 text-xs">
                                {{ $p->estimasi_lahir ? \Carbon\Carbon::parse($p->estimasi_lahir)->format('d M Y') : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                $statusMap = [
                                    'menunggu_konfirmasi' => ['label' => 'Menunggu', 'class' => 'bg-amber-50 text-amber-700'],
                                    'bunting'             => ['label' => 'Bunting',   'class' => 'bg-green-50 text-green-700'],
                                    'tidak_bunting'       => ['label' => 'Tdk Bunting', 'class' => 'bg-pink-50 text-pink-700'],
                                    'lahir'               => ['label' => 'Lahir',     'class' => 'bg-gray-100 text-gray-700'],
                                    'gagal'               => ['label' => 'Gagal',     'class' => 'bg-red-50 text-red-700'],
                                ];
                                $s = $statusMap[$p->status] ?? ['label' => $p->status, 'class' => 'bg-gray-100 text-gray-600'];
                                @endphp
                                <span class="px-2 py-0.5 {{ $s['class'] }} text-[10px] font-bold rounded-full">{{ $s['label'] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($p->status === 'menunggu_konfirmasi')
                                    <button @click="openKonfirmasi({
                                                kawin_id: {{ $p->kawin_id }},
                                                pejantan_id: '{{ $p->pejantan_id }}',
                                                indukan_id: '{{ $p->indukan_id }}',
                                                tanggal_perkawinan: '{{ $p->tanggal_perkawinan }}',
                                                estimasi_lahir: '{{ $p->estimasi_lahir ?? '' }}'
                                            })"
                                            class="flex items-center gap-1 px-2.5 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold rounded-lg hover:bg-amber-100 transition-colors">
                                        <span class="material-symbols-outlined text-sm">check_circle</span> Konfirmasi
                                    </button>
                                    @endif
                                    <button @click="openEditPerkawinan({
                                                kawin_id: {{ $p->kawin_id }},
                                                pejantan_id: '{{ $p->pejantan_id }}',
                                                pejantan_ras: '{{ $p->pejantan_ras ?? '' }}',
                                                indukan_id: '{{ $p->indukan_id }}',
                                                indukan_ras: '{{ $p->indukan_ras ?? '' }}',
                                                tanggal_perkawinan: '{{ $p->tanggal_perkawinan }}',
                                                metode: '{{ $p->metode }}',
                                                status: '{{ $p->status }}'
                                            })"
                                            class="p-1.5 text-gray-400 hover:bg-gray-100 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-400 text-sm">
                                <span class="material-symbols-outlined text-4xl block mb-2 text-gray-200">favorite_border</span>
                                Belum ada data perkawinan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($perkawinan->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $perkawinan->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 2: KEBUNTINGAN & HPL                                            --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'kebuntingan'" x-cloak class="p-6 space-y-5">

        {{-- Alert: HPL Kritis --}}
        @php $kritis = $kebuntingan->where('alert', 'kritis'); @endphp
        @if($kritis->count() > 0)
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3">
            <span class="material-symbols-outlined text-red-500 mt-0.5" style="font-variation-settings:'FILL' 1">emergency</span>
            <div>
                <p class="text-sm font-bold text-red-800">{{ $kritis->count() }} Indukan HPL Kritis (≤ 3 hari)!</p>
                <p class="text-xs text-red-600 mt-0.5">
                    @foreach($kritis as $k) <strong>{{ $k->indukan_id }}</strong> ({{ $k->hari_tersisa }} hari){{ !$loop->last ? ', ' : '' }} @endforeach
                </p>
            </div>
        </div>
        @endif

        {{-- Alert Cards Grid --}}
        @if($kebuntingan->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($kebuntingan as $k)
            @php
            $alertClass = match($k->alert) {
                'kritis'   => 'border-red-300 bg-red-50',
                'warning'  => 'border-amber-300 bg-amber-50',
                default    => 'border-gray-200 bg-white',
            };
            $badgeClass = match($k->alert) {
                'kritis'  => 'bg-red-100 text-red-700',
                'warning' => 'bg-amber-100 text-amber-700',
                default   => 'bg-gray-100 text-gray-600',
            };
            $progressColor = match($k->alert) {
                'kritis'  => 'bg-red-500',
                'warning' => 'bg-amber-400',
                default   => 'bg-primary',
            };
            @endphp
            <div class="p-4 border-2 {{ $alertClass }} rounded-2xl space-y-3 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Indukan</p>
                        <p class="text-lg font-black text-secondary">{{ $k->indukan_id }}</p>
                    </div>
                    <span class="px-2 py-1 {{ $badgeClass }} text-[10px] font-bold rounded-lg">
                        {{ $k->hari_tersisa }} hari lagi
                    </span>
                </div>
                <div class="flex gap-3 text-xs text-gray-600">
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Pejantan</p>
                        <p class="font-semibold text-primary">{{ $k->pejantan_id }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Tgl Kawin</p>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($k->tanggal_perkawinan)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Est. HPL</p>
                        <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($k->estimasi_lahir)->format('d M Y') }}</p>
                    </div>
                </div>
                {{-- Progress Bar --}}
                <div>
                    <div class="flex justify-between text-[10px] text-gray-400 mb-1">
                        <span>Progress Kebuntingan</span>
                        <span>{{ round($k->progress) }}%</span>
                    </div>
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="{{ $progressColor }} h-2 rounded-full transition-all" style="width: {{ min(100, $k->progress) }}%"></div>
                    </div>
                </div>
                <button @click="openCreateKelahiran({
                            kawin_id: {{ $k->kawin_id }},
                            pejantan_id: '{{ $k->pejantan_id }}',
                            indukan_id: '{{ $k->indukan_id }}',
                            estimasi_lahir: '{{ $k->estimasi_lahir }}'
                        })"
                        class="w-full flex items-center justify-center gap-2 py-2 bg-primary/10 text-primary font-bold text-xs rounded-xl hover:bg-primary/20 transition-colors">
                    <span class="material-symbols-outlined text-sm">child_care</span> Catat Kelahiran
                </button>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center text-gray-400">
            <span class="material-symbols-outlined text-5xl block mb-3 text-gray-200">pregnant_woman</span>
            <p class="text-sm font-semibold">Tidak ada indukan yang sedang bunting</p>
        </div>
        @endif

        {{-- Full Kebuntingan Table --}}
        @if($kebuntingan->count() > 0)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-sm">Daftar Kebuntingan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase">Indukan</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase">Pejantan</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase">Est. HPL</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase">Sisa Hari</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase">Progress</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-400 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($kebuntingan as $k)
                        @php
                        $rowBadge = match($k->alert) {
                            'kritis'  => 'bg-red-50 text-red-700',
                            'warning' => 'bg-amber-50 text-amber-700',
                            default   => 'bg-green-50 text-green-700',
                        };
                        $bar = match($k->alert) {
                            'kritis'  => 'bg-red-500',
                            'warning' => 'bg-amber-400',
                            default   => 'bg-primary',
                        };
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-gray-500">KWN-{{ $k->kawin_id }}</td>
                            <td class="px-4 py-3 font-bold text-secondary">{{ $k->indukan_id }}</td>
                            <td class="px-4 py-3 font-bold text-primary">{{ $k->pejantan_id }}</td>
                            <td class="px-4 py-3 text-xs text-gray-700 font-semibold">{{ \Carbon\Carbon::parse($k->estimasi_lahir)->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 {{ $rowBadge }} text-[10px] font-bold rounded-full">
                                    {{ $k->hari_tersisa }} hari
                                </span>
                            </td>
                            <td class="px-4 py-3 w-32">
                                <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="{{ $bar }} h-1.5 rounded-full" style="width: {{ min(100, $k->progress) }}%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button @click="openCreateKelahiran({
                                            kawin_id: {{ $k->kawin_id }},
                                            pejantan_id: '{{ $k->pejantan_id }}',
                                            indukan_id: '{{ $k->indukan_id }}',
                                            estimasi_lahir: '{{ $k->estimasi_lahir }}'
                                        })"
                                        class="flex items-center gap-1 px-2.5 py-1.5 bg-primary/10 text-primary text-xs font-bold rounded-lg hover:bg-primary/20 transition-colors">
                                    <span class="material-symbols-outlined text-sm">child_care</span> Catat Kelahiran
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 3: KELAHIRAN                                                    --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'kelahiran'" x-cloak class="p-6 space-y-5">

        {{-- Stats Mini Cards --}}
        @php
            $totalAnak    = ($lhrStats->total_hidup ?? 0) + ($lhrStats->total_mati ?? 0);
            $tingkatHidup = $totalAnak > 0 ? round(($lhrStats->total_hidup / $totalAnak) * 100) : 0;
        @endphp
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm text-center">
                <p class="text-[10px] font-bold text-gray-400 uppercase">Bulan Ini</p>
                <p class="text-2xl font-black text-gray-900">{{ $lhrStats->total_kasus ?? 0 }}</p>
                <p class="text-[10px] text-gray-400">kasus kelahiran</p>
            </div>
            <div class="bg-white rounded-xl border border-green-100 p-4 shadow-sm text-center">
                <p class="text-[10px] font-bold text-green-500 uppercase">Anak Hidup</p>
                <p class="text-2xl font-black text-primary">{{ $lhrStats->total_hidup ?? 0 }}</p>
                <p class="text-[10px] text-gray-400">ekor bulan ini</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm text-center">
                <p class="text-[10px] font-bold text-gray-400 uppercase">Tingkat Hidup</p>
                <p class="text-2xl font-black text-gray-900">{{ $tingkatHidup }}%</p>
                <p class="text-[10px] text-gray-400">survival rate</p>
            </div>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('reproduksi.index') }}" class="flex gap-3">
            <input type="hidden" name="tab" value="kelahiran">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">search</span>
                <input type="text" name="lhr_search" value="{{ request('lhr_search') }}"
                       placeholder="Cari ear tag indukan/pejantan..."
                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-gray-900 text-white font-bold text-sm rounded-xl hover:bg-gray-700 transition-colors">Cari</button>
            @if(request('lhr_search'))
            <a href="{{ route('reproduksi.index') }}?tab=kelahiran" class="px-4 py-2.5 border border-gray-300 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50">Reset</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase">Pasangan</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase">Tgl Lahir</th>
                            <th class="px-4 py-3 text-center text-[10px] font-bold text-gray-400 uppercase">Hidup</th>
                            <th class="px-4 py-3 text-center text-[10px] font-bold text-gray-400 uppercase">Mati</th>
                            <th class="px-4 py-3 text-center text-[10px] font-bold text-gray-400 uppercase">Bobot Rata-rata</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-400 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($kelahiran as $l)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-gray-500">LHR-{{ $l->lahir_id }}</td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-primary">{{ $l->pejantan_id ?? '—' }}</span>
                                <span class="text-gray-400 mx-1">×</span>
                                <span class="font-bold text-secondary">{{ $l->indukan_id ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($l->tanggal_kelahiran)->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 bg-green-50 text-green-700 text-xs font-bold rounded-full">{{ $l->jml_anak_hidup }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($l->jml_anak_mati > 0)
                                <span class="px-2 py-0.5 bg-red-50 text-red-600 text-xs font-bold rounded-full">{{ $l->jml_anak_mati }}</span>
                                @else
                                <span class="text-gray-300 text-xs">0</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 text-xs font-semibold">
                                {{ $l->bobot_rata_rata ? number_format($l->bobot_rata_rata, 2) . ' kg' : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Expandable anak_lahir toggle --}}
                                    @if(isset($anakLahirMap[$l->lahir_id]) && count($anakLahirMap[$l->lahir_id]) > 0)
                                    <button @click="toggleAnakRows({{ $l->lahir_id }})"
                                            class="p-1.5 text-gray-400 hover:bg-gray-100 rounded-lg transition-colors"
                                            :title="expandedLahir.includes({{ $l->lahir_id }}) ? 'Sembunyikan detail' : 'Lihat detail anak'">
                                        <span class="material-symbols-outlined text-lg"
                                              x-text="expandedLahir.includes({{ $l->lahir_id }}) ? 'expand_less' : 'expand_more'">expand_more</span>
                                    </button>
                                    @endif
                                    <button @click="openEditKelahiran({
                                                lahir_id: {{ $l->lahir_id }},
                                                kawin_id: {{ $l->kawin_id }},
                                                pejantan_id: '{{ $l->pejantan_id ?? '' }}',
                                                indukan_id: '{{ $l->indukan_id ?? '' }}',
                                                tanggal_kelahiran: '{{ $l->tanggal_kelahiran }}',
                                                jml_anak_hidup: {{ $l->jml_anak_hidup }},
                                                jml_anak_mati: {{ $l->jml_anak_mati }},
                                                bobot_rata_rata: '{{ $l->bobot_rata_rata ?? '' }}',
                                                catatan: '{{ addslashes($l->catatan ?? '') }}'
                                            })"
                                            class="p-1.5 text-gray-400 hover:bg-gray-100 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        {{-- Expandable anak_lahir rows --}}
                        @if(isset($anakLahirMap[$l->lahir_id]) && count($anakLahirMap[$l->lahir_id]) > 0)
                        <tr x-show="expandedLahir.includes({{ $l->lahir_id }})" x-cloak>
                            <td colspan="7" class="px-4 pb-3">
                                <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                                    <table class="w-full text-xs">
                                        <thead>
                                            <tr class="bg-gray-100">
                                                <th class="px-3 py-2 text-left text-[10px] font-bold text-gray-400 uppercase">Anak #</th>
                                                <th class="px-3 py-2 text-left text-[10px] font-bold text-gray-400 uppercase">Ear Tag</th>
                                                <th class="px-3 py-2 text-left text-[10px] font-bold text-gray-400 uppercase">Jenis Kelamin</th>
                                                <th class="px-3 py-2 text-left text-[10px] font-bold text-gray-400 uppercase">Bobot</th>
                                                <th class="px-3 py-2 text-left text-[10px] font-bold text-gray-400 uppercase">Kondisi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($anakLahirMap[$l->lahir_id] as $i => $anak)
                                            <tr>
                                                <td class="px-3 py-2 text-gray-500">#{{ $i + 1 }}</td>
                                                <td class="px-3 py-2 font-mono font-bold text-primary">{{ $anak->ear_tag_id ?? '—' }}</td>
                                                <td class="px-3 py-2">
                                                    <span class="px-1.5 py-0.5 {{ $anak->jenis_kelamin === 'jantan' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }} font-bold rounded-full">
                                                        {{ ucfirst($anak->jenis_kelamin) }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 text-gray-600">{{ $anak->bobot_lahir ? number_format($anak->bobot_lahir, 2) . ' kg' : '—' }}</td>
                                                <td class="px-3 py-2">
                                                    <span class="px-1.5 py-0.5 {{ $anak->kondisi === 'hidup' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }} font-bold rounded-full">
                                                        {{ ucfirst($anak->kondisi) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">
                                <span class="material-symbols-outlined text-4xl block mb-2 text-gray-200">child_care</span>
                                Belum ada data kelahiran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($kelahiran->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $kelahiran->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- ── Modal Partials (harus di dalam x-data scope) ───────────────── --}}
    @include('reproduksi.partials.modal-create-perkawinan')
    @include('reproduksi.partials.modal-edit-perkawinan')
    @include('reproduksi.partials.modal-konfirmasi')
    @include('reproduksi.partials.modal-create-kelahiran')
    @include('reproduksi.partials.modal-edit-kelahiran')

</div>

@endsection

@push('scripts')
<script>
const pejantanList  = @json($pejantanList);
const indukanList   = @json($indukanList);

function reproduksiApp() {
    return {
        activeTab: '{{ request("tab", "perkawinan") }}',

        // ── Modal visibility ───────────────────────────────────────────
        showCreatePerkawinan: false,
        showEditPerkawinan:   false,
        showKonfirmasi:       false,
        showCreateKelahiran:  false,
        showEditKelahiran:    false,

        // ── Create Perkawinan state ────────────────────────────────────
        pejantanSearch: '',
        selectedPejantan: null,
        indukanSearch: '',
        selectedIndukanList: [],
        tanggalKawin: '',
        hplText: '',

        // ── Edit Perkawinan state ──────────────────────────────────────
        editData: {},

        // ── Konfirmasi state ───────────────────────────────────────────
        konfirmasiData: {},

        // ── Kelahiran state ────────────────────────────────────────────
        kelahiranData: {},
        kelahiranAnakHidup: 1,
        kelahiranAnakMati: 0,
        anakRows: [{}],

        // ── Edit Kelahiran state ───────────────────────────────────────
        editKelahiranData: {},

        // ── Expanded anak_lahir rows (kelahiran tab) ───────────────────
        expandedLahir: [],

        // ── Computed ──────────────────────────────────────────────────
        get filteredPejantan() {
            if (!this.pejantanSearch) return pejantanList;
            const q = this.pejantanSearch.toLowerCase();
            return pejantanList.filter(p =>
                p.ear_tag_id.toLowerCase().includes(q) ||
                (p.nama && p.nama.toLowerCase().includes(q))
            );
        },
        get filteredIndukan() {
            if (!this.indukanSearch) return indukanList;
            const q = this.indukanSearch.toLowerCase();
            return indukanList.filter(i =>
                i.ear_tag_id.toLowerCase().includes(q) ||
                (i.nama && i.nama.toLowerCase().includes(q))
            );
        },
        get previewRecords() {
            return this.selectedIndukanList.map(i => ({ indukan: i.ear_tag_id }));
        },

        // ── Methods ───────────────────────────────────────────────────
        init() {},

        calcHPL() {
            if (!this.tanggalKawin) { this.hplText = ''; return; }
            const d = new Date(this.tanggalKawin + 'T00:00:00');
            d.setDate(d.getDate() + 150);
            this.hplText = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        },

        addIndukan(item) {
            if (!this.selectedIndukanList.find(s => s.ear_tag_id === item.ear_tag_id)) {
                this.selectedIndukanList.push(item);
            }
            this.indukanSearch = '';
        },
        removeIndukan(earTagId) {
            this.selectedIndukanList = this.selectedIndukanList.filter(s => s.ear_tag_id !== earTagId);
        },

        openEditPerkawinan(data) {
            this.editData = data;
            this.showEditPerkawinan = true;
        },

        openKonfirmasi(data) {
            this.konfirmasiData = data;
            this.showKonfirmasi = true;
        },

        openCreateKelahiran(data) {
            this.kelahiranData = data;
            this.kelahiranAnakHidup = 1;
            this.kelahiranAnakMati = 0;
            this.anakRows = [{}];
            this.showCreateKelahiran = true;
        },

        openEditKelahiran(data) {
            this.editKelahiranData = data;
            this.showEditKelahiran = true;
        },

        syncAnakRows() {
            const n = parseInt(this.kelahiranAnakHidup) || 0;
            while (this.anakRows.length < n) this.anakRows.push({});
            this.anakRows = this.anakRows.slice(0, n);
        },

        toggleAnakRows(lahirId) {
            const idx = this.expandedLahir.indexOf(lahirId);
            if (idx === -1) this.expandedLahir.push(lahirId);
            else this.expandedLahir.splice(idx, 1);
        },
    };
}
</script>
@endpush
