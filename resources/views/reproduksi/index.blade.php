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
        @php $buntingBadge = $kpiBunting > 0 ? ['label' => 'AKTIF', 'class' => 'bg-primary-container text-on-primary-container'] : ['label' => 'NIHIL', 'class' => 'bg-surface-container text-outline']; @endphp
        <div class="bg-white rounded-xl border border-outline-variant/30 p-5 shadow-sm flex flex-col gap-1">
            <div class="flex items-start justify-between">
                <p class="text-[11px] font-semibold text-outline uppercase tracking-wider">Kebuntingan Aktif</p>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $buntingBadge['class'] }}">{{ $buntingBadge['label'] }}</span>
            </div>
            <p class="text-3xl font-black text-on-surface leading-none mt-2">{{ $kpiBunting }}</p>
            <p class="text-xs text-on-surface-variant mt-1">indukan sedang bunting</p>
        </div>

        {{-- Card 2: HPL dalam 14 Hari --}}
        @php $hplBadge = $kpiHpl14 > 0 ? ['label' => 'SEGERA', 'class' => 'bg-amber-100 text-amber-700'] : ['label' => 'AMAN', 'class' => 'bg-secondary-container text-on-secondary-container']; @endphp
        <div class="bg-white rounded-xl border border-outline-variant/30 p-5 shadow-sm flex flex-col gap-1">
            <div class="flex items-start justify-between">
                <p class="text-[11px] font-semibold text-outline uppercase tracking-wider">HPL Dalam 14 Hari</p>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $hplBadge['class'] }}">{{ $hplBadge['label'] }}</span>
            </div>
            <p class="text-3xl font-black text-on-surface leading-none mt-2">{{ $kpiHpl14 }}</p>
            <p class="text-xs text-on-surface-variant mt-1">indukan segera melahirkan</p>
            <button @click="activeTab = 'kebuntingan'"
                    class="text-xs font-bold text-primary hover:underline text-left mt-1">
                Lihat HPL →
            </button>
        </div>

        {{-- Card 3: Kelahiran Bulan Ini --}}
        <div class="bg-white rounded-xl border border-outline-variant/30 p-5 shadow-sm flex flex-col gap-1">
            <div class="flex items-start justify-between">
                <p class="text-[11px] font-semibold text-outline uppercase tracking-wider">Kelahiran Bulan Ini</p>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-surface-container text-outline">{{ now()->isoFormat('MMM YYYY') }}</span>
            </div>
            <p class="text-3xl font-black text-on-surface leading-none mt-2">{{ $kpiKelahiranBulan }}</p>
            <div class="flex justify-between items-center text-[10px] text-outline uppercase tracking-wider mt-2">
                <span>Anak hidup</span>
                <span class="font-bold text-on-surface">{{ $kpiAnakHidup }} ekor</span>
            </div>
        </div>

        {{-- Card 4: Tingkat Keberhasilan --}}
        @php
            $rateBadge = $kpiSuccessRate >= 70
                ? ['label' => 'BAIK', 'class' => 'bg-secondary-container text-on-secondary-container']
                : ($kpiSuccessRate >= 40
                    ? ['label' => 'SEDANG', 'class' => 'bg-amber-100 text-amber-700']
                    : ['label' => 'RENDAH', 'class' => 'bg-error-container text-on-error-container']);
            $rateBar = $kpiSuccessRate >= 70 ? 'bg-primary' : ($kpiSuccessRate >= 40 ? 'bg-amber-400' : 'bg-error');
        @endphp
        <div class="bg-white rounded-xl border border-outline-variant/30 p-5 shadow-sm flex flex-col gap-1">
            <div class="flex items-start justify-between">
                <p class="text-[11px] font-semibold text-outline uppercase tracking-wider">Tingkat Keberhasilan</p>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $rateBadge['class'] }}">{{ $rateBadge['label'] }}</span>
            </div>
            <p class="text-3xl font-black text-on-surface leading-none mt-2">{{ $kpiSuccessRate }}<span class="text-sm font-medium text-outline">%</span></p>
            <div class="flex justify-between items-center text-[10px] text-outline uppercase tracking-wider mt-2">
                <span>Keberhasilan kawin</span>
                <span>{{ $kpiLahirTotal }} / {{ $kpiLahirTotal + $kpiGagalTotal }}</span>
            </div>
            <div class="w-full h-1.5 bg-surface-container-high rounded-full overflow-hidden">
                <div class="h-1.5 rounded-full transition-all {{ $rateBar }}"
                     style="width: {{ $kpiSuccessRate }}%"></div>
            </div>
        </div>

    </div>

    {{-- ── Tab Navigation ───────────────────────────────────────────────── --}}
    <div class="px-6 mb-0">
        <div class="flex gap-1 bg-surface-container p-1 rounded-xl w-fit">
            <button @click="activeTab = 'perkawinan'"
                    :class="activeTab === 'perkawinan' ? 'bg-white text-on-surface shadow-sm' : 'text-on-surface-variant hover:text-on-surface'"
                    class="px-5 py-2 rounded-lg text-sm font-semibold transition-all">
                Perkawinan
            </button>
            <button @click="activeTab = 'kebuntingan'"
                    :class="activeTab === 'kebuntingan' ? 'bg-white text-on-surface shadow-sm' : 'text-on-surface-variant hover:text-on-surface'"
                    class="px-5 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-1.5">
                Kebuntingan &amp; HPL
                @if($kpiBunting > 0)
                <span class="px-1.5 py-0.5 bg-primary text-white text-[10px] font-bold rounded-full">{{ $kpiBunting }}</span>
                @endif
            </button>
            <button @click="activeTab = 'kelahiran'"
                    :class="activeTab === 'kelahiran' ? 'bg-white text-on-surface shadow-sm' : 'text-on-surface-variant hover:text-on-surface'"
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
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
                <input type="text" name="pk_search" value="{{ request('pk_search') }}"
                       placeholder="Cari ear tag, metode..."
                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
            <select name="pk_status"
                    class="px-4 py-2.5 bg-white border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">Semua Status</option>
                <option value="menunggu_konfirmasi" {{ request('pk_status') === 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                <option value="bunting"             {{ request('pk_status') === 'bunting'             ? 'selected' : '' }}>Bunting</option>
                <option value="tidak_bunting"       {{ request('pk_status') === 'tidak_bunting'       ? 'selected' : '' }}>Tidak Bunting</option>
                <option value="lahir"               {{ request('pk_status') === 'lahir'               ? 'selected' : '' }}>Lahir</option>
                <option value="gagal"               {{ request('pk_status') === 'gagal'               ? 'selected' : '' }}>Gagal</option>
            </select>
            <select name="pk_metode"
                    class="px-4 py-2.5 bg-white border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">Semua Metode</option>
                <option value="alami"             {{ request('pk_metode') === 'alami'             ? 'selected' : '' }}>Alami</option>
                <option value="inseminasi_buatan" {{ request('pk_metode') === 'inseminasi_buatan' ? 'selected' : '' }}>Inseminasi Buatan</option>
            </select>
            <button type="submit"
                    class="px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-xl hover:bg-primary/90 transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['pk_search','pk_status','pk_metode']))
            <a href="{{ route('reproduksi.index') }}?tab=perkawinan"
               class="px-4 py-2.5 border border-outline-variant text-on-surface-variant font-semibold text-sm rounded-xl hover:bg-surface-container-low transition-colors">
                Reset
            </a>
            @endif
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-container-low border-b border-outline-variant/30">
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-outline uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-outline uppercase tracking-wider">Pejantan</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-outline uppercase tracking-wider">Indukan</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-outline uppercase tracking-wider">Tgl Kawin</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-outline uppercase tracking-wider">Metode</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-outline uppercase tracking-wider">Est. HPL</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-outline uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-outline uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/20">
                        @forelse($perkawinan as $p)
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-on-surface-variant">KWN-{{ $p->kawin_id }}</td>
                            <td class="px-4 py-3 font-bold text-primary">{{ $p->pejantan_id }}</td>
                            <td class="px-4 py-3 font-bold text-secondary">{{ $p->indukan_id }}</td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ \Carbon\Carbon::parse($p->tanggal_perkawinan)->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                @if($p->metode === 'alami')
                                <span class="px-2 py-0.5 bg-green-50 text-green-700 text-[10px] font-bold rounded-full">Alami</span>
                                @else
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-full">IB</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-on-surface-variant text-xs">
                                {{ $p->estimasi_lahir ? \Carbon\Carbon::parse($p->estimasi_lahir)->format('d M Y') : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                $statusMap = [
                                    'menunggu_konfirmasi' => ['label' => 'Menunggu', 'class' => 'bg-amber-50 text-amber-700'],
                                    'bunting'             => ['label' => 'Bunting',   'class' => 'bg-green-50 text-green-700'],
                                    'tidak_bunting'       => ['label' => 'Tdk Bunting', 'class' => 'bg-pink-50 text-pink-700'],
                                    'lahir'               => ['label' => 'Lahir',     'class' => 'bg-surface-container text-on-surface'],
                                    'gagal'               => ['label' => 'Gagal',     'class' => 'bg-red-50 text-red-700'],
                                ];
                                $s = $statusMap[$p->status] ?? ['label' => $p->status, 'class' => 'bg-surface-container text-on-surface-variant'];
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
                                            class="p-1.5 text-outline hover:bg-surface-container rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-outline text-sm">
                                <span class="material-symbols-outlined text-4xl block mb-2 text-outline-variant">favorite_border</span>
                                Belum ada data perkawinan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($perkawinan->hasPages())
            <div class="px-4 py-3 border-t border-outline-variant/30">
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
                default    => 'border-outline-variant bg-white',
            };
            $badgeClass = match($k->alert) {
                'kritis'  => 'bg-red-100 text-red-700',
                'warning' => 'bg-amber-100 text-amber-700',
                default   => 'bg-surface-container text-on-surface-variant',
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
                        <p class="text-[10px] font-bold text-outline uppercase">Indukan</p>
                        <p class="text-lg font-black text-secondary">{{ $k->indukan_id }}</p>
                    </div>
                    <span class="px-2 py-1 {{ $badgeClass }} text-[10px] font-bold rounded-lg">
                        {{ $k->hari_tersisa }} hari lagi
                    </span>
                </div>
                <div class="flex gap-3 text-xs text-on-surface-variant">
                    <div>
                        <p class="text-[10px] text-outline uppercase font-bold">Pejantan</p>
                        <p class="font-semibold text-primary">{{ $k->pejantan_id }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-outline uppercase font-bold">Tgl Kawin</p>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($k->tanggal_perkawinan)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-outline uppercase font-bold">Est. HPL</p>
                        <p class="font-bold text-on-surface">{{ \Carbon\Carbon::parse($k->estimasi_lahir)->format('d M Y') }}</p>
                    </div>
                </div>
                {{-- Progress Bar --}}
                <div>
                    <div class="flex justify-between text-[10px] text-outline mb-1">
                        <span>Progress Kebuntingan</span>
                        <span>{{ round($k->progress) }}%</span>
                    </div>
                    <div class="w-full h-2 bg-surface-container-high rounded-full overflow-hidden">
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
        <div class="bg-white rounded-2xl border border-outline-variant/30 p-12 text-center text-outline">
            <span class="material-symbols-outlined text-5xl block mb-3 text-outline-variant">pregnant_woman</span>
            <p class="text-sm font-semibold">Tidak ada indukan yang sedang bunting</p>
        </div>
        @endif

        {{-- Full Kebuntingan Table --}}
        @if($kebuntingan->count() > 0)
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-outline-variant/30">
                <h3 class="font-bold text-on-surface text-sm">Daftar Kebuntingan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-container-low border-b border-outline-variant/30">
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-outline uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-outline uppercase">Indukan</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-outline uppercase">Pejantan</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-outline uppercase">Est. HPL</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-outline uppercase">Sisa Hari</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-outline uppercase">Progress</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-outline uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/20">
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
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-on-surface-variant">KWN-{{ $k->kawin_id }}</td>
                            <td class="px-4 py-3 font-bold text-secondary">{{ $k->indukan_id }}</td>
                            <td class="px-4 py-3 font-bold text-primary">{{ $k->pejantan_id }}</td>
                            <td class="px-4 py-3 text-xs text-on-surface font-semibold">{{ \Carbon\Carbon::parse($k->estimasi_lahir)->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 {{ $rowBadge }} text-[10px] font-bold rounded-full">
                                    {{ $k->hari_tersisa }} hari
                                </span>
                            </td>
                            <td class="px-4 py-3 w-32">
                                <div class="w-full h-1.5 bg-surface-container-high rounded-full overflow-hidden">
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
            <div class="bg-white rounded-xl border border-outline-variant/30 p-4 shadow-sm text-center">
                <p class="text-[10px] font-bold text-outline uppercase">Bulan Ini</p>
                <p class="text-2xl font-black text-on-surface">{{ $lhrStats->total_kasus ?? 0 }}</p>
                <p class="text-[10px] text-outline">kasus kelahiran</p>
            </div>
            <div class="bg-white rounded-xl border border-green-100 p-4 shadow-sm text-center">
                <p class="text-[10px] font-bold text-green-500 uppercase">Anak Hidup</p>
                <p class="text-2xl font-black text-primary">{{ $lhrStats->total_hidup ?? 0 }}</p>
                <p class="text-[10px] text-outline">ekor bulan ini</p>
            </div>
            <div class="bg-white rounded-xl border border-outline-variant/30 p-4 shadow-sm text-center">
                <p class="text-[10px] font-bold text-outline uppercase">Tingkat Hidup</p>
                <p class="text-2xl font-black text-on-surface">{{ $tingkatHidup }}%</p>
                <p class="text-[10px] text-outline">survival rate</p>
            </div>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('reproduksi.index') }}" class="flex gap-3">
            <input type="hidden" name="tab" value="kelahiran">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
                <input type="text" name="lhr_search" value="{{ request('lhr_search') }}"
                       placeholder="Cari ear tag indukan/pejantan..."
                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline-variant rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"/>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-xl hover:bg-primary/90 transition-colors">Cari</button>
            @if(request('lhr_search'))
            <a href="{{ route('reproduksi.index') }}?tab=kelahiran" class="px-4 py-2.5 border border-outline-variant text-on-surface-variant text-sm font-semibold rounded-xl hover:bg-surface-container-low">Reset</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-container-low border-b border-outline-variant/30">
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-outline uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-outline uppercase">Pasangan</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-outline uppercase">Tgl Lahir</th>
                            <th class="px-4 py-3 text-center text-[10px] font-bold text-outline uppercase">Hidup</th>
                            <th class="px-4 py-3 text-center text-[10px] font-bold text-outline uppercase">Mati</th>
                            <th class="px-4 py-3 text-center text-[10px] font-bold text-outline uppercase">Bobot Rata-rata</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-outline uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/20">
                        @forelse($kelahiran as $l)
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-on-surface-variant">LHR-{{ $l->lahir_id }}</td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-primary">{{ $l->pejantan_id ?? '—' }}</span>
                                <span class="text-outline mx-1">×</span>
                                <span class="font-bold text-secondary">{{ $l->indukan_id ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 text-on-surface-variant">{{ \Carbon\Carbon::parse($l->tanggal_kelahiran)->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 bg-green-50 text-green-700 text-xs font-bold rounded-full">{{ $l->jml_anak_hidup }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($l->jml_anak_mati > 0)
                                <span class="px-2 py-0.5 bg-red-50 text-red-600 text-xs font-bold rounded-full">{{ $l->jml_anak_mati }}</span>
                                @else
                                <span class="text-outline text-xs">0</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-on-surface-variant text-xs font-semibold">
                                {{ $l->bobot_rata_rata ? number_format($l->bobot_rata_rata, 2) . ' kg' : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Expandable anak_lahir toggle --}}
                                    @if(isset($anakLahirMap[$l->lahir_id]) && count($anakLahirMap[$l->lahir_id]) > 0)
                                    <button @click="toggleAnakRows({{ $l->lahir_id }})"
                                            class="p-1.5 text-outline hover:bg-surface-container rounded-lg transition-colors"
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
                                            class="p-1.5 text-outline hover:bg-surface-container rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        {{-- Expandable anak_lahir rows --}}
                        @if(isset($anakLahirMap[$l->lahir_id]) && count($anakLahirMap[$l->lahir_id]) > 0)
                        <tr x-show="expandedLahir.includes({{ $l->lahir_id }})" x-cloak>
                            <td colspan="7" class="px-4 pb-3">
                                <div class="bg-surface-container-low rounded-xl border border-outline-variant overflow-hidden">
                                    <table class="w-full text-xs">
                                        <thead>
                                            <tr class="bg-surface-container">
                                                <th class="px-3 py-2 text-left text-[10px] font-bold text-outline uppercase">Anak #</th>
                                                <th class="px-3 py-2 text-left text-[10px] font-bold text-outline uppercase">Ear Tag</th>
                                                <th class="px-3 py-2 text-left text-[10px] font-bold text-outline uppercase">Jenis Kelamin</th>
                                                <th class="px-3 py-2 text-left text-[10px] font-bold text-outline uppercase">Bobot</th>
                                                <th class="px-3 py-2 text-left text-[10px] font-bold text-outline uppercase">Kondisi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($anakLahirMap[$l->lahir_id] as $i => $anak)
                                            <tr>
                                                <td class="px-3 py-2 text-on-surface-variant">#{{ $i + 1 }}</td>
                                                <td class="px-3 py-2 font-mono font-bold text-primary">{{ $anak->ear_tag_id ?? '—' }}</td>
                                                <td class="px-3 py-2">
                                                    <span class="px-1.5 py-0.5 {{ $anak->jenis_kelamin === 'jantan' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }} font-bold rounded-full">
                                                        {{ ucfirst($anak->jenis_kelamin) }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 text-on-surface-variant">{{ $anak->bobot_lahir ? number_format($anak->bobot_lahir, 2) . ' kg' : '—' }}</td>
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
                            <td colspan="7" class="px-4 py-12 text-center text-outline text-sm">
                                <span class="material-symbols-outlined text-4xl block mb-2 text-outline-variant">child_care</span>
                                Belum ada data kelahiran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($kelahiran->hasPages())
            <div class="px-4 py-3 border-t border-outline-variant/30">
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
