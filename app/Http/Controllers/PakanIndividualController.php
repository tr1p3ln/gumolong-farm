<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\FCRCalculatorService;

class PakanIndividualController extends Controller
{
    public function index(Request $request)
    {
       // ── Base query ────────────────────────────────────────────────
    $baseQuery = DB::table('domba as d')
        ->leftJoin(
            DB::raw('(SELECT DISTINCT ON (ear_tag_id) ear_tag_id, berat_kg FROM penimbangan ORDER BY ear_tag_id, tanggal_timbang DESC) as p_latest'),
            'd.ear_tag_id', '=', 'p_latest.ear_tag_id'
        )
        ->leftJoin(
            DB::raw('(SELECT ear_tag_id, SUM(jumlah_gram) as pakan_hari_ini FROM pemberian_pakan WHERE tanggal_pemberian = CURRENT_DATE GROUP BY ear_tag_id) as pp_today'),
            'd.ear_tag_id', '=', 'pp_today.ear_tag_id'
        )
        ->leftJoin(
            DB::raw('(SELECT ear_tag_id, SUM(jumlah_gram) as total_pakan_30hr FROM pemberian_pakan WHERE tanggal_pemberian >= CURRENT_DATE - INTERVAL \'30 days\' GROUP BY ear_tag_id) as pp_30'),
            'd.ear_tag_id', '=', 'pp_30.ear_tag_id'
        )
        ->leftJoin(
            DB::raw('(SELECT DISTINCT ON (ear_tag_id) ear_tag_id, berat_kg FROM penimbangan ORDER BY ear_tag_id, tanggal_timbang ASC) as p_awal'),
            'd.ear_tag_id', '=', 'p_awal.ear_tag_id'
        )
        ->select(
            'd.ear_tag_id', 'd.nama', 'd.kategori', 'd.ras',
            'p_latest.berat_kg',
            'pp_today.pakan_hari_ini',
            'pp_30.total_pakan_30hr',
            DB::raw("
                CASE
                    WHEN pp_30.total_pakan_30hr IS NOT NULL
                    AND pp_30.total_pakan_30hr > 0
                    AND p_latest.berat_kg IS NOT NULL
                    AND p_awal.berat_kg IS NOT NULL
                    AND (p_latest.berat_kg - p_awal.berat_kg) > 0
                    THEN ROUND(
                        (pp_30.total_pakan_30hr / 1000.0) /
                        (p_latest.berat_kg - p_awal.berat_kg),
                    2)
                    ELSE NULL
                END as fcr
            ")
        )
        ->where('d.status', 'aktif')
        ->whereNull('d.deleted_at');

    // ── Filter search & kategori (di inner query) ─────────────────
    if ($request->filled('search')) {
        $baseQuery->where(function ($q) use ($request) {
            $q->where('d.ear_tag_id', 'ilike', "%{$request->search}%")
            ->orWhere('d.nama', 'ilike', "%{$request->search}%");
        });
    }
    if ($request->filled('kategori')) {
        $baseQuery->where('d.kategori', $request->kategori);
    }

    // ── Wrap jadi subquery → baru filter FCR di luar ──────────────
    $outerQuery = DB::table(DB::raw("({$baseQuery->toSql()}) as sub"))
    ->addBinding($baseQuery->getBindings(), 'where')
    ->select('*');

    if ($request->filled('status_fcr')) {
        $fcrCondition = match($request->status_fcr) {
            'efisien' => 'fcr < 5.0',
            'normal'  => 'fcr >= 5.0 AND fcr <= 7.0',
            'boros'   => 'fcr > 7.0',
            default   => null,
        };
        if ($fcrCondition) {
            $outerQuery->whereRaw($fcrCondition);
        }
    }

    $dombaFcr = $outerQuery->orderBy('ear_tag_id')->paginate(10)->withQueryString();

// ── Metric cards ──────────────────────────────────────────────
$allFcr = DB::table('domba as d')
    ->leftJoin(
        DB::raw('(
            SELECT DISTINCT ON (ear_tag_id) ear_tag_id, berat_kg
            FROM penimbangan
            ORDER BY ear_tag_id, tanggal_timbang DESC
        ) as p_akhir'),
        'd.ear_tag_id', '=', 'p_akhir.ear_tag_id'
    )
    ->leftJoin(
        DB::raw('(
            SELECT DISTINCT ON (ear_tag_id) ear_tag_id, berat_kg
            FROM penimbangan
            ORDER BY ear_tag_id, tanggal_timbang ASC
        ) as p_awal'),
        'd.ear_tag_id', '=', 'p_awal.ear_tag_id'
    )
    ->leftJoin(
        DB::raw('(
            SELECT ear_tag_id, SUM(jumlah_gram) / 1000.0 as total_kg
            FROM pemberian_pakan
            GROUP BY ear_tag_id
        ) as pp'),
        'd.ear_tag_id', '=', 'pp.ear_tag_id'
    )
    ->select(
        'd.ear_tag_id', 'd.nama', 'd.kategori',
        DB::raw("
            CASE
                WHEN pp.total_kg IS NOT NULL
                AND pp.total_kg > 0
                AND p_akhir.berat_kg IS NOT NULL
                AND p_awal.berat_kg IS NOT NULL
                AND (p_akhir.berat_kg - p_awal.berat_kg) > 0
                THEN ROUND(pp.total_kg / (p_akhir.berat_kg - p_awal.berat_kg), 2)
                ELSE NULL
            END as fcr
        ")
    )
    ->where('d.status', 'aktif')
    ->whereNull('d.deleted_at')
    ->get()
    ->filter(fn($r) => $r->fcr !== null);
    $avgFcr      = $allFcr->avg('fcr') ?? 0;
    $fcrTerbaik  = $allFcr->sortBy('fcr')->first();
    $fcrTerburuk = $allFcr->sortByDesc('fcr')->first();

// ── Dropdown data ─────────────────────────────────────────────
$dombaList = DB::table('domba')
    ->where('status', 'aktif')
    ->whereNull('deleted_at')
    ->select('ear_tag_id', 'nama', 'kategori')
    ->orderBy('ear_tag_id')
    ->get();

$pakanList = DB::table('pakan_stok')
    ->select('pakan_id', 'nama_pakan', 'jenis', 'jumlah_stok')
    ->orderBy('jenis')
    ->get();
        // ── Rekomendasi pakan (domba pertama) ─────────────────────────
        $firstDomba       = $dombaList->first();
        $rekomendasiPakan = collect();
        $totalPakanHarian = 0;

        if ($firstDomba) {
            $rekomendasiPakan = $this->getRekomendasiPakan($firstDomba->ear_tag_id);
            $totalPakanHarian = $rekomendasiPakan->sum('total_gram');
        }

        return view('pakan-individual.index', compact(
            'dombaFcr',
            'avgFcr',
            'fcrTerbaik',
            'fcrTerburuk',
            'dombaList',
            'pakanList',
            'rekomendasiPakan',
            'totalPakanHarian'
        ));
    }

    // ── Store pemberian pakan (AJAX) ──────────────────────────────────
    public function store(Request $request)
{
    $validated = $request->validate([
        'ear_tag_id'        => 'required|exists:domba,ear_tag_id',
        'pakan_id'          => 'required|exists:pakan_stok,pakan_id',
        'tanggal_pemberian' => 'required|date',
        'sesi'              => 'required|in:pagi,sore',
        'jumlah_gram'       => 'required|numeric|min:1',
    ]);
 
    // Stok di DB dalam KG, input form dalam GRAM
    $stokSekarang = DB::table('pakan_stok')
        ->where('pakan_id', $validated['pakan_id'])
        ->value('jumlah_stok'); // KG
 
    $jumlahKg = $validated['jumlah_gram'] / 1000; // gram → kg
 
    if ($stokSekarang !== null && $stokSekarang < $jumlahKg) {
        return response()->json([
            'success' => false,
            'message' => 'Stok tidak mencukupi. Tersedia: ' . number_format($stokSekarang, 2) . ' kg.',
        ], 422);
    }

    DB::beginTransaction();
    try {
        // Insert — hanya kolom yang ADA di tabel pemberian_pakan
        DB::table('pemberian_pakan')->insert([
            'pakan_id'          => $validated['pakan_id'],
            'ear_tag_id'        => $validated['ear_tag_id'],
            'user_id'           => auth()->id(),
            'tanggal_pemberian' => $validated['tanggal_pemberian'],
            'sesi'              => $validated['sesi'],
            'jumlah_gram'       => $validated['jumlah_gram'],
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
 
        // Kurangi stok dalam KG
        if ($stokSekarang !== null) {
            DB::table('pakan_stok')
                ->where('pakan_id', $validated['pakan_id'])
                ->decrement('jumlah_stok', $jumlahKg);
        }
 
        DB::commit();
 
        return response()->json([
            'success' => true,
            'message' => 'Pemberian pakan berhasil dicatat.',
        ]);
 
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        ], 500);
    }
}

    // ── Detail domba ──────────────────────────────────────────────────
    public function show(string $earTagId)
    {
        return redirect()->route('pakan-individual.index');
    }

    // ── Helper: rekomendasi pakan per domba ───────────────────────────
    private function getRekomendasiPakan(string $earTagId): \Illuminate\Support\Collection
    {
        $warnaMap = [
            'rumput'     => '#639922',
            'konsentrat' => '#378ADD',
            'silase'     => '#BA7517',
            'dedak'      => '#888780',
            'ampas_tahu' => '#888780',
        ];

        $data = DB::table('pemberian_pakan as pp')
            ->join('pakan_stok as ps', 'pp.pakan_id', '=', 'ps.pakan_id')
            ->where('pp.ear_tag_id', $earTagId)
            ->where('pp.tanggal_pemberian', '>=', now()->subDays(7))
            ->select(
                'ps.jenis',
                DB::raw('ROUND(AVG(pp.jumlah_gram), 0) as total_gram')
            )
            ->groupBy('ps.jenis')
            ->get();

        $total = $data->sum('total_gram') ?: 1;

        return $data->map(function ($row) use ($total, $warnaMap) {
            $row->persen = round(($row->total_gram / $total) * 100, 1);
            $row->warna  = $warnaMap[$row->jenis] ?? '#aaaaaa';
            return $row;
        });
    }

    public function searchDomba(Request $request)
{
    $q = trim($request->get('q', ''));
    if (strlen($q) < 1) return response()->json([]);

    return response()->json(
        DB::table('domba as d')
            ->leftJoin(
                DB::raw('(SELECT DISTINCT ON (ear_tag_id) ear_tag_id, berat_kg FROM penimbangan ORDER BY ear_tag_id, tanggal_timbang DESC) as p'),
                'd.ear_tag_id', '=', 'p.ear_tag_id'
            )
            ->select('d.ear_tag_id', 'd.nama', 'd.kategori', 'd.ras', 'p.berat_kg')
            ->where('d.status', 'aktif')
            ->whereNull('d.deleted_at')
            ->where(function($query) use ($q) {
                $query->where('d.ear_tag_id', 'ilike', "%{$q}%")
                    ->orWhere('d.nama', 'ilike', "%{$q}%");
            })
            ->limit(8)
            ->get()
    );
}
    public function stats(string $earTagId)
{
    $fcrService = new FCRCalculatorService();

    $detail = $fcrService->detail(
        $earTagId,
        now()->subDays(30)->toDateString(),
        now()->toDateString()
    );

    $rekomendasi = DB::table('pemberian_pakan as pp')
        ->join('pakan_stok as ps', 'pp.pakan_id', '=', 'ps.pakan_id')
        ->where('pp.ear_tag_id', $earTagId)
        ->where('pp.tanggal_pemberian', '>=', now()->subDays(7))
        ->select('ps.jenis', DB::raw('ROUND(AVG(pp.jumlah_gram), 0) as total_gram'))
        ->groupBy('ps.jenis')
        ->get();

    $totalIdeal  = $rekomendasi->sum('total_gram') ?: 0;
    $rekomendasi = $rekomendasi->map(function($r) use ($totalIdeal) {
        $r->persen = $totalIdeal > 0 ? round(($r->total_gram / $totalIdeal) * 100, 1) : 0;
        return $r;
    });

    return response()->json([
        // FCR & status
        'fcr'                  => $detail['fcr'],
        'fcr_status'           => $detail['status'],
        'fcr_status_label'     => $detail['status_label'],
        'fcr_status_color'     => $detail['status_color'],

        // Pakan
        'total_pakan_30hr'     => $detail['total_pakan_kg'],
        'rata_pakan_harian_gr' => $detail['rata_pakan_harian_gr'],

        // Bobot
        'kenaikan_bb'          => $detail['delta_bobot_kg'],
        'berat_awal_kg'        => $detail['berat_awal_kg'],
        'berat_akhir_kg'       => $detail['berat_akhir_kg'],

        // Rekomendasi komposisi
        'rekomendasi'          => $rekomendasi,
        'total_ideal_gram'     => $totalIdeal,
    ]);
}
    

}