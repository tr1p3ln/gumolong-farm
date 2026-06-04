@extends('layouts.app')
@section('page-title', 'Kesehatan Ternak')

@section('content')
<div class="-m-8 bg-[#F8F9F5]" x-data="kesehatanApp()" x-init="init()">

    {{-- ── Flash messages ─────────────────────────────────────── --}}
    @if(session('success'))
    <div class="mx-8 mt-6 flex items-center gap-3 px-5 py-3.5 bg-primary/10 border border-primary/20 rounded-xl text-sm text-primary font-semibold"
         x-data="{ show: true }" x-show="show">
        <span class="material-symbols-outlined text-lg">check_circle</span>
        {{ session('success') }}
        <button @click="show = false" class="ml-auto text-primary/50 hover:text-primary"><span class="material-symbols-outlined text-base">close</span></button>
    </div>
    @endif
    @if(session('error'))
    <div class="mx-8 mt-6 flex items-center gap-3 px-5 py-3.5 bg-error/10 border border-error/20 rounded-xl text-sm text-error font-semibold"
         x-data="{ show: true }" x-show="show">
        <span class="material-symbols-outlined text-lg">error</span>
        {{ session('error') }}
        <button @click="show = false" class="ml-auto text-error/50 hover:text-error"><span class="material-symbols-outlined text-base">close</span></button>
    </div>
    @endif

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <header class="px-8 pt-8 pb-6 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-extrabold text-on-surface mb-1">Kesehatan Ternak</h1>
        </div>
        <div class="flex gap-3">
            <button @click="showCreateVaksinasi = true"
                    class="flex items-center gap-2 px-5 py-2.5 border border-primary text-primary font-bold text-sm rounded-lg hover:bg-primary/5 transition-all">
                <span class="material-symbols-outlined text-lg">vaccines</span>
                Catat Vaksinasi
            </button>
            <button @click="showCreateRekam = true"
                    class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-lg shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all active:scale-95">
                <span class="material-symbols-outlined text-lg">add</span>
                Tambah Rekam Medis
            </button>
        </div>
    </header>

    {{-- ── KPI Cards ───────────────────────────────────────────── --}}
    <section class="px-8 grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">

        {{-- Sedang Sakit --}}
        @php $sakitBadge = $kpiSakit > 0 ? ['label' => 'PERLU PERHATIAN', 'class' => 'bg-error-container text-on-error-container'] : ['label' => 'NIHIL', 'class' => 'bg-secondary-container text-on-secondary-container']; @endphp
        <div class="bg-white p-5 rounded-xl border border-outline-variant/30 shadow-sm flex flex-col gap-1">
            <div class="flex items-start justify-between">
                <p class="text-[11px] font-semibold text-outline uppercase tracking-wider">Sedang Sakit</p>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $sakitBadge['class'] }}">{{ $sakitBadge['label'] }}</span>
            </div>
            <p class="text-3xl font-black text-on-surface leading-none mt-2">{{ $kpiSakit }} <span class="text-sm font-medium text-outline">ekor</span></p>
            <p class="text-xs text-on-surface-variant mt-1">domba sakit / dalam perawatan</p>
        </div>

        {{-- Karantina Aktif --}}
        @php
            $karantinaBadge = $kpiKarantina > 0 ? ['label' => 'AKTIF', 'class' => 'bg-amber-100 text-amber-700'] : ['label' => 'NIHIL', 'class' => 'bg-secondary-container text-on-secondary-container'];
            $karantinaPC = $kapasitasIsolasi > 0 ? min(100, round($kpiKarantina / $kapasitasIsolasi * 100)) : 0;
        @endphp
        <div class="bg-white p-5 rounded-xl border border-outline-variant/30 shadow-sm flex flex-col gap-1">
            <div class="flex items-start justify-between">
                <p class="text-[11px] font-semibold text-outline uppercase tracking-wider">Karantina Aktif</p>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $karantinaBadge['class'] }}">{{ $karantinaBadge['label'] }}</span>
            </div>
            <p class="text-3xl font-black text-on-surface leading-none mt-2">{{ $kpiKarantina }} <span class="text-sm font-medium text-outline">ekor</span></p>
            <div class="flex justify-between items-center text-[10px] text-outline uppercase tracking-wider mt-2">
                <span>Kapasitas isolasi</span>
                <span>{{ $karantinaPC }}%</span>
            </div>
            <div class="w-full h-1.5 bg-surface-container-high rounded-full overflow-hidden">
                <div class="h-1.5 rounded-full transition-all {{ $kpiKarantina > 0 ? 'bg-amber-400' : 'bg-primary' }}"
                     style="width: {{ $karantinaPC }}%"></div>
            </div>
        </div>

        {{-- Sembuh Bulan Ini --}}
        <div class="bg-white p-5 rounded-xl border border-outline-variant/30 shadow-sm flex flex-col gap-1">
            <div class="flex items-start justify-between">
                <p class="text-[11px] font-semibold text-outline uppercase tracking-wider">Sembuh Bulan Ini</p>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-secondary-container text-on-secondary-container">{{ now()->isoFormat('MMM YYYY') }}</span>
            </div>
            <p class="text-3xl font-black text-on-surface leading-none mt-2">{{ $kpiSembuh }} <span class="text-sm font-medium text-outline">ekor</span></p>
            <p class="text-xs text-on-surface-variant mt-1">pulih bulan ini</p>
        </div>

        {{-- Vaksinasi Minggu Ini --}}
        @php $vaksinBadge = $kpiVaksinMingguIni > 0 ? ['label' => 'ADA JADWAL', 'class' => 'bg-primary-container text-on-primary-container'] : ['label' => 'NIHIL', 'class' => 'bg-surface-container text-outline']; @endphp
        <div class="bg-white p-5 rounded-xl border border-outline-variant/30 shadow-sm flex flex-col gap-1">
            <div class="flex items-start justify-between">
                <p class="text-[11px] font-semibold text-outline uppercase tracking-wider">Vaksinasi Minggu Ini</p>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $vaksinBadge['class'] }}">{{ $vaksinBadge['label'] }}</span>
            </div>
            <p class="text-3xl font-black text-on-surface leading-none mt-2">{{ $kpiVaksinMingguIni }} <span class="text-sm font-medium text-outline">ekor</span></p>
            <p class="text-xs text-on-surface-variant mt-1">divaksin minggu ini</p>
        </div>

    </section>

    {{-- ── Tab Navigation ──────────────────────────────────────── --}}
    <section class="px-8 border-b border-outline-variant/30 mb-0">
        <div class="flex gap-8">
            <button @click="tab = 'rekam-medis'"
                    :class="tab === 'rekam-medis' ? 'text-primary font-bold border-b-4 border-primary' : 'text-outline font-semibold hover:text-on-surface'"
                    class="pb-4 text-sm transition-colors">
                Rekam Medis
            </button>
            <button @click="tab = 'karantina'"
                    :class="tab === 'karantina' ? 'text-primary font-bold border-b-4 border-primary' : 'text-outline font-semibold hover:text-on-surface'"
                    class="pb-4 text-sm transition-colors flex items-center gap-2">
                Karantina
                @if($kpiKarantina > 0)
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-error text-white">{{ $kpiKarantina }}</span>
                @endif
            </button>
            <button @click="tab = 'vaksinasi'"
                    :class="tab === 'vaksinasi' ? 'text-primary font-bold border-b-4 border-primary' : 'text-outline font-semibold hover:text-on-surface'"
                    class="pb-4 text-sm transition-colors flex items-center gap-2">
                Vaksinasi
                @if($jadwalVaksinasi->count() > 0)
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-500 text-white">{{ $jadwalVaksinasi->count() }}</span>
                @endif
            </button>
        </div>
    </section>

    {{-- ════════════════════════════════════════════════════════════
         TAB 1: REKAM MEDIS
    ══════════════════════════════════════════════════════════════ --}}
    <section x-show="tab === 'rekam-medis'" class="px-8 py-8 space-y-6">

        {{-- Filter bar --}}
        <form method="GET" action="{{ route('kesehatan.index') }}" class="flex flex-wrap gap-3 items-end">
            <input type="hidden" name="tab" value="rekam-medis">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-outline mb-1.5 uppercase tracking-wider">Cari Domba / Gejala</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg pointer-events-none">search</span>
                    <input type="text" name="rm_search" value="{{ request('rm_search') }}"
                           placeholder="Ear tag, nama, atau gejala..."
                           class="w-full pl-9 pr-4 py-2 border border-outline-variant rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-outline mb-1.5 uppercase tracking-wider">Status</label>
                <select name="rm_status"
                        class="px-3 py-2 border border-outline-variant rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary outline-none">
                    <option value="">Semua Status</option>
                    <option value="sakit"           @selected(request('rm_status') === 'sakit')>Sakit</option>
                    <option value="dalam_perawatan" @selected(request('rm_status') === 'dalam_perawatan')>Dalam Perawatan</option>
                    <option value="sembuh"          @selected(request('rm_status') === 'sembuh')>Sembuh</option>
                    <option value="mati"            @selected(request('rm_status') === 'mati')>Mati</option>
                </select>
            </div>
            <button type="submit"
                    class="px-5 py-2 bg-primary text-white font-bold text-sm rounded-lg hover:bg-primary/90 transition-all">
                Filter
            </button>
            @if(request()->hasAny(['rm_search','rm_status']))
            <a href="{{ route('kesehatan.index', ['tab' => 'rekam-medis']) }}"
               class="px-4 py-2 border border-outline-variant text-outline text-sm font-semibold rounded-lg hover:bg-surface-container transition-all">
                Reset
            </a>
            @endif
        </form>

        {{-- Table --}}
        <div class="rounded-xl border border-outline-variant/30 overflow-hidden shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low border-b border-outline-variant/30">
                    <tr>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Ear Tag</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Domba</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Tgl Sakit</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Gejala</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Diagnosa</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Status</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Obat</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 bg-white">
                    @forelse($rekamList as $r)
                    <tr class="hover:bg-surface-container-lowest transition-colors">
                        <td class="px-5 py-4 font-bold text-primary text-sm">{{ $r->ear_tag_id }}</td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-semibold text-on-surface">{{ $r->nama_domba ?? '—' }}</p>
                            <p class="text-xs text-outline">{{ $r->kategori }} · {{ $r->ras }}</p>
                        </td>
                        <td class="px-5 py-4 text-sm text-on-surface-variant">
                            {{ \Carbon\Carbon::parse($r->tanggal_sakit)->isoFormat('D MMM YYYY') }}
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm text-on-surface max-w-[160px] truncate" title="{{ $r->gejala }}">{{ $r->gejala }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm text-on-surface-variant max-w-[140px] truncate" title="{{ $r->diagnosa }}">
                                {{ $r->diagnosa ?? '—' }}
                            </p>
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $badge = match($r->status) {
                                    'sakit'           => 'bg-error-container text-on-error-container',
                                    'dalam_perawatan' => 'bg-tertiary-container/30 text-tertiary',
                                    'sembuh'          => 'bg-secondary-container text-on-secondary-container',
                                    'mati'            => 'bg-surface-container-high text-outline',
                                    default           => 'bg-surface-container text-outline',
                                };
                                $label = match($r->status) {
                                    'sakit'           => 'Sakit',
                                    'dalam_perawatan' => 'Dalam Perawatan',
                                    'sembuh'          => 'Sembuh',
                                    'mati'            => 'Mati',
                                    default           => $r->status,
                                };
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $badge }}">{{ $label }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @php $pemakaian = $pemakaianMap->get($r->rekam_id, collect()); @endphp
                            @if($pemakaian->count())
                                <div class="space-y-0.5">
                                    @foreach($pemakaian as $p)
                                    <p class="text-xs text-on-surface-variant">{{ $p->nama_obat }} <span class="font-bold">×{{ $p->jumlah }}</span> {{ $p->satuan }}</p>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-outline italic">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex gap-2">
                                <button type="button"
                                        @click='openDetailRekam({{ $r->rekam_id }})'
                                        class="text-[10px] font-bold uppercase tracking-tight px-3 py-1.5 border border-outline-variant text-outline rounded hover:bg-surface-container transition-all">
                                    Lihat
                                </button>
                                <button type="button"
                                        @click='openEditRekam({{ $r->rekam_id }})'
                                        class="text-[10px] font-bold uppercase tracking-tight px-3 py-1.5 border border-primary text-primary rounded hover:bg-primary hover:text-white transition-all">
                                    Edit
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-outline text-sm italic">
                            Belum ada data rekam medis.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($rekamList->hasPages())
        <div>{{ $rekamList->appends(request()->except('rm_page'))->links() }}</div>
        @endif

    </section>

    {{-- ════════════════════════════════════════════════════════════
         TAB 2: KARANTINA
    ══════════════════════════════════════════════════════════════ --}}
    <section x-show="tab === 'karantina'" class="px-8 py-8 space-y-6">

        {{-- Info banner --}}
        <div class="bg-surface-container-high/50 p-4 rounded-lg flex items-start gap-4 border-l-4 border-secondary">
            <span class="material-symbols-outlined text-secondary flex-shrink-0">info</span>
            <p class="text-sm text-on-surface-variant font-medium">
                Halaman ini menampilkan domba yang saat ini berstatus <strong>Karantina</strong>,
                beserta riwayat penyakit terkait.
            </p>
        </div>

        {{-- Stats bar --}}
        <div class="flex border border-outline-variant/50 rounded-xl overflow-hidden">
            <div class="flex-1 p-6 border-r border-outline-variant/50 flex flex-col items-center justify-center text-center">
                <p class="text-[10px] font-bold text-outline uppercase tracking-widest mb-1">KARANTINA AKTIF</p>
                <p class="text-xl font-black text-primary">{{ $kpiKarantina }} ekor</p>
            </div>
            <div class="flex-1 p-6 border-r border-outline-variant/50 flex flex-col items-center justify-center text-center bg-surface-container-low">
                <p class="text-[10px] font-bold text-outline uppercase tracking-widest mb-1">KAPASITAS KANDANG ISOLASI</p>
                <p class="text-xl font-black text-on-surface">{{ $kpiKarantina }} / {{ $kapasitasIsolasi }}</p>
            </div>
            <div class="flex-1 p-6 flex flex-col items-center justify-center text-center">
                <p class="text-[10px] font-bold text-outline uppercase tracking-widest mb-1">RATA-RATA DURASI</p>
                <p class="text-xl font-black text-on-surface">
                    {{ $rataKarantina ? round($rataKarantina) . ' hari' : '—' }}
                </p>
            </div>
        </div>

        {{-- Table --}}
        <div class="rounded-xl border border-outline-variant/30 overflow-hidden shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low border-b border-outline-variant/30">
                    <tr>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Ear Tag</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Nama Domba</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Kategori</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Alasan Karantina</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Sejak</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Durasi</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Kandang Isolasi</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 bg-white">
                    @forelse($karantinaList as $k)
                    <tr class="hover:bg-surface-container-lowest transition-colors">
                        <td class="px-5 py-4 font-bold text-primary text-sm">{{ $k->ear_tag_id }}</td>
                        <td class="px-5 py-4 font-semibold text-sm text-on-surface">{{ $k->nama ?? '—' }}</td>
                        <td class="px-5 py-4">
                            <span class="bg-secondary-container text-on-secondary-container px-3 py-1 rounded-full text-xs font-bold">
                                {{ ucfirst($k->kategori) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @if($k->alasan_karantina)
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-error flex-shrink-0"></span>
                                <p class="text-sm font-medium max-w-[200px] truncate" title="{{ $k->alasan_karantina }}">
                                    {{ $k->alasan_karantina }}
                                </p>
                            </div>
                            @else
                            <span class="text-xs text-outline italic">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-on-surface-variant">
                            {{ $k->tanggal_masuk ? \Carbon\Carbon::parse($k->tanggal_masuk)->isoFormat('D MMM YYYY') : '—' }}
                        </td>
                        <td class="px-5 py-4">
                            @if($k->durasi_hari !== null)
                            <span class="text-sm font-bold text-tertiary">{{ $k->durasi_hari }} hari</span>
                            @else
                            <span class="text-xs text-outline italic">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-sm font-semibold px-2.5 py-1 bg-surface-container rounded border border-outline-variant/30">
                                {{ $k->nama_kandang }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex gap-2">
                                <button type="button"
                                        @click='openPindahKandang("{{ $k->ear_tag_id }}", {{ $k->durasi_hari ?? 0 }})'
                                        class="text-[10px] font-bold uppercase tracking-tight px-3 py-1.5 border border-primary text-primary rounded hover:bg-primary hover:text-white transition-all">
                                    Pindah Kandang
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-outline text-sm italic">
                            Tidak ada domba dalam karantina saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-center text-[10px] text-outline font-bold uppercase tracking-widest mt-4">
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-primary"></span> Status: aktif</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-error"></span> Sakit/Karantina</span>
            </div>
        </div>

    </section>

    {{-- ════════════════════════════════════════════════════════════
         TAB 3: VAKSINASI
    ══════════════════════════════════════════════════════════════ --}}
    <section x-show="tab === 'vaksinasi'" class="px-8 py-8 space-y-6">

        {{-- Upcoming jadwal banner --}}
        @if($jadwalVaksinasi->count() > 0)
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
            <div class="flex items-start gap-3 mb-3">
                <span class="material-symbols-outlined text-amber-600 flex-shrink-0">schedule</span>
                <p class="text-sm font-bold text-amber-900">{{ $jadwalVaksinasi->count() }} jadwal vaksinasi dalam 14 hari ke depan</p>
            </div>
            <div class="space-y-1.5 pl-8">
                @foreach($jadwalVaksinasi as $j)
                <p class="text-xs font-medium text-amber-800">
                    <span class="font-bold">{{ $j->ear_tag_id }}</span>
                    {{ $j->nama_domba ? '(' . $j->nama_domba . ')' : '' }}
                    — {{ $j->nama_obat }}
                    — <span class="font-bold">{{ \Carbon\Carbon::parse($j->tanggal_berikutnya)->isoFormat('D MMM YYYY') }}</span>
                </p>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Filter --}}
        <form method="GET" action="{{ route('kesehatan.index') }}" class="flex flex-wrap gap-3 items-end">
            <input type="hidden" name="tab" value="vaksinasi">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-outline mb-1.5 uppercase tracking-wider">Cari Domba / Vaksin</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg pointer-events-none">search</span>
                    <input type="text" name="vk_search" value="{{ request('vk_search') }}"
                           placeholder="Ear tag, nama, atau nama vaksin..."
                           class="w-full pl-9 pr-4 py-2 border border-outline-variant rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none"/>
                </div>
            </div>
            <button type="submit"
                    class="px-5 py-2 bg-primary text-white font-bold text-sm rounded-lg hover:bg-primary/90 transition-all">
                Filter
            </button>
            @if(request('vk_search'))
            <a href="{{ route('kesehatan.index', ['tab' => 'vaksinasi']) }}"
               class="px-4 py-2 border border-outline-variant text-outline text-sm font-semibold rounded-lg hover:bg-surface-container transition-all">
                Reset
            </a>
            @endif
        </form>

        {{-- Table --}}
        <div class="rounded-xl border border-outline-variant/30 overflow-hidden shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low border-b border-outline-variant/30">
                    <tr>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Ear Tag</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Domba</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Vaksin</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Tgl Vaksinasi</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Jadwal Berikutnya</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Catatan</th>
                        <th class="px-5 py-4 text-[10px] font-black text-outline uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 bg-white">
                    @forelse($vaksinasiList as $v)
                    <tr class="hover:bg-surface-container-lowest transition-colors">
                        <td class="px-5 py-4 font-bold text-primary text-sm">{{ $v->ear_tag_id }}</td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-semibold text-on-surface">{{ $v->nama_domba ?? '—' }}</p>
                            <p class="text-xs text-outline">{{ $v->kategori }} · {{ $v->ras }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-sm font-medium text-on-surface">{{ $v->nama_obat }}</span>
                        </td>
                        <td class="px-5 py-4 text-sm text-on-surface-variant">
                            {{ \Carbon\Carbon::parse($v->tanggal_vaksinasi)->isoFormat('D MMM YYYY') }}
                        </td>
                        <td class="px-5 py-4">
                            @if($v->tanggal_berikutnya)
                            @php
                                $sisa = \Carbon\Carbon::parse($v->tanggal_berikutnya)->diffInDays(now(), false);
                                $upcoming = $sisa <= 0;
                            @endphp
                            <span class="text-sm font-semibold {{ $upcoming && abs($sisa) <= 14 ? 'text-amber-600' : 'text-on-surface-variant' }}">
                                {{ \Carbon\Carbon::parse($v->tanggal_berikutnya)->isoFormat('D MMM YYYY') }}
                            </span>
                            @if($upcoming && abs($sisa) <= 14)
                            <span class="ml-1 text-[10px] font-bold text-amber-600 bg-amber-100 px-1.5 py-0.5 rounded">
                                {{ abs($sisa) === 0 ? 'Hari ini' : abs((int)$sisa) . 'h lagi' }}
                            </span>
                            @endif
                            @else
                            <span class="text-xs text-outline italic">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm text-on-surface-variant max-w-[140px] truncate" title="{{ $v->catatan }}">
                                {{ $v->catatan ?? '—' }}
                            </p>
                        </td>
                        <td class="px-5 py-4">
                            <button type="button"
                                    @click="openEditVaksinasi({
                                        vaksin_id: {{ $v->vaksin_id }},
                                        ear_tag_id: '{{ $v->ear_tag_id }}',
                                        nama_domba: @json($v->nama_domba),
                                        obat_id: {{ $v->obat_id }},
                                        nama_obat: @json($v->nama_obat),
                                        tanggal_vaksinasi: '{{ $v->tanggal_vaksinasi }}',
                                        tanggal_berikutnya: @json($v->tanggal_berikutnya),
                                        catatan: @json($v->catatan)
                                    })"
                                    class="text-[10px] font-bold uppercase tracking-tight px-3 py-1.5 border border-primary text-primary rounded hover:bg-primary hover:text-white transition-all">
                                Edit
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-outline text-sm italic">
                            Belum ada data vaksinasi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vaksinasiList->hasPages())
        <div>{{ $vaksinasiList->appends(request()->except('vk_page'))->links() }}</div>
        @endif

    </section>

    {{-- ── Modals ──────────────────────────────────────────────── --}}
    @include('kesehatan.partials.modal-create-rekam')
    @include('kesehatan.partials.modal-edit-rekam')
    @include('kesehatan.partials.modal-detail-rekam')
    @include('kesehatan.partials.modal-create-vaksinasi')
    @include('kesehatan.partials.modal-edit-vaksinasi')
    @include('kesehatan.partials.modal-pindah-kandang')

</div>
@endsection

@push('scripts')
<script>
const dombaList   = @json($dombaList);
const vaksinList  = @json($vaksinList);
const kandangList = @json($kandangList);

// Karantina row data map: ear_tag_id → row object
const karantinaDataMap = {
@foreach($karantinaList as $k)
    @json($k->ear_tag_id): {
        ear_tag_id: @json($k->ear_tag_id),
        nama: @json($k->nama),
        nama_kandang: @json($k->nama_kandang),
        tanggal_masuk: @json($k->tanggal_masuk),
        durasi_hari: {{ $k->durasi_hari ?? 0 }}
    },
@endforeach
};

// Rekam medis row data map: rekam_id → record object
const rekamDataMap = {
@foreach($rekamList as $r)
    {{ $r->rekam_id }}: {
        rekam_id: {{ $r->rekam_id }},
        ear_tag_id: @json($r->ear_tag_id),
        nama_domba: @json($r->nama_domba),
        ras: @json($r->ras),
        kategori: @json($r->kategori),
        tanggal_sakit: @json($r->tanggal_sakit),
        tanggal_sembuh: @json($r->tanggal_sembuh),
        gejala: @json($r->gejala),
        diagnosa: @json($r->diagnosa),
        status: @json($r->status),
        nama_kandang: @json($r->nama_kandang),
        domba_status: @json($r->domba_status),
        kandang_id: {{ $r->kandang_id ?? 'null' }},
        created_at: @json($r->created_at),
        pemakaian: @json($pemakaianMap->get($r->rekam_id, collect())->values())
    },
@endforeach
};

function kesehatanApp() {
    return {
        tab: 'rekam-medis',

        // Modal visibility
        showCreateRekam:     false,
        showEditRekam:       false,
        showDetailRekam:     false,
        showCreateVaksinasi: false,
        showEditVaksinasi:   false,
        showPindahKandang:   false,

        // Edit rekam state
        editRekamData:    {},
        editRekamStatus:  '',
        editKarantina:    'tidak',
        editObatRows:     [],

        // Detail rekam state
        detailRekamData:  {},

        // Other edit data
        editVaksinasiData: {},
        pindahData:       {},

        // Create rekam form state
        selectedDomba:    null,
        dombaSearch:      '',
        obatRows:         [],
        tandaiKarantina:  'tidak',

        // Create vaksinasi form state
        selectedDombaVk:  null,
        dombaVkSearch:    '',

        get filteredDombaRekam() {
            if (!this.dombaSearch) return [];
            const q = this.dombaSearch.toLowerCase();
            return dombaList.filter(d =>
                d.ear_tag_id.toLowerCase().includes(q) ||
                (d.nama && d.nama.toLowerCase().includes(q))
            );
        },

        get filteredDombaVk() {
            if (!this.dombaVkSearch) return [];
            const q = this.dombaVkSearch.toLowerCase();
            return dombaList.filter(d =>
                d.ear_tag_id.toLowerCase().includes(q) ||
                (d.nama && d.nama.toLowerCase().includes(q))
            );
        },

        addObatRow() {
            this.obatRows.push({ obat_id: '', jumlah: 1 });
        },
        removeObatRow(idx) {
            this.obatRows.splice(idx, 1);
        },

        addEditObatRow() {
            this.editObatRows.push({ obat_id: '', jumlah: 1 });
        },
        removeEditObatRow(idx) {
            this.editObatRows.splice(idx, 1);
        },

        hapusPemakaian(pakaiId) {
            if (!confirm('Hapus pemakaian obat ini? Stok akan dikembalikan.')) return;
            const f = document.createElement('form');
            f.method = 'POST';
            f.action = `/kesehatan/${this.editRekamData.rekam_id}/obat/${pakaiId}`;
            f.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(f);
            f.submit();
        },

        tandaiSembuh() {
            const today = new Date().toISOString().split('T')[0];
            this.editRekamData.status = 'sembuh';
            this.editRekamData.tanggal_sembuh = today;
            this.editRekamStatus = 'sembuh';
            this.editKarantina = 'tidak';
            this.$nextTick(() => document.getElementById('edit-rekam-form').submit());
        },

        openEditRekam(rekamId) {
            const data = rekamDataMap[rekamId];
            if (!data) return;
            this.editRekamData   = { ...data };
            this.editRekamStatus = data.status;
            this.editKarantina   = data.domba_status === 'karantina' ? 'ya' : 'tidak';
            this.editObatRows    = [];
            this.showEditRekam   = true;
        },

        openDetailRekam(rekamId) {
            const data = rekamDataMap[rekamId];
            if (!data) return;
            this.detailRekamData = data;
            this.showDetailRekam = true;
        },

        openEditVaksinasi(data) {
            this.editVaksinasiData = data;
            this.showEditVaksinasi = true;
        },

        openPindahKandang(earTagId, durasiHari) {
            const data = karantinaDataMap[earTagId] ?? { ear_tag_id: earTagId, durasi_hari: durasiHari };
            this.pindahData       = data;
            this.showPindahKandang = true;
        },

        init() {
            const params = new URLSearchParams(window.location.search);
            const t = params.get('tab');
            if (t) this.tab = t;
        }
    };
}
</script>
@endpush
