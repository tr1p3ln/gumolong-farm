@extends('layouts.app')

@section('page-title', 'Detail Pertumbuhan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pertumbuhan.css') }}">
@endpush

@section('content')

<div class="pg-header">
    <div>
        <h2 class="pg-title">
            Detail Pertumbuhan - {{ $domba->nama }}
        </h2>
        
    </div>

    <a href="{{ route('tracking-pertumbuhan.index') }}" class="btn-reset">
        ← Kembali
    </a>
</div>

{{-- Ringkasan --}}
<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-label">Berat Terakhir</div>
        <div class="stat-value">
            {{ number_format($latestPenimbangan?->berat_kg ?? 0,1) }}
            <span class="unit">kg</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-label">ADG Terakhir</div>
        <div class="stat-value">
            {{ number_format($latestPenimbangan?->adg ?? 0,2) }}
            <span class="unit">kg/hari</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Jumlah Penimbangan</div>
        <div class="stat-value">
            {{ $riwayat->count() }}
            <span class="unit">log</span>
        </div>
    </div>

</div>

{{-- Profil Domba --}}
<div class="panel" style="margin-bottom:20px">
    <div class="panel-header">
        <span class="panel-title">Informasi Domba</span>
    </div>

    <div style="padding:20px">
        <table style="width:100%">
            <tr>
                <td><strong>Nama</strong></td>
                <td>{{ $domba->nama }}</td>
            </tr>

            <tr>
                <td><strong>Ras</strong></td>
                <td>{{ $domba->ras }}</td>
            </tr>

            <tr>
                <td><strong>Kategori</strong></td>
                <td>{{ ucfirst($domba->kategori) }}</td>
            </tr>

            <tr>
                <td><strong>Jenis Kelamin</strong></td>
                <td>{{ ucfirst($domba->jenis_kelamin) }}</td>
            </tr>

            <tr>
                <td><strong>Status</strong></td>
                <td>{{ ucfirst($domba->status) }}</td>
            </tr>
        </table>
    </div>
</div>

{{-- Grafik --}}
<div class="panel" style="margin-bottom:20px">
    <div class="panel-header">
        <span class="panel-title">Grafik Pertumbuhan Berat Badan</span>
    </div>

    <div style="padding:20px">
        <canvas id="chartBerat"></canvas>
    </div>
</div>

{{-- Riwayat --}}
<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Berat (kg)</th>
                <th>ADG</th>
                <th>Catatan</th>
            </tr>
        </thead>

        <tbody>
            @forelse($riwayat as $item)
                <tr>
                    <td>
                        {{ \Carbon\Carbon::parse($item->tanggal_timbang)->format('d M Y') }}
                    </td>

                    <td>
                        {{ number_format($item->berat_kg,1) }}
                    </td>

                    <td>
                        {{ number_format($item->adg,2) }}
                    </td>

                    <td>
                        {{ $item->catatan ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        Belum ada data penimbangan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('chartBerat');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($riwayat->pluck('tanggal_timbang')->map(fn($d)=>\Carbon\Carbon::parse($d)->format('d M'))->values()) !!},

        datasets: [{
            label: 'Berat Badan (kg)',
            data: {!! json_encode($riwayat->pluck('berat_kg')->values()) !!},
            tension: 0.4
        }]
    }
});
</script>
@endpush