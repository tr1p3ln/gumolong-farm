<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReproduksiController extends Controller
{
    public function index(Request $request)
    {
        // ── KPI Cards ──────────────────────────────────────────
        $kpiBunting  = DB::table('perkawinan')->where('status', 'bunting')->count();

        $kpiHpl14 = DB::table('perkawinan')
            ->where('status', 'bunting')
            ->whereNotNull('estimasi_lahir')
            ->whereDate('estimasi_lahir', '>=', now()->toDateString())
            ->whereDate('estimasi_lahir', '<=', now()->addDays(14)->toDateString())
            ->count();

        $kpiKelahiranBulan = DB::table('kelahiran')
            ->whereMonth('tanggal_kelahiran', now()->month)
            ->whereYear('tanggal_kelahiran', now()->year)
            ->count();

        $kpiAnakHidup = (int) DB::table('kelahiran')
            ->whereMonth('tanggal_kelahiran', now()->month)
            ->whereYear('tanggal_kelahiran', now()->year)
            ->sum('jml_anak_hidup');

        $kpiLahirTotal = DB::table('perkawinan')->where('status', 'lahir')->count();
        $kpiGagalTotal = DB::table('perkawinan')->whereIn('status', ['gagal', 'tidak_bunting'])->count();
        $kpiProsesTotal = DB::table('perkawinan')->whereIn('status', ['menunggu_konfirmasi', 'bunting'])->count();
        $kpiSuccessRate = ($kpiLahirTotal + $kpiGagalTotal) > 0
            ? round($kpiLahirTotal / ($kpiLahirTotal + $kpiGagalTotal) * 100)
            : 0;

        // ── Tab 1: Perkawinan ──────────────────────────────────
        $pkQuery = DB::table('perkawinan as p')
            ->join('domba as pj',  'p.pejantan_id', '=', 'pj.ear_tag_id')
            ->join('domba as ind', 'p.indukan_id',  '=', 'ind.ear_tag_id')
            ->leftJoin('user as u', 'p.user_id', '=', 'u.user_id')
            ->select(
                'p.kawin_id', 'p.pejantan_id', 'p.indukan_id',
                'p.tanggal_perkawinan', 'p.metode', 'p.estimasi_lahir', 'p.status',
                'pj.nama as pejantan_nama', 'pj.ras as pejantan_ras',
                'ind.nama as indukan_nama', 'ind.ras as indukan_ras',
                'u.nama as dicatat_oleh'
            )
            ->orderByDesc('p.tanggal_perkawinan');

        if ($request->filled('pk_search')) {
            $s = $request->pk_search;
            $pkQuery->where(function ($q) use ($s) {
                $q->where('p.pejantan_id', 'ilike', "%$s%")
                  ->orWhere('p.indukan_id', 'ilike', "%$s%");
            });
        }
        if ($request->filled('pk_status')) {
            $pkQuery->where('p.status', $request->pk_status);
        }
        if ($request->filled('pk_metode')) {
            $pkQuery->where('p.metode', $request->pk_metode);
        }

        $perkawinanList = $pkQuery->paginate(10, ['*'], 'pk_page')->withQueryString();

        // ── Tab 2: Kebuntingan & HPL ───────────────────────────
        $kebuntinganList = DB::table('perkawinan as p')
            ->join('domba as pj',  'p.pejantan_id', '=', 'pj.ear_tag_id')
            ->join('domba as ind', 'p.indukan_id',  '=', 'ind.ear_tag_id')
            ->where('p.status', 'bunting')
            ->select(
                'p.kawin_id', 'p.pejantan_id', 'p.indukan_id',
                'p.tanggal_perkawinan', 'p.estimasi_lahir',
                'pj.ras as pejantan_ras', 'ind.ras as indukan_ras'
            )
            ->orderBy('p.estimasi_lahir', 'asc')
            ->get()
            ->map(function ($row) {
                $hpl = Carbon::parse($row->estimasi_lahir);
                // diffInDays(now, false): negative means now is ahead of $hpl (past due)
                $row->hari_tersisa = (int) ($hpl->diffInDays(now(), false) * -1);
                $row->progress     = max(0, min(100, round((150 - max(0, $row->hari_tersisa)) / 150 * 100)));
                if ($row->hari_tersisa <= 3)       $row->alert = 'kritis';
                elseif ($row->hari_tersisa <= 14)  $row->alert = 'warning';
                else                               $row->alert = 'terpantau';
                return $row;
            });

        // ── Tab 3: Kelahiran ───────────────────────────────────
        $lhrQuery = DB::table('kelahiran as k')
            ->join('perkawinan as p', 'k.kawin_id',    '=', 'p.kawin_id')
            ->join('domba as pj',     'p.pejantan_id', '=', 'pj.ear_tag_id')
            ->join('domba as ind',    'p.indukan_id',  '=', 'ind.ear_tag_id')
            ->leftJoin('user as u',   'k.user_id',     '=', 'u.user_id')
            ->select(
                'k.lahir_id', 'k.kawin_id', 'k.tanggal_kelahiran',
                'k.jml_anak_hidup', 'k.jml_anak_mati', 'k.bobot_rata_rata', 'k.catatan',
                'p.pejantan_id', 'p.indukan_id',
                'pj.nama as pejantan_nama', 'ind.nama as indukan_nama',
                'u.nama as dicatat_oleh'
            )
            ->orderByDesc('k.tanggal_kelahiran');

        if ($request->filled('lhr_search')) {
            $s = $request->lhr_search;
            $lhrQuery->where(function ($q) use ($s) {
                $q->where('p.indukan_id', 'ilike', "%$s%")
                  ->orWhere('p.pejantan_id', 'ilike', "%$s%");
            });
        }

        $kelahiranList = $lhrQuery->paginate(10, ['*'], 'lhr_page')->withQueryString();

        $lahirIds    = $kelahiranList->pluck('lahir_id')->toArray();
        $anakLahirMap = DB::table('anak_lahir')
            ->whereIn('lahir_id', $lahirIds)
            ->get()
            ->groupBy('lahir_id');

        $lhrStats = DB::table('kelahiran')
            ->whereMonth('tanggal_kelahiran', now()->month)
            ->whereYear('tanggal_kelahiran', now()->year)
            ->selectRaw('COUNT(*) as total_kasus, SUM(jml_anak_hidup) as total_hidup, SUM(jml_anak_mati) as total_mati')
            ->first();

        // ── Dropdown data for modals ───────────────────────────
        $pejantanList = DB::table('domba')
            ->where('kategori', 'pejantan')
            ->where('status', 'aktif')
            ->whereNull('deleted_at')
            ->select('ear_tag_id', 'nama', 'ras')
            ->orderBy('ear_tag_id')
            ->get();

        $indukanList = DB::table('domba')
            ->where('kategori', 'indukan')
            ->where('status', 'aktif')
            ->whereNull('deleted_at')
            ->select('ear_tag_id', 'nama', 'ras')
            ->orderBy('ear_tag_id')
            ->get();

        $perkawinanUntukKelahiran = DB::table('perkawinan as p')
            ->join('domba as ind', 'p.indukan_id',  '=', 'ind.ear_tag_id')
            ->join('domba as pj',  'p.pejantan_id', '=', 'pj.ear_tag_id')
            ->where('p.status', 'bunting')
            ->select(
                'p.kawin_id', 'p.pejantan_id', 'p.indukan_id', 'p.estimasi_lahir',
                'ind.ras as indukan_ras', 'pj.ras as pejantan_ras'
            )
            ->orderBy('p.estimasi_lahir')
            ->get();

        $perkawinanMenunggu = DB::table('perkawinan as p')
            ->join('domba as ind', 'p.indukan_id',  '=', 'ind.ear_tag_id')
            ->join('domba as pj',  'p.pejantan_id', '=', 'pj.ear_tag_id')
            ->where('p.status', 'menunggu_konfirmasi')
            ->select(
                'p.kawin_id', 'p.pejantan_id', 'p.indukan_id',
                'p.tanggal_perkawinan', 'p.estimasi_lahir',
                'ind.ras as indukan_ras', 'pj.ras as pejantan_ras'
            )
            ->orderByDesc('p.tanggal_perkawinan')
            ->get();

        return view('reproduksi.index', [
            'kpiBunting'        => $kpiBunting,
            'kpiHpl14'          => $kpiHpl14,
            'kpiKelahiranBulan' => $kpiKelahiranBulan,
            'kpiAnakHidup'      => $kpiAnakHidup,
            'kpiLahirTotal'     => $kpiLahirTotal,
            'kpiGagalTotal'     => $kpiGagalTotal,
            'kpiProsesTotal'    => $kpiProsesTotal,
            'kpiSuccessRate'    => $kpiSuccessRate,
            'perkawinan'  => $perkawinanList,
            'kebuntingan' => $kebuntinganList,
            'kelahiran'   => $kelahiranList,
            'anakLahirMap'           => $anakLahirMap,
            'lhrStats'               => $lhrStats,
            'pejantanList'           => $pejantanList,
            'indukanList'            => $indukanList,
            'perkawinanUntukKelahiran' => $perkawinanUntukKelahiran,
            'perkawinanMenunggu'     => $perkawinanMenunggu,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pejantan_id'        => 'required|exists:domba,ear_tag_id',
            'indukan_ids'        => 'required|array|min:1',
            'indukan_ids.*'      => 'required|exists:domba,ear_tag_id',
            'tanggal_perkawinan' => 'required|date',
            'metode'             => 'required|in:alami,inseminasi_buatan',
        ]);

        $tgl    = Carbon::parse($request->tanggal_perkawinan);
        $hpl    = $tgl->copy()->addDays(150)->toDateString();
        $userId = auth()->id();

        foreach ($request->indukan_ids as $indukanId) {
            DB::table('perkawinan')->insert([
                'pejantan_id'        => $request->pejantan_id,
                'indukan_id'         => $indukanId,
                'user_id'            => $userId,
                'tanggal_perkawinan' => $request->tanggal_perkawinan,
                'metode'             => $request->metode,
                'estimasi_lahir'     => $hpl,
                'status'             => 'menunggu_konfirmasi',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }

        $count = count($request->indukan_ids);
        return redirect()->route('reproduksi.index', ['tab' => 'perkawinan'])
            ->with('success', "$count record perkawinan berhasil disimpan.");
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'tanggal_perkawinan' => 'required|date',
            'metode'             => 'required|in:alami,inseminasi_buatan',
            'status'             => 'required|in:menunggu_konfirmasi,bunting,tidak_bunting,lahir,gagal',
        ]);

        $hpl = Carbon::parse($request->tanggal_perkawinan)->addDays(150)->toDateString();

        DB::table('perkawinan')->where('kawin_id', $id)->update([
            'tanggal_perkawinan' => $request->tanggal_perkawinan,
            'metode'             => $request->metode,
            'estimasi_lahir'     => $hpl,
            'status'             => $request->status,
            'updated_at'         => now(),
        ]);

        return redirect()->route('reproduksi.index', ['tab' => 'perkawinan'])
            ->with('success', 'Data perkawinan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        if (DB::table('kelahiran')->where('kawin_id', $id)->exists()) {
            return redirect()->route('reproduksi.index', ['tab' => 'perkawinan'])
                ->with('error', 'Tidak dapat dihapus — sudah ada data kelahiran terkait.');
        }

        DB::table('perkawinan')->where('kawin_id', $id)->delete();
        return redirect()->route('reproduksi.index', ['tab' => 'perkawinan'])
            ->with('success', 'Data perkawinan berhasil dihapus.');
    }

    public function konfirmasi(Request $request, string $id)
    {
        $perkawinan = DB::table('perkawinan')->where('kawin_id', $id)->first();
        abort_if(!$perkawinan, 404, 'Perkawinan tidak ditemukan.');
        abort_if($perkawinan->status !== 'menunggu_konfirmasi', 422, 'Perkawinan ini sudah dikonfirmasi.');

        $request->validate([
            'hasil'              => 'required|in:bunting,tidak_bunting',
            'metode_konfirmasi'  => 'required|in:USG,observasi_fisik',
            'tgl_konfirmasi'     => 'required|date',
            'catatan_konfirmasi' => 'nullable|string|max:500',
        ]);

        DB::table('perkawinan')->where('kawin_id', $id)->update([
            'status'             => $request->hasil,
            'tgl_konfirmasi'     => $request->tgl_konfirmasi,
            'metode_konfirmasi'  => $request->metode_konfirmasi,
            'catatan_konfirmasi' => $request->catatan_konfirmasi,
            'dikonfirmasi_oleh'  => auth()->id(),
            'updated_at'         => now(),
        ]);

        return redirect()->route('reproduksi.index', ['tab' => 'perkawinan'])
            ->with('success', 'Konfirmasi kebuntingan berhasil disimpan.');
    }

    public function storeKelahiran(Request $request, string $kawin_id)
    {
        $request->validate([
            'tanggal_kelahiran'    => 'required|date',
            'jml_anak_hidup'       => 'required|integer|min:0',
            'jml_anak_mati'        => 'required|integer|min:0',
            'bobot_rata_rata'      => 'nullable|numeric|min:0',
            'catatan'              => 'nullable|string',
            'anak'                 => 'nullable|array',
            'anak.*.jenis_kelamin' => 'required_with:anak|in:jantan,betina',
            'anak.*.bobot_lahir'   => 'nullable|numeric|min:0',
            'anak.*.kondisi'       => 'required_with:anak|in:hidup,mati',
            'anak.*.ear_tag_id'    => 'nullable|string|max:20|distinct|unique:domba,ear_tag_id',
        ]);

        // Ambil data perkawinan + indukan untuk mengisi data domba baru
        $perkawinan = DB::table('perkawinan')->where('kawin_id', $kawin_id)->first();
        abort_if(!$perkawinan, 404, 'Perkawinan tidak ditemukan.');
        $indukan = DB::table('domba')->where('ear_tag_id', $perkawinan->indukan_id)->first();

        $dombaRegistered = 0;

        DB::transaction(function () use ($request, $kawin_id, $perkawinan, $indukan, &$dombaRegistered) {
            $lahirId = DB::table('kelahiran')->insertGetId([
                'kawin_id'          => $kawin_id,
                'user_id'           => auth()->id(),
                'tanggal_kelahiran' => $request->tanggal_kelahiran,
                'jml_anak_hidup'    => $request->jml_anak_hidup,
                'jml_anak_mati'     => $request->jml_anak_mati,
                'bobot_rata_rata'   => $request->bobot_rata_rata,
                'catatan'           => $request->catatan,
                'created_at'        => now(),
                'updated_at'        => now(),
            ], 'lahir_id');

            foreach ($request->input('anak', []) as $anak) {
                $earTagId = !empty($anak['ear_tag_id']) ? trim($anak['ear_tag_id']) : null;

                // Insert domba DULU sebelum anak_lahir (FK constraint)
                if ($earTagId && $indukan) {
                    $sudahAda = DB::table('domba')->where('ear_tag_id', $earTagId)->exists();
                    if (!$sudahAda) {
                        DB::table('domba')->insert([
                            'ear_tag_id'    => $earTagId,
                            'jenis_kelamin' => $anak['jenis_kelamin'],
                            'ras'           => $indukan->ras,
                            'kategori'      => 'cempe',
                            'status'        => 'aktif',
                            'asal'          => 'lahir_di_kandang',
                            'tanggal_lahir' => $request->tanggal_kelahiran,
                            'kandang_id'    => $indukan->kandang_id,
                            'induk_id'      => $perkawinan->indukan_id,
                            'ayah_id'       => $perkawinan->pejantan_id,
                            'catatan'       => 'Lahir dari perkawinan KWN-' . $kawin_id,
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ]);
                        $dombaRegistered++;
                    }
                }

                // Baru insert anak_lahir
                DB::table('anak_lahir')->insert([
                    'lahir_id'      => $lahirId,
                    'ear_tag_id'    => $earTagId,
                    'jenis_kelamin' => $anak['jenis_kelamin'],
                    'bobot_lahir'   => $anak['bobot_lahir'] ?: null,
                    'kondisi'       => $anak['kondisi'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            DB::table('perkawinan')->where('kawin_id', $kawin_id)->update([
                'status'     => 'lahir',
                'updated_at' => now(),
            ]);
        });

        $msg = 'Data kelahiran berhasil disimpan.';
        if ($dombaRegistered > 0) {
            $msg .= " {$dombaRegistered} anak domba otomatis terdaftar di Data Domba (kategori: Cempe).";
        }

        return redirect()->route('reproduksi.index', ['tab' => 'kelahiran'])
            ->with('success', $msg);
    }

    public function updateKelahiran(Request $request, string $kawin_id, string $lahir_id)
    {
        $request->validate([
            'tanggal_kelahiran' => 'required|date',
            'jml_anak_hidup'    => 'required|integer|min:0',
            'jml_anak_mati'     => 'required|integer|min:0',
            'bobot_rata_rata'   => 'nullable|numeric|min:0',
            'catatan'           => 'nullable|string',
        ]);

        DB::table('kelahiran')->where('lahir_id', $lahir_id)->update([
            'tanggal_kelahiran' => $request->tanggal_kelahiran,
            'jml_anak_hidup'    => $request->jml_anak_hidup,
            'jml_anak_mati'     => $request->jml_anak_mati,
            'bobot_rata_rata'   => $request->bobot_rata_rata,
            'catatan'           => $request->catatan,
            'updated_at'        => now(),
        ]);

        return redirect()->route('reproduksi.index', ['tab' => 'kelahiran'])
            ->with('success', 'Data kelahiran berhasil diperbarui.');
    }

    public function destroyKelahiran(string $kawin_id, string $lahir_id)
    {
        $kelahiran = DB::table('kelahiran')->where('lahir_id', $lahir_id)->first();
        if ($kelahiran) {
            DB::table('anak_lahir')->where('lahir_id', $lahir_id)->delete();
            DB::table('kelahiran')->where('lahir_id', $lahir_id)->delete();
            DB::table('perkawinan')->where('kawin_id', $kelahiran->kawin_id)->update([
                'status'     => 'bunting',
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('reproduksi.index', ['tab' => 'kelahiran'])
            ->with('success', 'Data kelahiran berhasil dihapus.');
    }
}
