<?php

namespace App\Http\Controllers;

use App\Models\Domba;
use App\Models\Kandang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()?->role;

        if ($role === 'kepala_kandang') {
            return redirect()->route('kk.dashboard');
        }

        if ($role === 'pengurus_kandang') {
            return redirect()->route('pk.dashboard');
        }

        $data = $this->buildDashboardData();
        return view('dashboard.index', $data);
    }

    public function exportPdf()
    {
        $data  = $this->buildDashboardData();
        $theme = config('pdf_theme');

        $pdf = Pdf::loadView('dashboard.pdf', array_merge($data, ['theme' => $theme]))
            ->setPaper($theme['paper_size'], $theme['paper_orientation'])
            ->setOptions([
                'defaultFont'          => $theme['font_family'],
                'isRemoteEnabled'      => false,
                'isHtml5ParserEnabled' => true,
            ]);

        $filename = 'statistik-gumolong-farm-' . now()->format('Y-m-d') . '.pdf';

        // stream() = inline disposition → browser PDF viewer, tidak diinterfer IDM
        return $pdf->stream($filename);
    }

    private function buildDashboardData(): array
    {
        // ── Population stats ─────────────────────────────────────────
        $totalAktif = Domba::where('status', 'aktif')->count();
        $pejantan   = Domba::where('status', 'aktif')->where('jenis_kelamin', 'jantan')->count();
        $betina     = Domba::where('status', 'aktif')->where('jenis_kelamin', 'betina')->count();

        $byKategoriRaw = Domba::where('status', 'aktif')
            ->selectRaw('kategori, count(*) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori')
            ->toArray();

        $byKategori = array_map('intval', $byKategoriRaw);

        $kategoriLabels = ['cempe', 'dara', 'indukan', 'pejantan'];
        $kategoriData   = array_map(fn ($k) => $byKategori[$k] ?? 0, $kategoriLabels);

        // ── Mortality this month ──────────────────────────────────────
        $mortalitasBulanIni = Domba::where('status', 'mati')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        // ── Kandang occupancy ─────────────────────────────────────────
        $kandangList    = Kandang::all();
        $totalKapasitas = $kandangList->sum('kapasitas');
        $persenOkupansi = $totalKapasitas > 0
            ? min(100, round(($totalAktif / $totalKapasitas) * 100))
            : 0;

        // ── Last 6 months labels ──────────────────────────────────────
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->startOfMonth());

        $monthLabels = $months->map(fn ($m) => $m->locale('id')->isoFormat('MMM YYYY'))->toArray();

        // ── Weight growth chart (avg kg per month) ────────────────────
        $pertumbuhanRaw = DB::table('penimbangan')
            ->selectRaw("TO_CHAR(DATE_TRUNC('month', tanggal_timbang), 'YYYY-MM') as bulan,
                         ROUND(AVG(berat_kg)::numeric, 1) as rata_rata")
            ->where('tanggal_timbang', '>=', now()->subMonths(6)->startOfMonth())
            ->groupByRaw("DATE_TRUNC('month', tanggal_timbang)")
            ->pluck('rata_rata', 'bulan');

        $pertumbuhanData = $months
            ->map(fn ($m) => (float) ($pertumbuhanRaw->get($m->format('Y-m')) ?? 0))
            ->toArray();

        // ── Mortality per month chart ─────────────────────────────────
        $mortalitasRaw = DB::table('domba')
            ->selectRaw("TO_CHAR(DATE_TRUNC('month', updated_at), 'YYYY-MM') as bulan,
                         count(*) as total")
            ->where('status', 'mati')
            ->where('updated_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupByRaw("DATE_TRUNC('month', updated_at)")
            ->pluck('total', 'bulan');

        $mortalitasData = $months
            ->map(fn ($m) => (int) ($mortalitasRaw->get($m->format('Y-m')) ?? 0))
            ->toArray();

        // ── Reproduction (births) per month ───────────────────────────
        $reproduksiRaw = DB::table('kelahiran')
            ->selectRaw("TO_CHAR(DATE_TRUNC('month', tanggal_kelahiran), 'YYYY-MM') as bulan,
                         SUM(jml_anak_hidup) as total")
            ->where('tanggal_kelahiran', '>=', now()->subMonths(6)->startOfMonth())
            ->groupByRaw("DATE_TRUNC('month', tanggal_kelahiran)")
            ->pluck('total', 'bulan');

        $reproduksiData = $months
            ->map(fn ($m) => (int) ($reproduksiRaw->get($m->format('Y-m')) ?? 0))
            ->toArray();

        // ── Feed consumption per month (kg) ───────────────────────────
        $pakanRaw = DB::table('pemberian_pakan')
            ->selectRaw("TO_CHAR(DATE_TRUNC('month', tanggal_pemberian), 'YYYY-MM') as bulan,
                         ROUND((SUM(jumlah_gram) / 1000)::numeric, 1) as total")
            ->where('tanggal_pemberian', '>=', now()->subMonths(6)->startOfMonth())
            ->groupByRaw("DATE_TRUNC('month', tanggal_pemberian)")
            ->pluck('total', 'bulan');

        $pakanData = $months
            ->map(fn ($m) => (float) ($pakanRaw->get($m->format('Y-m')) ?? 0))
            ->toArray();

        // ── FCR summary ───────────────────────────────────────────────
        $beratBulanIni = (float) DB::table('penimbangan')
            ->whereMonth('tanggal_timbang', now()->month)
            ->whereYear('tanggal_timbang', now()->year)
            ->avg('berat_kg') ?? 0;

        $pakanBulanIni = (float) (DB::table('pemberian_pakan')
            ->whereMonth('tanggal_pemberian', now()->month)
            ->whereYear('tanggal_pemberian', now()->year)
            ->sum('jumlah_gram') / 1000);

        $fcrValue = ($beratBulanIni > 0 && $pakanBulanIni > 0)
            ? round($pakanBulanIni / ($beratBulanIni * max($totalAktif, 1)), 2)
            : null;

        // ── Kandang detail untuk PDF ──────────────────────────────────
        $kandangDetail = $kandangList->map(function ($kandang) {
            $isi = Domba::where('status', 'aktif')->where('kandang_id', $kandang->kandang_id)->count();
            $pct = $kandang->kapasitas > 0 ? min(100, round(($isi / $kandang->kapasitas) * 100)) : 0;
            return [
                'nama'      => $kandang->nama_kandang,
                'tipe'      => $kandang->tipe,
                'kapasitas' => $kandang->kapasitas,
                'isi'       => $isi,
                'pct'       => $pct,
            ];
        })->toArray();

        return compact(
            'totalAktif', 'pejantan', 'betina', 'byKategori',
            'mortalitasBulanIni', 'kandangList', 'kandangDetail',
            'totalKapasitas', 'persenOkupansi',
            'monthLabels', 'kategoriLabels', 'kategoriData',
            'pertumbuhanData', 'mortalitasData', 'reproduksiData', 'pakanData',
            'fcrValue', 'pakanBulanIni',
        );
    }
}
