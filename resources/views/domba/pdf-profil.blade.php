<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: {{ $theme['font_family'] }};
        font-size: {{ $theme['font_size_base'] }};
        color: {{ $theme['color_text'] }};
        background: #fff;
        padding: {{ $theme['body_padding'] }};
    }

    /* ── HEADER ── */
    .header {
        border-bottom: 2.5px solid {{ $theme['color_primary'] }};
        padding-bottom: 14px;
        margin-bottom: 20px;
    }
    .header-inner { display: table; width: 100%; }
    .header-left  { display: table-cell; vertical-align: middle; width: 70%; }
    .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 30%; }

    .farm-name {
        font-size: {{ $theme['font_size_farmname'] }};
        font-weight: bold;
        color: {{ $theme['color_primary'] }};
        letter-spacing: 0.5px;
    }
    .report-title {
        font-size: 11px;
        font-weight: bold;
        color: {{ $theme['color_text'] }};
        margin-top: 3px;
    }
    .report-meta {
        font-size: {{ $theme['font_size_small'] }};
        color: {{ $theme['color_text_muted'] }};
        margin-top: 2px;
    }
    .badge-generated {
        background: {{ $theme['color_primary'] }};
        color: #fff;
        font-size: {{ $theme['font_size_badge'] }};
        font-weight: bold;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-block;
    }

    /* ── SECTION TITLE ── */
    .section-title {
        font-size: {{ $theme['font_size_section'] }};
        font-weight: bold;
        color: {{ $theme['color_primary'] }};
        text-transform: uppercase;
        letter-spacing: 0.8px;
        border-left: 3px solid {{ $theme['color_primary'] }};
        padding-left: 8px;
        margin: {{ $theme['section_spacing'] }} 0 10px;
    }

    /* ── STAT CARDS ── */
    .card-row { display: table; width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 12px; }
    .card {
        display: table-cell;
        background: {{ $theme['color_bg_alt'] }};
        border: 1px solid {{ $theme['color_border'] }};
        border-top: 3px solid {{ $theme['color_primary'] }};
        border-radius: 6px;
        padding: 10px 12px;
        text-align: center;
        vertical-align: top;
        width: 25%;
    }
    .card.red { border-top-color: {{ $theme['color_danger'] }}; }

    .card-value { font-size: 16px; font-weight: bold; color: {{ $theme['color_text'] }}; line-height: 1; }
    .card-label { font-size: {{ $theme['font_size_label'] }}; color: {{ $theme['color_text_muted'] }}; margin-top: 4px; font-weight: 600; text-transform: uppercase; }

    /* ── TABLES ── */
    table.data-table { width: 100%; border-collapse: collapse; font-size: {{ $theme['font_size_table'] }}; margin-bottom: 16px; }
    table.data-table thead tr { background: {{ $theme['color_primary'] }}; color: #fff; }
    table.data-table thead th { padding: 7px 10px; text-align: left; font-weight: 600; font-size: {{ $theme['font_size_small'] }}; text-transform: uppercase; }
    table.data-table tbody tr:nth-child(even) { background: {{ $theme['color_bg_alt'] }}; }
    table.data-table tbody td { padding: 6px 10px; color: {{ $theme['color_table_text'] }}; border-bottom: 1px solid {{ $theme['color_bg_row_hover'] }}; }

    /* Layout Utility */
    .two-col { display: table; width: 100%; border-collapse: separate; border-spacing: 10px 0; margin-bottom: 16px; }
    .col-half { display: table-cell; width: 50%; vertical-align: top; }

    .info-table { width: 100%; border-collapse: collapse; font-size: {{ $theme['font_size_table'] }}; }
    .info-table td { padding: 6px 0; border-bottom: 1px dashed {{ $theme['color_border'] }}; }
    .info-table td.label { font-weight: bold; color: {{ $theme['color_text_muted'] }}; width: 35%; text-transform: uppercase; font-size: {{ $theme['font_size_label'] }}; }

    .footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid {{ $theme['color_border'] }}; display: table; width: 100%; }
    .footer-left  { display: table-cell; font-size: {{ $theme['font_size_footer'] }}; color: {{ $theme['color_text_light'] }}; vertical-align: middle; }
    .footer-right { display: table-cell; text-align: right; font-size: {{ $theme['font_size_footer'] }}; color: {{ $theme['color_text_light'] }}; vertical-align: middle; }
    .page-break { page-break-after: always; }
</style>
</head>
<body>

@php
    $umur = $domba->tanggal_lahir ? \Carbon\Carbon::parse($domba->tanggal_lahir)->diffForHumans(null, true) : '-';
    $beratTerkini = $domba->penimbangan->first();
@endphp

<div class="header">
    <div class="header-inner">
        <div class="header-left">
            <div class="farm-name">{{ $theme['farm_name'] }}</div>
            <div class="report-title">PROFIL LENGKAP DOMBA — {{ $domba->ear_tag_id }}</div>
            <div class="report-meta">Digenerate: {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB</div>
        </div>
        <div class="header-right">
            <span class="badge-generated">{{ $theme['badge_text'] }}</span>
        </div>
    </div>
</div>

{{-- HIGHLIGHT CARDS --}}
<div class="card-row">
    <div class="card">
        <div class="card-value">{{ $beratTerkini ? number_format($beratTerkini->berat_kg, 1) . ' kg' : '-' }}</div>
        <div class="card-label">Berat Terkini</div>
    </div>
    <div class="card">
        <div class="card-value">{{ $umur }}</div>
        <div class="card-label">Umur</div>
    </div>
    <div class="card {{ $domba->status === 'karantina' ? 'red' : '' }}">
        <div class="card-value" style="{{ $domba->status === 'karantina' ? 'color:'.$theme['color_danger'] : 'color:'.$theme['color_primary'] }}">
            {{ ucfirst($domba->status) }}
        </div>
        <div class="card-label">Status Domba</div>
    </div>
    <div class="card">
        <div class="card-value">{{ number_format($domba->total_pakan_30_hari ?? 0, 2) }} kg</div>
        <div class="card-label">Pakan (30 Hari)</div>
    </div>
</div>

{{-- IDENTITAS DOMBA --}}
<div class="section-title">A. Identitas Domba</div>
<div class="two-col">
    <div class="col-half">
        <table class="info-table">
            <tr><td class="label">Ear Tag ID</td><td><strong>{{ $domba->ear_tag_id }}</strong></td></tr>
            <tr><td class="label">Nama</td><td>{{ $domba->nama ?? '-' }}</td></tr>
            <tr><td class="label">Jenis Kelamin</td><td style="text-transform: capitalize;">{{ $domba->jenis_kelamin }}</td></tr>
            <tr><td class="label">Kategori</td><td style="text-transform: capitalize;">{{ $domba->kategori }}</td></tr>
            <tr><td class="label">Ras</td><td>{{ $domba->ras }}</td></tr>
        </table>
    </div>
    <div class="col-half">
        <table class="info-table">
            <tr><td class="label">Kandang</td><td>{{ $domba->kandang->nama_kandang ?? '-' }}</td></tr>
            <tr><td class="label">Asal</td><td style="text-transform: capitalize;">{{ str_replace('_', ' ', $domba->asal) }}</td></tr>
            <tr><td class="label">Tanggal Lahir</td><td>{{ $domba->tanggal_lahir ? \Carbon\Carbon::parse($domba->tanggal_lahir)->isoFormat('D MMMM YYYY') : '-' }}</td></tr>
            <tr><td class="label">Induk (Dam)</td><td>{{ $domba->induk_id ?? '-' }}</td></tr>
            <tr><td class="label">Pejantan (Sire)</td><td>{{ $domba->ayah_id ?? '-' }}</td></tr>
        </table>
    </div>
</div>

{{-- RIWAYAT KESEHATAN --}}
<div class="section-title">B. Rekam Medis & Penyakit</div>
<table class="data-table">
    <thead>
        <tr>
            <th>Tgl Sakit</th>
            <th>Gejala & Diagnosa</th>
            <th>Status</th>
            <th>Tgl Sembuh</th>
        </tr>
    </thead>
    <tbody>
        @forelse($domba->medical_record as $rekam)
        <tr>
            <td>{{ \Carbon\Carbon::parse($rekam->tanggal_sakit)->format('d/m/Y') }}</td>
            <td><strong>{{ $rekam->diagnosa ?: 'Belum Ada Diagnosa' }}</strong><br><span style="font-size:8.5px;color:#6B7280;">{{ $rekam->gejala }}</span></td>
            <td style="text-transform:capitalize;">{{ str_replace('_', ' ', $rekam->status) }}</td>
            <td>{{ $rekam->tanggal_sembuh ? \Carbon\Carbon::parse($rekam->tanggal_sembuh)->format('d/m/Y') : '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center; font-style:italic;">Domba ini sehat (tidak memiliki riwayat penyakit).</td></tr>
        @endforelse
    </tbody>
</table>

{{-- RIWAYAT VAKSINASI --}}
<div class="section-title">C. Log Vaksinasi & Proteksi</div>
<table class="data-table">
    <thead>
        <tr>
            <th>Tanggal Vaksin</th>
            <th>Nama Obat/Vaksin</th>
            <th>Jadwal Berikutnya</th>
            <th>Catatan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($domba->vaksinasi as $vaksin)
        <tr>
            <td>{{ \Carbon\Carbon::parse($vaksin->tanggal_vaksinasi)->format('d/m/Y') }}</td>
            <td><strong>{{ $vaksin->nama_obat }}</strong></td>
            <td>{{ $vaksin->tanggal_berikutnya ? \Carbon\Carbon::parse($vaksin->tanggal_berikutnya)->format('d/m/Y') : '-' }}</td>
            <td>{{ $vaksin->catatan ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center; font-style:italic;">Belum ada log vaksinasi.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- RIWAYAT PENIMBANGAN (Max 10 Data Terakhir) --}}
<div class="section-title">D. Riwayat Pertumbuhan Berdasarkan Timbangan</div>
<table class="data-table">
    <thead>
        <tr>
            <th>Tanggal Timbang</th>
            <th style="text-align:center;">Berat (kg)</th>
            <th style="text-align:center;">ADG (kg/hari)</th>
            <th>Catatan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($domba->penimbangan->take(10) as $timbang)
        <tr>
            <td>{{ \Carbon\Carbon::parse($timbang->tanggal_timbang)->format('d/m/Y') }}</td>
            <td style="text-align:center; font-weight:bold;">{{ number_format($timbang->berat_kg, 1) }}</td>
            <td style="text-align:center;">{{ $timbang->adg ? '+' . number_format($timbang->adg, 3) : '-' }}</td>
            <td>{{ $timbang->catatan ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center; font-style:italic;">Belum ada data penimbangan.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    <div class="footer-left">
        {{ $theme['farm_name'] }} &mdash; Detail Domba {{ $domba->ear_tag_id }}
    </div>
    <div class="footer-right">
        Dokumen Profil Internal
    </div>
</div>
</body>
</html>
