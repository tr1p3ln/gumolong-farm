<?php

namespace App\Http\Controllers;

use App\Models\Domba;
use App\Models\Kandang;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Population stats ─────────────────────────────────────────
        $totalAktif = Domba::where('status', 'aktif')->count();
        $pejantan   = Domba::where('status', 'aktif')->where('jenis_kelamin', 'jantan')->count();
        $betina     = Domba::where('status', 'aktif')->where('jenis_kelamin', 'betina')->count();

        $byKategori = Domba::where('status', 'aktif')
            ->selectRaw('kategori, count(*) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        $kategoriLabels = ['anak', 'betina', 'induk', 'pejantan'];
        $kategoriData   = collect($kategoriLabels)
            ->map(fn ($k) => (int) ($byKategori[$k] ?? 0))
            ->toArray();

        // ── Mortality this month ──────────────────────────────────────
        $mortalitasBulanIni = Domba::where('status', 'mati')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        // ── Kandang occupancy ─────────────────────────────────────────
        $kandangList     = Kandang::all();
        $totalKapasitas  = $kandangList->sum('kapasitas');
        $persenOkupansi  = $totalKapasitas > 0
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
        // kelahiran.jml_anak_hidup = live offspring count per birth event
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
        // pemberian_pakan.jumlah_gram stored in grams → convert to kg for display
        $pakanRaw = DB::table('pemberian_pakan')
            ->selectRaw("TO_CHAR(DATE_TRUNC('month', tanggal_pemberian), 'YYYY-MM') as bulan,
                         ROUND((SUM(jumlah_gram) / 1000)::numeric, 1) as total")
            ->where('tanggal_pemberian', '>=', now()->subMonths(6)->startOfMonth())
            ->groupByRaw("DATE_TRUNC('month', tanggal_pemberian)")
            ->pluck('total', 'bulan');

        $pakanData = $months
            ->map(fn ($m) => (float) ($pakanRaw->get($m->format('Y-m')) ?? 0))
            ->toArray();

        // ── FCR summary (weight gained vs feed consumed, current month) ─
        $beratBulanIni = (float) DB::table('penimbangan')
            ->whereMonth('tanggal_timbang', now()->month)
            ->whereYear('tanggal_timbang', now()->year)
            ->avg('berat_kg') ?? 0;

        // jumlah_gram → kg
        $pakanBulanIni = (float) (DB::table('pemberian_pakan')
            ->whereMonth('tanggal_pemberian', now()->month)
            ->whereYear('tanggal_pemberian', now()->year)
            ->sum('jumlah_gram') / 1000);

        $fcrValue = ($beratBulanIni > 0 && $pakanBulanIni > 0)
            ? round($pakanBulanIni / ($beratBulanIni * max($totalAktif, 1)), 2)
            : null;

        return view('dashboard.index', compact(
            'totalAktif',
            'pejantan',
            'betina',
            'mortalitasBulanIni',
            'kandangList',
            'totalKapasitas',
            'persenOkupansi',
            'monthLabels',
            'kategoriLabels',
            'kategoriData',
            'pertumbuhanData',
            'mortalitasData',
            'reproduksiData',
            'pakanData',
            'fcrValue',
            'pakanBulanIni',
        ));
    }
}
