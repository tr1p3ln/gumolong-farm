<?php

namespace App\Http\Controllers;

use App\Models\Domba;
use App\Models\Penimbangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingPertumbuhanController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->input('search');
        $kategori  = $request->input('kategori');
        $statusAdg = $request->input('status_adg');
        $tglTimbang = $request->input('tgl_timbang');
        $kandangId = $request->input('kandang_id');

        $latestPenimbangan = DB::table('penimbangan as p1')
            ->select('p1.ear_tag_id', 'p1.berat_kg', 'p1.adg', 'p1.tanggal_timbang')
            ->whereRaw('p1.tanggal_timbang = (
                SELECT MAX(p2.tanggal_timbang)
                FROM penimbangan p2
                WHERE p2.ear_tag_id = p1.ear_tag_id
            )');

        $query = Domba::leftJoinSub($latestPenimbangan, 'latest_p', function ($join) {
                $join->on('domba.ear_tag_id', '=', 'latest_p.ear_tag_id');
            })
            ->select(
                'domba.ear_tag_id',
                'domba.nama',
                'domba.kategori',
                'domba.ras',
                'latest_p.berat_kg',
                'latest_p.tanggal_timbang as tgl_timbang',
                'latest_p.adg',
                'domba.kandang_id'
            )
            ->whereNull('domba.deleted_at');

        if ($search) {
            // Use LIKE for cross-DB compatibility
            $query->where(function ($q) use ($search) {
                $q->where('domba.ear_tag_id', 'like', "%{$search}%")
                  ->orWhere('domba.nama', 'like', "%{$search}%");
            });
        }

        if ($kategori) {
            $query->where('domba.kategori', $kategori);
        }

        if ($tglTimbang) {
            $query->where('latest_p.tanggal_timbang', $tglTimbang);
        }

        if ($kandangId) {
            $query->where('domba.kandang_id', $kandangId);
        }

        if ($statusAdg) {
            match ($statusAdg) {
                'alert'  => $query->where('latest_p.adg', '<', 0),
                'rendah' => $query->whereBetween('latest_p.adg', [0, 0.2]),
                'normal' => $query->where('latest_p.adg', '>=', 0.2),
                default  => null,
            };
        }

        $dombaList = $query->paginate(10)->withQueryString();

        $stats = $this->getSummaryStats();
        $alerts = $this->getAlerts();
        $trendData = $this->getWeeklyTrend();
        $kandangList = DB::table('kandang')->select('kandang_id', 'nama_kandang')->get();

        return view('tracking-pertumbuhan.index', compact(
            'dombaList',
            'stats',
            'alerts',
            'trendData',
            'kandangList',
        ));
    }

        public function show(string $earTagId)
    {
        $domba = Domba::with('kandang')
            ->findOrFail($earTagId);

        $riwayat = Penimbangan::where('ear_tag_id', $earTagId)
            ->orderBy('tanggal_timbang')
            ->get();

        $latestPenimbangan = $riwayat->last();

        return view(
            'tracking-pertumbuhan.show',
            compact(
                'domba',
                'riwayat',
                'latestPenimbangan'
            )
        );
    }

    public function storePenimbangan(Request $request, string $earTagId)
    {
        $validated = $request->validate([
            'tanggal_timbang' => 'required|date',
            'berat_kg'        => 'required|numeric|min:0',
            'catatan'         => 'nullable|string|max:500',
        ]);

        $prevRecord = Penimbangan::where('ear_tag_id', $earTagId)
            ->where('tanggal_timbang', '<', $validated['tanggal_timbang'])
            ->orderByDesc('tanggal_timbang')
            ->first();

        $adg = null;
        if ($prevRecord) {
            $days = \Carbon\Carbon::parse($prevRecord->tanggal_timbang)
                ->diffInDays($validated['tanggal_timbang']);

            if ($days > 0) {
                $adg = round(($validated['berat_kg'] - $prevRecord->berat_kg) / $days, 3);
            }
        }

        Penimbangan::create([
            'ear_tag_id'      => $earTagId,
            'tanggal_timbang' => $validated['tanggal_timbang'],
            'berat_kg'        => $validated['berat_kg'],
            'adg'             => $adg,
            'catatan'         => $validated['catatan'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Data penimbangan berhasil disimpan.');
    }

    private function getSummaryStats(): array
    {
        $avgAdg = DB::table('penimbangan as p1')
            ->whereRaw('p1.tanggal_timbang = (
                SELECT MAX(p2.tanggal_timbang)
                FROM penimbangan p2
                WHERE p2.ear_tag_id = p1.ear_tag_id
            )')
            ->avg('p1.adg');

        $dombaRendah = DB::table('penimbangan as p1')
            ->whereRaw('p1.tanggal_timbang = (
                SELECT MAX(p2.tanggal_timbang)
                FROM penimbangan p2
                WHERE p2.ear_tag_id = p1.ear_tag_id
            )')
            ->where('p1.adg', '<', 0.2)
            ->count();

        $penimbanganBulanIni = Penimbangan::whereYear('tanggal_timbang', now()->year)
            ->whereMonth('tanggal_timbang', now()->month)
            ->count();

        $totalDomba = Domba::whereNull('deleted_at')->count();

        $targetPersen = $totalDomba > 0
            ? round((($totalDomba - $dombaRendah) / $totalDomba) * 100)
            : 0;

        return [
            'avg_adg'               => round($avgAdg ?? 0, 2),
            'avg_adg_trend'         => 4.2,
            'domba_rendah'          => $dombaRendah,
            'penimbangan_bulan_ini' => $penimbanganBulanIni,
            'target_persen'         => $targetPersen,
        ];
    }

    private function getAlerts(): \Illuminate\Support\Collection
    {
        return DB::table('penimbangan as p1')
            ->join('domba', 'domba.ear_tag_id', '=', 'p1.ear_tag_id')
            ->whereRaw('p1.tanggal_timbang = (
                SELECT MAX(p2.tanggal_timbang)
                FROM penimbangan p2
                WHERE p2.ear_tag_id = p1.ear_tag_id
            )')
            ->where('p1.adg', '<', 0.3)
            ->whereNull('domba.deleted_at')
            ->select(
                'domba.ear_tag_id',
                'domba.nama',
                'p1.adg',
                'p1.tanggal_timbang'
            )
            ->orderBy('p1.adg')
            ->limit(10)
            ->get();
    }

    private function getWeeklyTrend(): array
    {
        $weeks = [];
        for ($i = 3; $i >= 0; $i--) {
            $start = now()->subWeeks($i)->startOfWeek();
            $end   = now()->subWeeks($i)->endOfWeek();

            $avg = Penimbangan::whereBetween('tanggal_timbang', [$start, $end])
                ->avg('adg');

            $weeks[] = [
                'label' => 'Week ' . (4 - $i),
                'value' => round($avg ?? 0, 3),
            ];
        }

        $lastWeekAvg = $weeks[2]['value'] ?? 0;
        $thisWeekAvg = $weeks[3]['value'] ?? 0;
        $weeklyGrowth = $lastWeekAvg > 0
            ? round((($thisWeekAvg - $lastWeekAvg) / $lastWeekAvg) * 100, 1)
            : 0;

        return [
            'weeks'         => $weeks,
            'weekly_growth' => $weeklyGrowth,
        ];
    }
}
