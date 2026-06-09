@extends('layouts.app')

@section('page-title', 'Pertumbuhan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pertumbuhan.css') }}">
@endpush

@section('content')

{{-- Flash --}}
@if (session('success'))
    <div class="flash flash-success">{{ session('success') }}</div>
@endif

{{-- Page header --}}
<div class="pg-header">
    <div>
        <h2 class="pg-title">Tracking Pertumbuhan</h2>
        <p class="pg-sub">Pantau laju pertumbuhan ADG harian ternak Anda secara real-time.</p>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid">
    {{-- Rata-rata ADG --}}
    <div class="stat-card">
        <div class="stat-card-icon">
            <svg width="28" height="28" fill="none" stroke="#2D7A3A" stroke-width="1.5" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div class="stat-label">Rata-rata ADG Keseluruhan</div>
        <div class="stat-value">{{ number_format($stats['avg_adg'], 2) }}<span class="unit">g/hari</span></div>
        <div class="stat-sub">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg>
            {{ $stats['avg_adg_trend'] }}% dari bulan lalu
        </div>
    </div>

    {{-- Domba ADG Rendah --}}
    <div class="stat-card {{ $stats['domba_rendah'] > 0 ? 'alert-card' : '' }}">
        <div class="stat-card-icon">
            <svg width="28" height="28" fill="none" stroke="#E8630A" stroke-width="1.5" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div class="stat-label">Domba ADG Rendah</div>
        <div class="stat-value">{{ $stats['domba_rendah'] }}<span class="unit">ekor</span></div>
        <div class="stat-sub warn">Membutuhkan perhatian nutrisi</div>
    </div>

    {{-- Penimbangan Bulan Ini --}}
    <div class="stat-card">
        <div class="stat-card-icon">
            <svg width="28" height="28" fill="none" stroke="#2D7A3A" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 2a5 5 0 0 1 5 5v2H7V7a5 5 0 0 1 5-5z"/><rect x="3" y="9" width="18" height="13" rx="2"/><line x1="12" y1="13" x2="12" y2="17"/></svg>
        </div>
        <div class="stat-label">Penimbangan Bulan Ini</div>
        <div class="stat-value">{{ $stats['penimbangan_bulan_ini'] }}<span class="unit">log</span></div>
        <div class="stat-sub">Target tercapai {{ $stats['target_persen'] }}%</div>
    </div>
</div>

{{-- Page body: left + right --}}
<div class="pg-body">

    {{-- ── Left column ──────────────────────────────────────────────────── --}}
    <div class="left-col">

        {{-- Filter bar --}}
        <form method="GET" action="{{ route('tracking-pertumbuhan.index') }}">
            <div class="filter-bar">
                <div class="filter-search">
                    <svg width="14" height="14" fill="none" stroke="#9AA0A6" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="search" placeholder="Cari Ear Tag…" value="{{ request('search') }}">
                </div>

                <select name="kategori" class="filter-select">
                    <option value="">Kategori</option>
                    <option value="pedaging"  {{ request('kategori') === 'pedaging'  ? 'selected' : '' }}>Pedaging</option>
                    <option value="perah"     {{ request('kategori') === 'perah'     ? 'selected' : '' }}>Perah</option>
                    <option value="bibit"     {{ request('kategori') === 'bibit'     ? 'selected' : '' }}>Bibit</option>
                </select>

                <select name="status_adg" class="filter-select">
                    <option value="">Status ADG</option>
                    <option value="normal" {{ request('status_adg') === 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="rendah" {{ request('status_adg') === 'rendah' ? 'selected' : '' }}>Rendah</option>
                    <option value="alert"  {{ request('status_adg') === 'alert'  ? 'selected' : '' }}>Alert</option>
                </select>

                <input type="date" name="tgl_timbang" class="filter-date" value="{{ request('tgl_timbang') }}">

                <select name="kandang_id" class="filter-select">
                    <option value="">Kandang</option>
                    @foreach($kandangList as $k)
                        <option value="{{ $k->kandang_id }}" {{ request('kandang_id') == $k->kandang_id ? 'selected' : '' }}>
                            {{ $k->nama_kandang }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn-cari">Cari</button>
                <a href="{{ route('tracking-pertumbuhan.index') }}" class="btn-reset">Reset</a>
            </div>
        </form>

        {{-- Data table --}}
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Ear Tag</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Ras</th>
                        <th>Berat</th>
                        <th>Tgl Timbang</th>
                        <th>ADG</th>
                        <th>Status ADG</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dombaList as $domba)
                        @php
                            $adg = $domba->adg;
                            $statusAdg = 'normal';
                            if ($adg === null)      { $statusAdg = 'belum'; }
                            elseif ($adg < 0)       { $statusAdg = 'alert'; }
                            elseif ($adg < 0.2)     { $statusAdg = 'rendah'; }
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('tracking-pertumbuhan.show', $domba->ear_tag_id) }}" class="ear-tag-link">
                                    #{{ $domba->ear_tag_id }}
                                </a>
                            </td>
                            <td>{{ $domba->nama }}</td>
                            <td style="text-transform:capitalize">{{ $domba->kategori ?? '-' }}</td>
                            <td>{{ $domba->ras ?? '-' }}</td>
                            <td>{{ $domba->berat_kg ? number_format($domba->berat_kg, 1).' kg' : '-' }}</td>
                            <td>{{ $domba->tgl_timbang ? \Carbon\Carbon::parse($domba->tgl_timbang)->format('d M Y') : '-' }}</td>
                            <td>
                                @if ($adg !== null)
                                    <span class="adg-val {{ $adg >= 0 ? 'positive' : 'negative' }}">
                                        {{ number_format($adg, 2) }} g
                                    </span>
                                @else
                                    <span style="color:var(--gf-gray-500)">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($statusAdg === 'normal')
                                    <span class="badge badge-normal">Normal</span>
                                @elseif ($statusAdg === 'rendah')
                                    <span class="badge badge-rendah">Rendah</span>
                                @elseif ($statusAdg === 'alert')
                                    <span class="badge badge-alert">Alert</span>
                                @else
                                    <span class="badge badge-belum">Belum</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="{{ route('tracking-pertumbuhan.show', $domba->ear_tag_id) }}" class="btn-detail">Detail</a>
                                    <button
                                        class="btn-timbang"
                                        type="button"
                                        data-ear="{{ $domba->ear_tag_id }}"
                                        data-nama="{{ $domba->nama }}"
                                        data-url="{{ route('tracking-pertumbuhan.penimbangan.store', $domba->ear_tag_id) }}"
                                        onclick="openTimbangModal(this)"
                                    >
                                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        Timbang
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    <p>Tidak ada data domba yang ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="table-footer">
                <span>Menampilkan {{ $dombaList->firstItem() ?? 0 }}–{{ $dombaList->lastItem() ?? 0 }} dari {{ $dombaList->total() }} ternak</span>

                <div class="pagination-links">
                    @if ($dombaList->onFirstPage())
                        <span class="page-btn disabled"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></span>
                    @else
                        <a href="{{ $dombaList->previousPageUrl() }}" class="page-btn"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></a>
                    @endif

                    @foreach ($dombaList->getUrlRange(max(1,$dombaList->currentPage()-2), min($dombaList->lastPage(),$dombaList->currentPage()+2)) as $page => $url)
                        <a href="{{ $url }}" class="page-btn {{ $page === $dombaList->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach

                    @if ($dombaList->hasMorePages())
                        <a href="{{ $dombaList->nextPageUrl() }}" class="page-btn"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></a>
                    @else
                        <span class="page-btn disabled"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
                    @endif
                </div>
            </div>
        </div>
    </div>{{-- /left-col --}}

    {{-- ── Right column ─────────────────────────────────────────────────── --}}
    <div class="right-col">

        {{-- Alert panel --}}
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">Perlu Perhatian</span>
                <span class="alert-count-badge">{{ $alerts->count() }} Alert</span>
            </div>
            <div class="alert-list">
                @forelse($alerts->take(3) as $alert)
                    @php
                        $dotClass     = $alert->adg < 0 ? 'red' : 'amber';
                        $adgFormatted = number_format($alert->adg, 2);
                        $since        = \Carbon\Carbon::parse($alert->tanggal_timbang)->diffForHumans();
                    @endphp
                    <div class="alert-item">
                        <div class="alert-dot {{ $dotClass }}">{{ strtoupper(substr($alert->nama,0,1)) }}</div>
                        <div class="alert-info">
                            <div>
                                <span class="alert-ear">#{{ $alert->ear_tag_id }}</span>
                                <span class="alert-nama">({{ $alert->nama }})</span>
                            </div>
                            <div class="alert-detail">{{ $adgFormatted }} g/hari · {{ $since }}</div>
                        </div>
                    </div>
                @empty
                    <div class="alert-item">
                        <div class="alert-info">
                            <div class="alert-detail" style="padding:8px 0">Semua ternak dalam kondisi baik 🎉</div>
                        </div>
                    </div>
                @endforelse
            </div>
            <a href="{{ route('tracking-pertumbuhan.index', ['status_adg' => 'alert']) }}" class="btn-lihat-semua">
                Lihat Semua Alert →
            </a>
        </div>

        {{-- Trend panel --}}
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">Tren Rata-rata ADG</span>
            </div>
            <div class="trend-body">
                <div class="trend-growth {{ $trendData['weekly_growth'] < 0 ? 'negative' : '' }}">
                    {{ $trendData['weekly_growth'] >= 0 ? '+' : '' }}{{ $trendData['weekly_growth'] }}%
                </div>
                <div class="trend-label">Weekly Growth</div>

                @php
                    $vals  = collect($trendData['weeks'])->pluck('value');
                    $min   = $vals->min();
                    $max   = $vals->max();
                    $range = max($max - $min, 0.001);
                    $sw = 260; $sh = 70;
                    $pts = $vals->values()->map(function($v,$i) use($min,$range,$sw,$sh,$vals){
                        $x = ($i/($vals->count()-1))*$sw;
                        $y = $sh-(($v-$min)/$range)*($sh-12)-6;
                        return "{$x},{$y}";
                    })->implode(' ');
                    $area = $vals->values()->map(function($v,$i) use($min,$range,$sw,$sh,$vals){
                        $x = ($i/($vals->count()-1))*$sw;
                        $y = $sh-(($v-$min)/$range)*($sh-12)-6;
                        return "{$x},{$y}";
                    })->prepend("0,{$sh}")->push("{$sw},{$sh}")->implode(' ');
                @endphp
                <div class="trend-chart">
                    <svg viewBox="0 0 260 70" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="sparkGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%"   stop-color="#4CAF62" stop-opacity=".25"/>
                                <stop offset="100%" stop-color="#4CAF62" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <polygon points="{{ $area }}" fill="url(#sparkGrad)"/>
                        <polyline points="{{ $pts }}" fill="none" stroke="#4CAF62" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="trend-weeks">
                    @foreach($trendData['weeks'] as $wk)
                        <span class="trend-week-label">{{ $wk['label'] }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Plot highlight --}}
        <div class="plot-highlight" style="background:linear-gradient(135deg,#2D7A3A 0%,#4CAF62 100%)">
            <div class="plot-highlight-overlay">
                <div class="plot-cat">Plot Highlight</div>
                <div class="plot-name">Kandang Utama A</div>
            </div>
        </div>

    </div>{{-- /right-col --}}

</div>{{-- /pg-body --}}

{{-- ── Modal Penimbangan ──────────────────────────────────────────────── --}}
<div class="modal-backdrop" id="timbangModal">
    <div class="modal-box">
        <div class="modal-box__header">
            <h3 id="modalTitle">Timbang Domba</h3>
            <button class="modal-box__close" type="button" onclick="closeTimbangModal()">&#10005;</button>
        </div>
        <p id="modalSub" style="font-size:13px;color:var(--gf-gray-500);margin:0 0 20px"></p>

        <form id="timbangForm" method="POST" action="">
            @csrf
            <div class="modal-form-group" style="margin-bottom:14px">
                <label style="font-size:12px;font-weight:600;color:var(--gf-gray-700);display:block;margin-bottom:5px">Tanggal Timbang</label>
                <input type="date" name="tanggal_timbang" class="form-input"
                       value="{{ now()->format('Y-m-d') }}" required>
            </div>
            <div class="modal-form-group" style="margin-bottom:14px">
                <label style="font-size:12px;font-weight:600;color:var(--gf-gray-700);display:block;margin-bottom:5px">Berat (kg)</label>
                <input type="number" name="berat_kg" class="form-input"
                       step="0.01" min="0" placeholder="contoh: 45.50" required>
            </div>
            <div class="modal-form-group" style="margin-bottom:14px">
                <label style="font-size:12px;font-weight:600;color:var(--gf-gray-700);display:block;margin-bottom:5px">Catatan (opsional)</label>
                <textarea name="catatan" class="form-input" rows="2"
                          placeholder="Kondisi kesehatan, dll." style="resize:vertical"></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeTimbangModal()">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openTimbangModal(btn) {
    const ear  = btn.dataset.ear;
    const nama = btn.dataset.nama;
    const url  = btn.dataset.url;

    document.getElementById('modalSub').textContent   = 'Input berat timbangan untuk domba #' + ear + ' – ' + nama;
    document.getElementById('timbangForm').action     = url;

    document.getElementById('timbangModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeTimbangModal() {
    document.getElementById('timbangModal').classList.remove('show');
    document.body.style.overflow = '';
}

// Tutup modal jika klik backdrop
document.getElementById('timbangModal').addEventListener('click', function(e) {
    if (e.target === this) closeTimbangModal();
});

// Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeTimbangModal();
});
</script>
@endpush

@endsection