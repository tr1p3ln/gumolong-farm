@extends('layouts.app')

@section('page-title', 'Stok Pakan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/stok-pakan.css') }}">
@endpush

@section('content')
<div class="sp-page">

    {{-- ======================== BREADCRUMB & HEADER ======================== --}}
    <div class="sp-header">

        <div class="header-top">
            <div>
                <h1>Stok Pakan</h1>
            </div>
            <div class="header-actions">
                <button class="btn-outline" onclick="openModal('modal-masuk')">
                    <i class="bi bi-box-arrow-in-down"></i> 
                    Catat Pakan Masuk
                </button>
                <button class="btn-primary" onclick="openModal('modal-keluar')">
                    <i class="bi bi-box-arrow-up"></i>
                    Catat Pakan Keluar
                </button>
            </div>
        </div>
    </div>

    {{-- ======================== ALERT BANNERS ======================== --}}
    @if($peringatan->count() || $kritis->count())
    <div class="sp-alerts">
        @foreach($peringatan as $p)
        @php $infoPrediksi = collect($prediksi)->firstWhere('pakan_id', $p->pakan_id); @endphp
        <div class="sp-alert sp-alert-warning">
            <div class="sp-alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="sp-alert-body">
                <strong>Peringatan: Stok {{ $p->nama_pakan }} Menipis</strong>
                <span>Tersisa {{ number_format($p->jumlah_stok,0,',','.') }} {{ $p->satuan }}
                    @if($infoPrediksi && $infoPrediksi['sisa_hari'] !== null)
                        (Estimasi {{ $infoPrediksi['sisa_hari'] }} hari lagi).
                    @endif
                    Segera lakukan pengadaan.
                </span>
            </div>
            <button class="btn-alert" onclick="openModal('modal-masuk', {{ $p->pakan_id }})">Catat Masuk</button>
        </div>
        @endforeach

        @foreach($kritis as $k)
        <div class="sp-alert sp-alert-danger">
            <div class="sp-alert-icon"><i class="bi bi-x-octagon-fill"></i></div>
            <div class="sp-alert-body">
                <strong>Kritis: Stok {{ $k->nama_pakan }} Habis</strong>
                <span>Stok saat ini 0 {{ $k->satuan }}. Distribusi pakan harian terganggu.</span>
            </div>
            <button class="btn-alert" onclick="openModal('modal-masuk', {{ $k->pakan_id }})">Catat Masuk</button>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ======================== STOK CARDS ======================== --}}
    <div class="sp-stok-grid">
        @foreach($stokList as $stok)
        @php
            $status = $stok->status_stok;
            $pct    = $stok->persentase_kapasitas;
        @endphp
        <div class="stok-card">
            <div class="stok-card-header">
                <span class="stok-card-jenis">{{ strtoupper(str_replace('_', ' ', $stok->jenis)) }}</span>
                <span class="stok-badge badge-{{ $status }}">{{ strtoupper($status) }}</span>
            </div>
            <div class="stok-jumlah">
                {{ number_format($stok->jumlah_stok, 0, ',', '.') }}
                <span>{{ $stok->satuan }}</span>
            </div>
            <div class="stok-kapasitas-label">
                <span>KAPASITAS</span>
                <span>{{ $pct }}%</span>
            </div>
            <div class="progress-bar-wrap">
                <div class="progress-bar-fill fill-{{ $status }}" style="width: {{ $pct }}%"></div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ======================== PREDIKSI + FILTER ======================== --}}
    <div class="sp-two-col">

        {{-- Prediksi Restock --}}
        <div class="sp-card">
            <div class="sp-card-header">
                <div>
                    <h2>Prediksi Kebutuhan Restock</h2>
                    <p>Berdasarkan rata-rata pemberian pakan 30 hari terakhir</p>
                </div>
                <span class="badge-auto">AUTOMATED LOGIC</span>
            </div>
            <table class="restock-table">
                <thead>
                    <tr>
                        <th>Komoditas</th>
                        <th>Pemakaian Avg / Hari</th>
                        <th>Sisa Hari</th>
                        <th>Tgl Restock Disarankan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prediksi as $p)
                    @php
                        $sisaClass = 'sisa-normal';
                        if ($p['sisa_hari'] === null || $p['status_stok'] === 'habis') $sisaClass = 'sisa-danger';
                        elseif ($p['sisa_hari'] <= 3)  $sisaClass = 'sisa-danger';
                        elseif ($p['sisa_hari'] <= 7)  $sisaClass = 'sisa-warning';
                    @endphp
                    <tr>
                        <td>{{ $p['nama_pakan'] }}</td>
                        <td>{{ $p['avg_per_hari'] > 0 ? $p['avg_per_hari'].' '.$p['satuan'] : '-' }}</td>
                        <td class="{{ $sisaClass }}">
                            @if($p['status_stok'] === 'habis')
                                <span class="tag-stok-habis">STOK HABIS</span>
                            @elseif($p['sisa_hari'] === null)
                                <span style="color:var(--gf-gray-500)">-</span>
                            @else
                                {{ $p['sisa_hari'] }} Hari
                            @endif
                        </td>
                        <td>
                            @if($p['status_stok'] === 'habis')
                                <span class="tgl-danger">ASAP</span>
                            @elseif($p['tgl_restock'])
                                @if(($p['sisa_hari'] ?? 99) <= 3)
                                    <span class="tgl-danger">{{ $p['tgl_restock'] }}</span>
                                @elseif(($p['sisa_hari'] ?? 99) <= 7)
                                    <span class="tgl-warning"><a href="#">{{ $p['tgl_restock'] }}</a></span>
                                @else
                                    {{ $p['tgl_restock'] }}
                                @endif
                            @else
                                <span style="color:var(--gf-gray-500)">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Filter Log --}}
        <div class="sp-card filter-card">
            <h3>Filter Log Mutasi</h3>
            <form method="GET" action="{{ route('stok-pakan.index') }}">
                <div class="filter-group">
                    <label>Rentang Tanggal</label>
                    <input type="date" name="dari" class="form-input" value="{{ request('dari') }}" style="margin-bottom:6px">
                    <span style="display:block;text-align:center;font-size:11px;color:var(--gf-gray-500);margin-bottom:6px;">to</span>
                    <input type="date" name="sampai" class="form-input" value="{{ request('sampai') }}">
                </div>
                <div class="filter-group">
                    <label>Jenis Pakan</label>
                    <select name="jenis_pakan" class="form-select">
                        <option value="semua" {{ request('jenis_pakan','semua')==='semua'?'selected':'' }}>Semua Jenis</option>
                        @foreach($jenisOptions as $j)
                        <option value="{{ $j }}" {{ request('jenis_pakan')===$j?'selected':'' }}>
                            {{ ucfirst(str_replace('_',' ',$j)) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-filter"><i class="bi bi-funnel-fill"></i>
Terapkan Filter</button>
            </form>
        </div>
    </div>

    {{-- ======================== LOG PEMBERIAN PAKAN ======================== --}}
    <div class="sp-card">
        <div class="sp-card-header log-card-header">
            <div>
                <h2>Log Mutasi Stok Pakan</h2>
                <p>Data mutasi harian berdasarkan input operator</p>
            </div>
            <div class="log-actions">
                <button class="btn-icon" title="Export CSV" onclick="exportCSV()"><i class="bi bi-download"></i></button>
                <button class="btn-icon" title="Cetak" onclick="window.print()"><i class="bi bi-printer"></i></button>
            </div>
        </div>

        <div class="log-table-wrap">
            <table class="log-table">
                <thead>
                    <tr>
                        <th>Tanggal &amp; Waktu</th>
                        <th>Jenis Pakan</th>
                        <th>Nama Spesifik</th>
                        <th>Domba (Ear Tag)</th>
                        <th>Jumlah</th>
                        <th>Sisa Stok</th>
                        <th>User</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logPemberian as $log)
                    <tr>
                        <td>
                            <div class="tanggal">{{ $log->tanggal_pemberian->format('d M Y') }}</div>
                            <div class="waktu">{{ $log->tanggal_pemberian->format('H:i') }}</div>
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $log->pakanStok->jenis ?? '-')) }}</td>
                        <td>{{ $log->pakanStok->nama_pakan ?? '-' }}</td>
                        <td>
                            @if($log->domba)
                                <span class="ear-tag">{{ $log->ear_tag_id }}</span>
                                @if($log->domba->nama ?? null)
                                    <div style="font-size:11px;color:var(--gf-gray-500);margin-top:2px;">{{ $log->domba->nama }}</div>
                                @endif
                            @else
                                <span style="color:var(--gf-gray-500)">{{ $log->ear_tag_id }}</span>
                            @endif
                        </td>
                        <td class="jumlah-keluar">
                            &minus;{{ number_format($log->jumlah, 0, ',', '.') }} {{ $log->satuan }}
                        </td>
                        <td>{{ number_format($log->sisa_stok, 0, ',', '.') }} {{ $log->satuan }}</td>
                        <td style="font-size:12px;">{{ $log->user->name ?? '-' }}</td>
                        <td style="color:var(--gf-gray-700); font-size:12px;">{{ $log->keterangan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; color:var(--gf-gray-500); padding:32px;">
                            Belum ada data pemberian pakan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($logPemberian->lastPage() > 1)
        <div class="sp-pagination">
            @if($logPemberian->onFirstPage())
                <span><i class="bi bi-chevron-left"></i></span>
            @else
                <a href="{{ $logPemberian->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a>
            @endif

            @foreach($logPemberian->getUrlRange(1, $logPemberian->lastPage()) as $page => $url)
                @if($page == $logPemberian->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if($logPemberian->hasMorePages())
                <a href="{{ $logPemberian->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a>
            @else
                <span><i class="bi bi-chevron-right"></i></span>
            @endif
        </div>
        @endif
    </div>

</div>

{{-- ======================== MODAL: CATAT MASUK ======================== --}}
<div class="modal-backdrop" id="modal-masuk">
    <div class="modal-box">
        <h3>
            <i class="bi bi-box-arrow-in-down"></i>
            Catat Pakan Masuk
        </h3>
        <p>Tambahkan stok pakan yang baru diterima dari supplier</p>
        <form method="POST" action="{{ route('stok-pakan.masuk') }}">
            @csrf
            <div class="modal-form-group">
                <label>Jenis Pakan <span style="color:var(--gf-red)">*</span></label>
                <select name="pakan_id" id="masuk-pakan-id" class="form-select" required>
                    <option value="">-- Pilih Pakan --</option>
                    @foreach($stokList as $s)
                    <option value="{{ $s->pakan_id }}">
                        {{ $s->nama_pakan }} ({{ ucfirst(str_replace('_',' ',$s->jenis)) }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="modal-form-group">
                <label>Jumlah (kg) <span style="color:var(--gf-red)">*</span></label>
                <input type="number" name="jumlah" class="form-input" placeholder="Masukkan jumlah" min="0.01" step="0.01" required>
            </div>
            <div class="modal-form-group">
                <label>Keterangan</label>
                <input type="text" name="keterangan" class="form-input" placeholder="Cth: Pengiriman Supplier CV. Tani Maju">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('modal-masuk')">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ======================== MODAL: CATAT KELUAR (PEMBERIAN PAKAN) ======================== --}}
<div class="modal-backdrop" id="modal-keluar">
    <div class="modal-box">
        <h3>
            <i class="bi bi-box-arrow-up"></i>
            Catat Pakan Keluar
        </h3>
        <p>Catat pemberian pakan ke domba — stok akan otomatis berkurang</p>
        <form method="POST" action="{{ route('stok-pakan.keluar') }}">
            @csrf
            <div class="modal-form-group">
                <label>Jenis Pakan <span style="color:var(--gf-red)">*</span></label>
                <select name="pakan_id" id="keluar-pakan-id" class="form-select" required>
                    <option value="">-- Pilih Pakan --</option>
                    @foreach($stokList as $s)
                    <option value="{{ $s->pakan_id }}" data-stok="{{ $s->jumlah_stok }}" data-satuan="{{ $s->satuan }}">
                        {{ $s->nama_pakan }} — Stok: {{ number_format($s->jumlah_stok,0,',','.') }} {{ $s->satuan }}
                    </option>
                    @endforeach
                </select>
                <div class="form-hint" id="stok-hint"></div>
            </div>
            <div class="modal-form-group">
                <label>Ear Tag Domba <span style="color:var(--gf-red)">*</span></label>
                <select name="ear_tag_id" class="form-select" required>
                    <option value="">-- Pilih Domba --</option>
                    @foreach($dombaList ?? [] as $domba)
                    <option value="{{ $domba->ear_tag_id }}">
                        {{ $domba->ear_tag_id }}{{ $domba->nama ? ' — '.$domba->nama : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="modal-form-group">
                <label>Jumlah (kg) <span style="color:var(--gf-red)">*</span></label>
                <input type="number" name="jumlah" id="keluar-jumlah" class="form-input" placeholder="Masukkan jumlah" min="0.01" step="0.01" required>
            </div>
            <div class="modal-form-group">
                <label>Tanggal Pemberian</label>
                <input type="datetime-local" name="tanggal_pemberian" class="form-input"
                       value="{{ now()->format('Y-m-d\TH:i') }}">
            </div>
            <div class="modal-form-group">
                <label>Keterangan</label>
                <input type="text" name="keterangan" class="form-input" placeholder="Cth: Pemberian pakan pagi (Kandang A)">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('modal-keluar')">Batal</button>
                <button type="submit" class="btn-primary" style="background:var(--gf-red);">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ======================== TOAST ======================== --}}
@if(session('success'))
<div class="toast show" id="toast-success">
    <span class="toast-icon"><i class="bi bi-check-circle-fill"></i></span>
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="toast show" id="toast-error" style="border-color:#F5C6C4;">
    <span class="toast-icon" style="color:var(--gf-red);"><i class="bi bi-x-circle-fill"></i></span>
    {{ $errors->first() }}
</div>
@endif

@push('scripts')
<script>
    // ---- Modal helpers ----
    function openModal(id, pakanId) {
        document.getElementById(id).classList.add('open');
        if (pakanId) {
            const sel = document.querySelector('#' + id + ' select[name="pakan_id"]');
            if (sel) { sel.value = pakanId; sel.dispatchEvent(new Event('change')); }
        }
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }
    document.querySelectorAll('.modal-backdrop').forEach(el => {
        el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
    });

    // ---- Stok hint on modal keluar ----
    const keluarSel = document.getElementById('keluar-pakan-id');
    const stokHint  = document.getElementById('stok-hint');
    if (keluarSel) {
        keluarSel.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            const stok = opt.dataset.stok;
            const sat  = opt.dataset.satuan || 'kg';
            stokHint.textContent = stok !== undefined
                ? 'Stok tersedia: ' + parseFloat(stok).toLocaleString('id-ID') + ' ' + sat
                : '';
        });
    }

    // ---- Auto-dismiss toast ----
    ['toast-success','toast-error'].forEach(id => {
        const el = document.getElementById(id);
        if (el) setTimeout(() => el.classList.remove('show'), 4000);
    });

    // ---- Re-open modal on validation error ----
    @if($errors->has('ear_tag_id') || $errors->has('jumlah') || $errors->has('pakan_id'))
        document.addEventListener('DOMContentLoaded', () => openModal('modal-keluar'));
    @endif

    // ---- CSV Export ----
    function exportCSV() {
        const rows = [['Tanggal', 'Jenis Pakan', 'Nama Spesifik', 'Ear Tag', 'Jumlah', 'Sisa Stok', 'User', 'Keterangan']];
        document.querySelectorAll('.log-table tbody tr').forEach(tr => {
            const cells = tr.querySelectorAll('td');
            if (cells.length > 1) {
                rows.push([...cells].map(td => '"' + td.innerText.trim().replace(/"/g,'""') + '"'));
            }
        });
        const csv = rows.map(r => r.join(',')).join('\n');
        const a   = document.createElement('a');
        a.href     = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
        a.download = 'log-pemberian-pakan.csv';
        a.click();
    }
</script>
@endpush
@endsection