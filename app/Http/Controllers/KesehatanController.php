<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KesehatanController extends Controller
{
    public function index(Request $request)
    {
        // ── KPI Cards ──────────────────────────────────────────
        $kpiSakit = DB::table('medical_record')
            ->whereIn('status', ['sakit', 'dalam_perawatan'])
            ->count();

        $kpiKarantina = DB::table('domba')
            ->where('status', 'karantina')
            ->count();

        $kpiSembuh = DB::table('medical_record')
            ->where('status', 'sembuh')
            ->whereMonth('tanggal_sembuh', now()->month)
            ->whereYear('tanggal_sembuh', now()->year)
            ->count();

        $weekStart = now()->startOfWeek()->toDateString();
        $weekEnd   = now()->endOfWeek()->toDateString();
        $kpiVaksinMingguIni = DB::table('vaksinasi')
            ->whereBetween('tanggal_vaksinasi', [$weekStart, $weekEnd])
            ->count();

        // ── Tab 1: Rekam Medis ─────────────────────────────────
        $rekamQuery = DB::table('medical_record as mr')
            ->join('domba as d', 'mr.ear_tag_id', '=', 'd.ear_tag_id')
            ->leftJoin('kandang as k', 'd.kandang_id', '=', 'k.kandang_id')
            ->select(
                'mr.rekam_id', 'mr.ear_tag_id', 'mr.tanggal_sakit',
                'mr.gejala', 'mr.diagnosa', 'mr.tanggal_sembuh', 'mr.status',
                'mr.created_at',
                'd.nama as nama_domba', 'd.kategori', 'd.ras',
                'd.status as domba_status', 'd.kandang_id',
                'k.nama_kandang'
            )
            ->orderByDesc('mr.tanggal_sakit');

        if ($request->filled('rm_search')) {
            $s = $request->rm_search;
            $rekamQuery->where(function ($q) use ($s) {
                $q->where('mr.ear_tag_id', 'ilike', "%$s%")
                  ->orWhere('d.nama', 'ilike', "%$s%")
                  ->orWhere('mr.gejala', 'ilike', "%$s%");
            });
        }
        if ($request->filled('rm_status')) {
            $rekamQuery->where('mr.status', $request->rm_status);
        }

        $rekamList = $rekamQuery->paginate(10, ['*'], 'rm_page')->withQueryString();

        $rekamIds     = $rekamList->pluck('rekam_id')->toArray();
        $pemakaianMap = DB::table('pemakaian_obat as po')
            ->join('obat_vaksin as ov', 'po.obat_id', '=', 'ov.obat_id')
            ->whereIn('po.rekam_id', $rekamIds)
            ->select('po.pakai_id', 'po.rekam_id', 'ov.nama_obat', 'po.jumlah', 'ov.satuan', 'po.tanggal_pakai')
            ->get()
            ->groupBy('rekam_id');

        // ── Tab 2: Karantina ───────────────────────────────────
        $karantinaList = DB::table('domba as d')
            ->join('kandang as k', 'd.kandang_id', '=', 'k.kandang_id')
            ->leftJoin(DB::raw(
                '(SELECT ear_tag_id, MAX(tanggal_sakit) as tanggal_masuk,
                    MAX(gejala) as alasan_karantina
                  FROM medical_record
                  WHERE status IN (\'sakit\',\'dalam_perawatan\')
                  GROUP BY ear_tag_id) as mr_latest'
            ), 'd.ear_tag_id', '=', 'mr_latest.ear_tag_id')
            ->where('d.status', 'karantina')
            ->whereNull('d.deleted_at')
            ->select(
                'd.ear_tag_id', 'd.nama', 'd.kategori', 'd.ras',
                'k.kandang_id', 'k.nama_kandang',
                'mr_latest.tanggal_masuk', 'mr_latest.alasan_karantina'
            )
            ->orderBy('d.ear_tag_id')
            ->get()
            ->map(function ($row) {
                $row->durasi_hari = $row->tanggal_masuk
                    ? Carbon::parse($row->tanggal_masuk)->diffInDays(now())
                    : null;
                return $row;
            });

        $kapasitasIsolasi = (int) DB::table('kandang')
            ->where('tipe', 'isolasi')
            ->sum('kapasitas');

        $rataKarantina = $karantinaList->filter(fn($r) => $r->durasi_hari !== null)->avg('durasi_hari');

        // ── Tab 3: Vaksinasi ───────────────────────────────────
        $vaksinasiQuery = DB::table('vaksinasi as v')
            ->join('domba as d', 'v.ear_tag_id', '=', 'd.ear_tag_id')
            ->join('obat_vaksin as ov', 'v.obat_id', '=', 'ov.obat_id')
            ->select(
                'v.vaksin_id', 'v.ear_tag_id', 'v.obat_id',
                'v.tanggal_vaksinasi', 'v.tanggal_berikutnya', 'v.catatan',
                'd.nama as nama_domba', 'd.kategori', 'd.ras',
                'ov.nama_obat', 'ov.satuan'
            )
            ->orderByDesc('v.tanggal_vaksinasi');

        if ($request->filled('vk_search')) {
            $s = $request->vk_search;
            $vaksinasiQuery->where(function ($q) use ($s) {
                $q->where('v.ear_tag_id', 'ilike', "%$s%")
                  ->orWhere('d.nama', 'ilike', "%$s%")
                  ->orWhere('ov.nama_obat', 'ilike', "%$s%");
            });
        }

        $vaksinasiList = $vaksinasiQuery->paginate(10, ['*'], 'vk_page')->withQueryString();

        $jadwalVaksinasi = DB::table('vaksinasi as v')
            ->join('domba as d', 'v.ear_tag_id', '=', 'd.ear_tag_id')
            ->join('obat_vaksin as ov', 'v.obat_id', '=', 'ov.obat_id')
            ->whereNotNull('v.tanggal_berikutnya')
            ->whereDate('v.tanggal_berikutnya', '>=', now()->toDateString())
            ->whereDate('v.tanggal_berikutnya', '<=', now()->addDays(14)->toDateString())
            ->select(
                'v.vaksin_id', 'v.ear_tag_id', 'v.tanggal_berikutnya',
                'd.nama as nama_domba', 'ov.nama_obat'
            )
            ->orderBy('v.tanggal_berikutnya')
            ->get();

        // ── Dropdown data for modals ───────────────────────────
        $dombaList = DB::table('domba')
            ->whereIn('status', ['aktif', 'karantina'])
            ->whereNull('deleted_at')
            ->select('ear_tag_id', 'nama', 'ras', 'kategori', 'status')
            ->orderBy('ear_tag_id')
            ->get();

        $obatList = DB::table('obat_vaksin')
            ->where('tipe', 'obat')
            ->where('stok', '>', 0)
            ->select('obat_id', 'nama_obat', 'satuan', 'stok')
            ->orderBy('nama_obat')
            ->get();

        $vaksinList = DB::table('obat_vaksin')
            ->where('tipe', 'vaksin')
            ->select('obat_id', 'nama_obat', 'satuan', 'stok')
            ->orderBy('nama_obat')
            ->get();

        $kandangList = DB::table('kandang')
            ->select('kandang_id', 'nama_kandang', 'tipe', 'kapasitas')
            ->orderBy('nama_kandang')
            ->get();

        return view('kesehatan.index', compact(
            'kpiSakit', 'kpiKarantina', 'kpiSembuh', 'kpiVaksinMingguIni',
            'rekamList', 'pemakaianMap',
            'karantinaList', 'kapasitasIsolasi', 'rataKarantina',
            'vaksinasiList', 'jadwalVaksinasi',
            'dombaList', 'obatList', 'vaksinList', 'kandangList'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ear_tag_id'           => 'required|exists:domba,ear_tag_id',
            'tanggal_sakit'        => 'required|date',
            'status'               => 'required|in:sakit,dalam_perawatan,sembuh,mati',
            'gejala'               => 'required|string|max:1000',
            'diagnosa'             => 'nullable|string|max:1000',
            'tanggal_sembuh'       => 'nullable|date',
            'tandai_karantina'     => 'nullable|in:ya,tidak',
            'kandang_karantina_id' => 'nullable|exists:kandang,kandang_id',
            'obat_ids'             => 'nullable|array',
            'obat_ids.*'           => 'exists:obat_vaksin,obat_id',
            'obat_jumlah'          => 'nullable|array',
            'obat_jumlah.*'        => 'nullable|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $rekamId = DB::table('medical_record')->insertGetId([
                'ear_tag_id'     => $request->ear_tag_id,
                'tanggal_sakit'  => $request->tanggal_sakit,
                'gejala'         => $request->gejala,
                'diagnosa'       => $request->diagnosa,
                'tanggal_sembuh' => $request->tanggal_sembuh,
                'status'         => $request->status,
                'created_at'     => now(),
                'updated_at'     => now(),
            ], 'rekam_id');

            foreach ($request->input('obat_ids', []) as $idx => $obatId) {
                $jumlah = (int) ($request->input("obat_jumlah.$idx") ?? 1);
                DB::table('pemakaian_obat')->insert([
                    'rekam_id'      => $rekamId,
                    'obat_id'       => $obatId,
                    'jumlah'        => $jumlah,
                    'tanggal_pakai' => $request->tanggal_sakit,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                DB::table('obat_vaksin')->where('obat_id', $obatId)->decrement('stok', $jumlah);
            }

            if ($request->input('tandai_karantina') === 'ya') {
                $dombaUpdate = ['status' => 'karantina', 'updated_at' => now()];
                if ($request->filled('kandang_karantina_id')) {
                    $dombaUpdate['kandang_id'] = $request->kandang_karantina_id;
                }
                DB::table('domba')->where('ear_tag_id', $request->ear_tag_id)->update($dombaUpdate);
            }
        });

        return redirect()->route('kesehatan.index', ['tab' => 'rekam-medis'])
            ->with('success', 'Rekam medis berhasil dicatat.');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'tanggal_sakit'        => 'required|date',
            'gejala'               => 'required|string|max:1000',
            'status'               => 'required|in:sakit,sembuh,mati,dalam_perawatan',
            'diagnosa'             => 'nullable|string|max:1000',
            'tanggal_sembuh'       => 'nullable|date',
            'tandai_karantina'     => 'nullable|in:ya,tidak',
            'kandang_karantina_id' => 'nullable|exists:kandang,kandang_id',
            'obat_ids'             => 'nullable|array',
            'obat_ids.*'           => 'exists:obat_vaksin,obat_id',
            'obat_jumlah'          => 'nullable|array',
            'obat_jumlah.*'        => 'nullable|integer|min:1',
        ]);

        $rekam = DB::table('medical_record')->where('rekam_id', $id)->first();
        abort_if(!$rekam, 404);

        DB::transaction(function () use ($request, $id, $rekam) {
            DB::table('medical_record')->where('rekam_id', $id)->update([
                'tanggal_sakit'  => $request->tanggal_sakit,
                'gejala'         => $request->gejala,
                'status'         => $request->status,
                'diagnosa'       => $request->diagnosa,
                'tanggal_sembuh' => $request->tanggal_sembuh,
                'updated_at'     => now(),
            ]);

            foreach ($request->input('obat_ids', []) as $idx => $obatId) {
                $jumlah = (int) ($request->input("obat_jumlah.$idx") ?? 1);
                DB::table('pemakaian_obat')->insert([
                    'rekam_id'      => $id,
                    'obat_id'       => $obatId,
                    'jumlah'        => $jumlah,
                    'tanggal_pakai' => $request->tanggal_sakit,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                DB::table('obat_vaksin')->where('obat_id', $obatId)->decrement('stok', $jumlah);
            }

            if ($request->input('tandai_karantina') === 'ya') {
                $dombaUpdate = ['status' => 'karantina', 'updated_at' => now()];
                if ($request->filled('kandang_karantina_id')) {
                    $dombaUpdate['kandang_id'] = $request->kandang_karantina_id;
                }
                DB::table('domba')->where('ear_tag_id', $rekam->ear_tag_id)->update($dombaUpdate);
            } elseif ($request->status === 'sembuh') {
                DB::table('domba')->where('ear_tag_id', $rekam->ear_tag_id)
                    ->where('status', 'karantina')
                    ->update(['status' => 'aktif', 'updated_at' => now()]);
            } elseif ($request->status === 'mati') {
                DB::table('domba')->where('ear_tag_id', $rekam->ear_tag_id)
                    ->update(['status' => 'mati', 'updated_at' => now()]);
            }
        });

        return redirect()->route('kesehatan.index', ['tab' => 'rekam-medis'])
            ->with('success', 'Rekam medis berhasil diperbarui.');
    }

    public function destroyPemakaianObat(string $rekamId, string $pakaiId)
    {
        $pakai = DB::table('pemakaian_obat')
            ->where('pakai_id', $pakaiId)
            ->where('rekam_id', $rekamId)
            ->first();
        abort_if(!$pakai, 404);

        DB::table('obat_vaksin')->where('obat_id', $pakai->obat_id)->increment('stok', $pakai->jumlah);
        DB::table('pemakaian_obat')->where('pakai_id', $pakaiId)->delete();

        return redirect()->route('kesehatan.index', ['tab' => 'rekam-medis'])
            ->with('success', 'Pemakaian obat berhasil dihapus dan stok dikembalikan.');
    }

    public function destroy(string $id)
    {
        $rekam = DB::table('medical_record')->where('rekam_id', $id)->first();
        abort_if(!$rekam, 404);

        DB::table('pemakaian_obat')->where('rekam_id', $id)->delete();
        DB::table('medical_record')->where('rekam_id', $id)->delete();

        return redirect()->route('kesehatan.index', ['tab' => 'rekam-medis'])
            ->with('success', 'Rekam medis berhasil dihapus.');
    }

    public function storeVaksinasi(Request $request)
    {
        $request->validate([
            'ear_tag_id'         => 'required|exists:domba,ear_tag_id',
            'obat_id'            => 'required|exists:obat_vaksin,obat_id',
            'tanggal_vaksinasi'  => 'required|date',
            'tanggal_berikutnya' => 'nullable|date|after:tanggal_vaksinasi',
            'catatan'            => 'nullable|string|max:500',
        ]);

        DB::table('vaksinasi')->insert([
            'ear_tag_id'         => $request->ear_tag_id,
            'obat_id'            => $request->obat_id,
            'tanggal_vaksinasi'  => $request->tanggal_vaksinasi,
            'tanggal_berikutnya' => $request->tanggal_berikutnya,
            'catatan'            => $request->catatan,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        return redirect()->route('kesehatan.index', ['tab' => 'vaksinasi'])
            ->with('success', 'Data vaksinasi berhasil disimpan.');
    }

    public function updateVaksinasi(Request $request, string $id)
    {
        $request->validate([
            'tanggal_vaksinasi'  => 'required|date',
            'tanggal_berikutnya' => 'nullable|date|after:tanggal_vaksinasi',
            'catatan'            => 'nullable|string|max:500',
        ]);

        DB::table('vaksinasi')->where('vaksin_id', $id)->update([
            'tanggal_vaksinasi'  => $request->tanggal_vaksinasi,
            'tanggal_berikutnya' => $request->tanggal_berikutnya,
            'catatan'            => $request->catatan,
            'updated_at'         => now(),
        ]);

        return redirect()->route('kesehatan.index', ['tab' => 'vaksinasi'])
            ->with('success', 'Data vaksinasi berhasil diperbarui.');
    }

    public function destroyVaksinasi(string $id)
    {
        DB::table('vaksinasi')->where('vaksin_id', $id)->delete();

        return redirect()->route('kesehatan.index', ['tab' => 'vaksinasi'])
            ->with('success', 'Data vaksinasi berhasil dihapus.');
    }

    public function pindahKandang(Request $request, string $earTagId)
    {
        $request->validate([
            'kandang_id'   => 'required|exists:kandang,kandang_id',
            'status_rekam' => 'nullable|in:sembuh,dalam_perawatan',
            'tanggal_pindah' => 'nullable|date',
        ]);

        $kandang = DB::table('kandang')->where('kandang_id', $request->kandang_id)->first();

        DB::transaction(function () use ($request, $earTagId, $kandang) {
            DB::table('domba')->where('ear_tag_id', $earTagId)->update([
                'kandang_id' => $request->kandang_id,
                'updated_at' => now(),
            ]);

            if ($kandang && $kandang->tipe !== 'isolasi') {
                DB::table('domba')->where('ear_tag_id', $earTagId)
                    ->where('status', 'karantina')
                    ->update(['status' => 'aktif', 'updated_at' => now()]);
            }

            if ($request->filled('status_rekam')) {
                $latestRekam = DB::table('medical_record')
                    ->where('ear_tag_id', $earTagId)
                    ->whereIn('status', ['sakit', 'dalam_perawatan'])
                    ->orderByDesc('tanggal_sakit')
                    ->first();

                if ($latestRekam) {
                    DB::table('medical_record')->where('rekam_id', $latestRekam->rekam_id)->update([
                        'status'         => $request->status_rekam,
                        'tanggal_sembuh' => $request->status_rekam === 'sembuh'
                            ? ($request->tanggal_pindah ?? now()->toDateString())
                            : null,
                        'updated_at'     => now(),
                    ]);

                    if ($request->status_rekam === 'sembuh') {
                        DB::table('domba')->where('ear_tag_id', $earTagId)
                            ->update(['status' => 'aktif', 'updated_at' => now()]);
                    }
                }
            }
        });

        return redirect()->route('kesehatan.index', ['tab' => 'karantina'])
            ->with('success', 'Domba berhasil dipindahkan.');
    }
}
