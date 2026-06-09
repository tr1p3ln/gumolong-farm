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
    .card-row {
        display: table;
        width: 100%;
        border-collapse: separate;
        border-spacing: 6px 0;
        margin-bottom: 4px;
    }
    .card {
        display: table-cell;
        background: {{ $theme['color_bg_alt'] }};
        border: 1px solid {{ $theme['color_border'] }};
        border-top: 3px solid {{ $theme['color_primary'] }};
        border-radius: 6px;
        padding: 10px 12px;
        text-align: center;
        vertical-align: top;
        width: 16.6%;
    }
    .card.red    { border-top-color: {{ $theme['color_danger'] }}; }
    .card.blue   { border-top-color: {{ $theme['color_info'] }}; }
    .card.amber  { border-top-color: {{ $theme['color_warning'] }}; }
    .card.earthy { border-top-color: {{ $theme['color_earthy'] }}; }

    .card-value {
        font-size: {{ $theme['font_size_card_val'] }};
        font-weight: bold;
        color: {{ $theme['color_text'] }};
        line-height: 1;
    }
    .card-value.red   { color: {{ $theme['color_danger'] }}; }
    .card-value.green { color: {{ $theme['color_primary'] }}; }
    .card-value.amber { color: {{ $theme['color_warning'] }}; }

    .card-label {
        font-size: {{ $theme['font_size_label'] }};
        color: {{ $theme['color_text_muted'] }};
        margin-top: 4px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .card-sub {
        font-size: {{ $theme['font_size_badge'] }};
        color: {{ $theme['color_text_light'] }};
        margin-top: 3px;
    }

    /* ── TABLES ── */
    table.data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: {{ $theme['font_size_table'] }};
    }
    table.data-table thead tr {
        background: {{ $theme['color_primary'] }};
        color: #fff;
    }
    table.data-table thead th {
        padding: 7px 10px;
        text-align: left;
        font-weight: 600;
        font-size: {{ $theme['font_size_small'] }};
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    table.data-table thead th.center { text-align: center; }
    table.data-table tbody tr:nth-child(even) { background: {{ $theme['color_bg_alt'] }}; }
    table.data-table tbody td {
        padding: 6px 10px;
        color: {{ $theme['color_table_text'] }};
        border-bottom: 1px solid {{ $theme['color_bg_row_hover'] }};
    }
    table.data-table tbody td.center { text-align: center; }
    table.data-table tbody td.right  { text-align: right; }
    table.data-table tbody td.bold   { font-weight: bold; color: {{ $theme['color_text'] }}; }

    /* ── PROGRESS BAR ── */
    .progress-wrap {
        background: {{ $theme['color_border'] }};
        border-radius: 4px;
        height: 6px;
        width: 100%;
        overflow: hidden;
        display: inline-block;
        vertical-align: middle;
    }
    .progress-fill { height: 100%; border-radius: 4px; }

    /* ── TWO COLUMN ── */
    .two-col   { display: table; width: 100%; border-collapse: separate; border-spacing: 10px 0; }
    .col-half  { display: table-cell; width: 50%; vertical-align: top; }

    /* ── DISTRIBUSI ── */
    .dist-row       { display: table; width: 100%; margin-bottom: 6px; }
    .dist-label     { display: table-cell; width: 60%; font-size: {{ $theme['font_size_table'] }}; color: {{ $theme['color_table_text'] }}; vertical-align: middle; }
    .dist-bar-cell  { display: table-cell; width: 25%; vertical-align: middle; padding: 0 6px; }
    .dist-value     { display: table-cell; width: 15%; text-align: right; font-weight: bold; font-size: {{ $theme['font_size_table'] }}; color: {{ $theme['color_text'] }}; vertical-align: middle; }

    /* ── FOOTER ── */
    .footer {
        margin-top: 24px;
        padding-top: 10px;
        border-top: 1px solid {{ $theme['color_border'] }};
        display: table;
        width: 100%;
    }
    .footer-left  { display: table-cell; font-size: {{ $theme['font_size_footer'] }}; color: {{ $theme['color_text_light'] }}; vertical-align: middle; }
    .footer-right { display: table-cell; text-align: right; font-size: {{ $theme['font_size_footer'] }}; color: {{ $theme['color_text_light'] }}; vertical-align: middle; }

    /* ── PAGE BREAK ── */
    .page-break { page-break-after: always; }
</style>
</head>
<body>

{{-- ══════════════════════════════════════════════
     HEADER
══════════════════════════════════════════════ --}}
<div class="header">
    <div class="header-inner">
        <div class="header-left">
            @if($theme['show_farm_logo'])
                <img src="{{ public_path('logo.png') }}" alt="{{ $theme['farm_name'] }}" style="height:40px; width:auto; margin-bottom:4px; display:block;">
            @endif
            <div class="farm-name">{{ $theme['farm_name'] }}</div>
            <div class="report-title">{{ $theme['report_title'] }}</div>
            <div class="report-meta">
                Periode: {{ now()->locale('id')->isoFormat('MMMM YYYY') }}
                &nbsp;&bull;&nbsp;
                Digenerate: {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB
            </div>
        </div>
        <div class="header-right">
            <span class="badge-generated">{{ $theme['badge_text'] }}</span>
            <div class="report-meta" style="margin-top:5px;">
                Oleh: {{ auth()->user()->nama ?? '-' }}<br>
                Role: {{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? '-')) }}
            </div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════
     SECTION 1: RINGKASAN POPULASI
══════════════════════════════════════════════ --}}
<div class="section-title">1. Ringkasan Populasi</div>

@php
    $fcrClass = $fcrValue !== null
        ? ($fcrValue <= $theme['fcr_good'] ? 'green' : ($fcrValue <= $theme['fcr_bad'] ? 'amber' : 'red'))
        : '';
@endphp

<div class="card-row">
    <div class="card">
        <div class="card-value green">{{ number_format($totalAktif) }}</div>
        <div class="card-label">Total Aktif</div>
        <div class="card-sub">Kapasitas {{ $persenOkupansi }}%</div>
    </div>
    <div class="card blue">
        <div class="card-value">{{ number_format($pejantan) }}</div>
        <div class="card-label">Pejantan</div>
        <div class="card-sub">{{ $totalAktif > 0 ? round(($pejantan/$totalAktif)*100) : 0 }}% populasi</div>
    </div>
    <div class="card earthy">
        <div class="card-value">{{ number_format($betina) }}</div>
        <div class="card-label">Betina</div>
        <div class="card-sub">{{ $totalAktif > 0 ? round(($betina/$totalAktif)*100) : 0 }}% populasi</div>
    </div>
    <div class="card {{ $mortalitasBulanIni > 0 ? 'red' : '' }}">
        <div class="card-value {{ $mortalitasBulanIni > 0 ? 'red' : '' }}">{{ $mortalitasBulanIni }}</div>
        <div class="card-label">Kematian</div>
        <div class="card-sub">Bulan ini</div>
    </div>
    <div class="card amber">
        <div class="card-value {{ $fcrClass }}">{{ $fcrValue ?? 'N/A' }}</div>
        <div class="card-label">FCR</div>
        <div class="card-sub">Ideal: ≤{{ $theme['fcr_good'] }}</div>
    </div>
    <div class="card">
        <div class="card-value">{{ number_format($pakanBulanIni, 0) }}</div>
        <div class="card-label">Pakan (kg)</div>
        <div class="card-sub">Bulan ini</div>
    </div>
</div>


{{-- ══════════════════════════════════════════════
     SECTION 2: DISTRIBUSI KATEGORI + KANDANG
══════════════════════════════════════════════ --}}
<div class="two-col" style="margin-top: 4px;">

    <div class="col-half">
        <div class="section-title">2. Distribusi per Kategori</div>
        @foreach($theme['kategori_colors'] as $kat => $color)
            @php $val = $byKategori[$kat] ?? 0; $pct = $totalAktif > 0 ? round(($val/$totalAktif)*100) : 0; @endphp
            <div class="dist-row">
                <div class="dist-label" style="text-transform:capitalize;">{{ $kat }}</div>
                <div class="dist-bar-cell">
                    <div class="progress-wrap">
                        <div class="progress-fill" style="width:{{ $pct }}%; background:{{ $color }};"></div>
                    </div>
                </div>
                <div class="dist-value">
                    {{ $val }}
                    <span style="color:{{ $theme['color_text_light'] }};font-weight:normal;">({{ $pct }}%)</span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="col-half">
        <div class="section-title">3. Kapasitas Kandang</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kandang</th>
                    <th class="center">Tipe</th>
                    <th class="center">Isi</th>
                    <th class="center">Kapasitas</th>
                    <th class="center">Terisi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kandangDetail as $k)
                    @php
                        $occColor = $k['pct'] >= $theme['occupancy_danger']
                            ? $theme['color_danger']
                            : ($k['pct'] >= $theme['occupancy_warning'] ? $theme['color_warning'] : $theme['color_primary']);
                    @endphp
                    <tr>
                        <td>{{ $k['nama'] }}</td>
                        <td class="center" style="text-transform:capitalize;">{{ $k['tipe'] }}</td>
                        <td class="center bold">{{ $k['isi'] }}</td>
                        <td class="center">{{ $k['kapasitas'] }}</td>
                        <td class="center">
                            <span style="color:{{ $occColor }}; font-weight:bold;">{{ $k['pct'] }}%</span>
                        </td>
                    </tr>
                @endforeach
                <tr style="background:{{ $theme['color_bg_row_hover'] }}; font-weight:bold;">
                    <td colspan="2"><strong>Total</strong></td>
                    <td class="center bold">{{ $totalAktif }}</td>
                    <td class="center bold">{{ $totalKapasitas }}</td>
                    <td class="center bold" style="color:{{ $persenOkupansi >= $theme['occupancy_danger'] ? $theme['color_danger'] : $theme['color_primary'] }};">
                        {{ $persenOkupansi }}%
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>


{{-- ══════════════════════════════════════════════
     SECTION 4: TREN 6 BULAN TERAKHIR
══════════════════════════════════════════════ --}}
<div class="section-title" style="margin-top:16px;">4. Tren Bulanan — 6 Bulan Terakhir</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Bulan</th>
            <th class="center">Berat Rata-rata (kg)</th>
            <th class="center">Kematian (ekor)</th>
            <th class="center">Kelahiran (ekor)</th>
            <th class="center">Konsumsi Pakan (kg)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($monthLabels as $i => $label)
            <tr>
                <td class="bold">{{ $label }}</td>
                <td class="center">
                    {{ $pertumbuhanData[$i] > 0 ? number_format($pertumbuhanData[$i], 1) : '—' }}
                </td>
                <td class="center" style="{{ $mortalitasData[$i] > 0 ? 'color:'.$theme['color_danger'].';font-weight:bold;' : '' }}">
                    {{ $mortalitasData[$i] > 0 ? $mortalitasData[$i] : '—' }}
                </td>
                <td class="center" style="{{ $reproduksiData[$i] > 0 ? 'color:'.$theme['color_primary'].';font-weight:bold;' : '' }}">
                    {{ $reproduksiData[$i] > 0 ? $reproduksiData[$i] : '—' }}
                </td>
                <td class="center">
                    {{ $pakanData[$i] > 0 ? number_format($pakanData[$i], 1) : '—' }}
                </td>
            </tr>
        @endforeach
        <tr style="background:{{ $theme['color_bg_row_hover'] }}; font-weight:bold; border-top:2px solid {{ $theme['color_border'] }};">
            <td><strong>Rata-rata</strong></td>
            <td class="center">
                @php $avg = collect($pertumbuhanData)->filter()->avg(); @endphp
                {{ $avg ? number_format($avg, 1) : '—' }}
            </td>
            <td class="center" style="{{ collect($mortalitasData)->sum() > 0 ? 'color:'.$theme['color_danger'].';' : '' }}">
                {{ collect($mortalitasData)->sum() ?: '—' }}
            </td>
            <td class="center" style="color:{{ $theme['color_primary'] }};">
                {{ collect($reproduksiData)->sum() ?: '—' }}
            </td>
            <td class="center">
                @php $avgPakan = collect($pakanData)->filter()->avg(); @endphp
                {{ $avgPakan ? number_format($avgPakan, 1) : '—' }}
            </td>
        </tr>
    </tbody>
</table>


{{-- ══════════════════════════════════════════════
     CATATAN INTERPRETASI (opsional)
══════════════════════════════════════════════ --}}
@if($theme['show_interpretation_notes'])
<div style="margin-top:16px; background:{{ $theme['color_primary_light'] }}; border:1px solid {{ $theme['color_primary_border'] }}; border-radius:6px; padding:10px 14px;">
    <div style="font-size:{{ $theme['font_size_table'] }}; font-weight:bold; color:{{ $theme['color_primary_dark'] }}; margin-bottom:5px;">&#9432; Catatan Interpretasi</div>
    <div style="font-size:{{ $theme['font_size_small'] }}; color:{{ $theme['color_table_text'] }}; line-height:1.6;">
        &bull; <strong>FCR (Feed Conversion Ratio)</strong>: nilai ideal domba adalah ≤{{ $theme['fcr_good'] }}.
        FCR ≤{{ $theme['fcr_good'] }} = efisien &nbsp;|&nbsp; FCR {{ $theme['fcr_good'] }}–{{ $theme['fcr_bad'] }} = perlu perhatian &nbsp;|&nbsp; FCR &gt;{{ $theme['fcr_bad'] }} = evaluasi pakan.<br>
        &bull; <strong>Kapasitas Kandang</strong>: hijau (&lt;{{ $theme['occupancy_warning'] }}%), kuning ({{ $theme['occupancy_warning'] }}–{{ $theme['occupancy_danger']-1 }}%), merah (≥{{ $theme['occupancy_danger'] }}%).<br>
        &bull; <strong>Tren Berat</strong>: penurunan berat rata-rata lebih dari 10% bulan-ke-bulan perlu penyelidikan.<br>
        &bull; Data diambil secara real-time pada saat laporan digenerate.
    </div>
</div>
@endif


{{-- ══════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════ --}}
<div class="footer">
    <div class="footer-left">
        {{ $theme['farm_name'] }} &mdash; {{ $theme['report_title'] }}<br>
        Digenerate oleh sistem pada {{ now()->format('d/m/Y H:i') }} WIB
    </div>
    <div class="footer-right">
        Dokumen ini digenerate otomatis oleh sistem.<br>
        Tidak memerlukan tanda tangan basah.
    </div>
</div>

</body>
</html>
