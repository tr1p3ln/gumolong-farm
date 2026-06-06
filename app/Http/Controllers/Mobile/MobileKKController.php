<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Domba;
use App\Models\TugasHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileKKController extends Controller
{
    public function dashboard()
    {
        $totalAktif = Domba::where('status', 'aktif')->count();
        $pejantan   = Domba::where('status', 'aktif')->where('jenis_kelamin', 'jantan')->count();
        $indukan    = Domba::where('status', 'aktif')->where('jenis_kelamin', 'betina')
                           ->where('kategori', 'indukan')->count();
        $anakan     = Domba::where('status', 'aktif')->where('kategori', 'anakan')->count();

        $healthAlerts    = DB::table('medical_record')->whereIn('status', ['sakit', 'dalam_perawatan'])->count();
        $pendingValidasi = DB::table('penimbangan')->where('status_validasi', 'pending')->count();

        $weightTrend = DB::table('penimbangan')
            ->selectRaw("TO_CHAR(DATE_TRUNC('month', tanggal_timbang), 'YYYY-MM') as bulan,
                         ROUND(AVG(berat_kg)::numeric, 1) as rata_rata")
            ->where('tanggal_timbang', '>=', now()->subMonths(6)->startOfMonth())
            ->groupByRaw("DATE_TRUNC('month', tanggal_timbang)")
            ->orderByRaw("DATE_TRUNC('month', tanggal_timbang)")
            ->pluck('rata_rata', 'bulan');

        $reproTrend = DB::table('kelahiran')
            ->selectRaw("TO_CHAR(DATE_TRUNC('month', tanggal_kelahiran), 'YYYY-MM') as bulan,
                         SUM(jml_anak_hidup) as total")
            ->where('tanggal_kelahiran', '>=', now()->subMonths(6)->startOfMonth())
            ->groupByRaw("DATE_TRUNC('month', tanggal_kelahiran)")
            ->orderByRaw("DATE_TRUNC('month', tanggal_kelahiran)")
            ->pluck('total', 'bulan');

        $hplMendatang = DB::table('perkawinan')
            ->join('domba', 'perkawinan.indukan_id', '=', 'domba.ear_tag_id')
            ->whereIn('perkawinan.status', ['bunting', 'menunggu_konfirmasi'])
            ->whereNotNull('estimasi_lahir')
            ->where('estimasi_lahir', '>=', today())
            ->orderBy('estimasi_lahir')
            ->limit(3)
            ->select('perkawinan.*', 'domba.nama', 'domba.ear_tag_id as indukan_tag')
            ->get();

        return view('mobile.kk.dashboard', compact(
            'totalAktif', 'pejantan', 'indukan', 'anakan',
            'healthAlerts', 'pendingValidasi',
            'weightTrend', 'reproTrend', 'hplMendatang'
        ));
    }

    public function monitorTugas()
    {
        $tanggal = today()->toDateString();

        $semua = TugasHarian::with(['kandang'])
            ->whereDate('tanggal', $tanggal)
            ->orderByRaw("CASE status
                WHEN 'dalam_proses' THEN 1
                WHEN 'belum'        THEN 2
                WHEN 'selesai'      THEN 3
                WHEN 'dilewati'     THEN 4
                END")
            ->get();

        $summary = [
            'total'        => $semua->count(),
            'selesai'      => $semua->where('status', 'selesai')->count(),
            'dalam_proses' => $semua->where('status', 'dalam_proses')->count(),
            'belum'        => $semua->where('status', 'belum')->count(),
            'persen'       => $semua->count() > 0
                ? round($semua->where('status', 'selesai')->count() / $semua->count() * 100)
                : 0,
        ];

        $perluValidasi = $semua->where('status', 'selesai')->values();
        $belumDikerjakan = $semua->where('status', 'belum')->values();

        return view('mobile.kk.monitor-tugas', compact(
            'semua', 'summary', 'perluValidasi', 'belumDikerjakan', 'tanggal'
        ));
    }

    public function kesehatan()
    {
        $laporan = DB::table('medical_record')
            ->join('domba', 'medical_record.ear_tag_id', '=', 'domba.ear_tag_id')
            ->whereIn('medical_record.status', ['sakit', 'dalam_perawatan'])
            ->orderBy('medical_record.tanggal_sakit', 'desc')
            ->select('medical_record.*', 'domba.nama', 'domba.jenis_kelamin')
            ->get();

        $ditangani = DB::table('medical_record')
            ->join('domba', 'medical_record.ear_tag_id', '=', 'domba.ear_tag_id')
            ->where('medical_record.status', 'sembuh')
            ->orderBy('medical_record.tanggal_sembuh', 'desc')
            ->limit(3)
            ->select('medical_record.*', 'domba.nama')
            ->get();

        return view('mobile.kk.kesehatan', compact('laporan', 'ditangani'));
    }

    public function konfirmasiKesehatan(Request $request, $rekamId)
    {
        $validated = $request->validate([
            'action' => 'required|in:sembuh,dalam_perawatan',
        ]);

        DB::table('medical_record')
            ->where('rekam_id', $rekamId)
            ->update([
                'status'         => $validated['action'],
                'tanggal_sembuh' => $validated['action'] === 'sembuh' ? today() : null,
                'updated_at'     => now(),
            ]);

        return redirect()->route('kk.kesehatan')
            ->with('success', 'Status kesehatan berhasil diperbarui.');
    }

    public function reproduksi()
    {
        $kelahiran = DB::table('kelahiran')
            ->join('perkawinan', 'kelahiran.kawin_id', '=', 'perkawinan.kawin_id')
            ->join('domba', 'perkawinan.indukan_id', '=', 'domba.ear_tag_id')
            ->orderBy('kelahiran.tanggal_kelahiran', 'desc')
            ->limit(10)
            ->select('kelahiran.*', 'domba.nama', 'domba.ear_tag_id as indukan_tag')
            ->get();

        $perkawinan = DB::table('perkawinan')
            ->join('domba as induk', 'perkawinan.indukan_id', '=', 'induk.ear_tag_id')
            ->join('domba as jantan', 'perkawinan.pejantan_id', '=', 'jantan.ear_tag_id')
            ->orderBy('perkawinan.tanggal_perkawinan', 'desc')
            ->limit(10)
            ->select('perkawinan.*', 'induk.nama as nama_induk', 'jantan.nama as nama_jantan')
            ->get();

        $kebuntingan = DB::table('perkawinan')
            ->join('domba', 'perkawinan.indukan_id', '=', 'domba.ear_tag_id')
            ->where('perkawinan.status', 'menunggu_konfirmasi')
            ->orderBy('perkawinan.tanggal_perkawinan', 'asc')
            ->select('perkawinan.*', 'domba.nama', 'domba.ear_tag_id as indukan_tag')
            ->get()
            ->map(function ($item) {
                $item->hari_sejak_kawin = \Carbon\Carbon::parse($item->tanggal_perkawinan)->diffInDays(today());
                return $item;
            });

        return view('mobile.kk.reproduksi', compact('kelahiran', 'perkawinan', 'kebuntingan'));
    }

    public function konfirmasiKebuntingan(Request $request, $kawinId)
    {
        $validated = $request->validate([
            'status' => 'required|in:bunting,tidak_bunting',
        ]);

        DB::table('perkawinan')
            ->where('kawin_id', $kawinId)
            ->update([
                'status'          => $validated['status'],
                'tgl_konfirmasi'  => today(),
                'dikonfirmasi_oleh' => auth()->id(),
                'updated_at'      => now(),
            ]);

        return redirect()->route('kk.reproduksi')
            ->with('success', 'Status kebuntingan berhasil diperbarui.');
    }

    public function validasiTimbangan()
    {
        $pending = DB::table('penimbangan')
            ->join('domba', 'penimbangan.ear_tag_id', '=', 'domba.ear_tag_id')
            ->where('penimbangan.status_validasi', 'pending')
            ->orderBy('penimbangan.created_at', 'desc')
            ->select('penimbangan.*', 'domba.nama', 'domba.jenis_kelamin')
            ->get()
            ->map(function ($item) {
                // Calculate ADG against previous record
                $sebelumnya = DB::table('penimbangan')
                    ->where('ear_tag_id', $item->ear_tag_id)
                    ->where('timbangan_id', '<', $item->timbangan_id)
                    ->where('status_validasi', 'valid')
                    ->orderBy('tanggal_timbang', 'desc')
                    ->first();
                $item->berat_sebelumnya = $sebelumnya?->berat_kg;
                return $item;
            });

        $tervalidasi = DB::table('penimbangan')
            ->join('domba', 'penimbangan.ear_tag_id', '=', 'domba.ear_tag_id')
            ->where('penimbangan.status_validasi', 'valid')
            ->orderBy('penimbangan.tanggal_timbang', 'desc')
            ->limit(6)
            ->select('penimbangan.*', 'domba.nama')
            ->get();

        $summary = [
            'total'   => DB::table('penimbangan')->whereDate('created_at', today())->count(),
            'valid'   => DB::table('penimbangan')->whereDate('created_at', today())->where('status_validasi', 'valid')->count(),
            'pending' => DB::table('penimbangan')->whereDate('created_at', today())->where('status_validasi', 'pending')->count(),
        ];

        return view('mobile.kk.validasi-timbangan', compact('pending', 'tervalidasi', 'summary'));
    }

    public function processValidasi(Request $request, $timbanganId)
    {
        $validated = $request->validate([
            'action' => 'required|in:valid,ditolak',
        ]);

        DB::table('penimbangan')
            ->where('timbangan_id', $timbanganId)
            ->update([
                'status_validasi'  => $validated['action'],
                'divalidasi_oleh'  => auth()->id(),
                'updated_at'       => now(),
            ]);

        return redirect()->route('kk.validasi-timbangan')
            ->with('success', $validated['action'] === 'valid' ? 'Data timbangan berhasil divalidasi.' : 'Data timbangan ditolak.');
    }
}
