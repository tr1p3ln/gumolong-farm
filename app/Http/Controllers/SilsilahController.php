<?php

namespace App\Http\Controllers;

use App\Models\Domba;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SilsilahController extends Controller
{
    public function index(Request $request)
    {
        $query = Domba::with(['kandang', 'induk', 'ayah'])
            ->whereNull('deleted_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ear_tag_id', 'ilike', "%{$search}%")
                  ->orWhere('nama', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        $dombas = $query->paginate(15)->withQueryString();

        return view('silsilah.index', compact('dombas'));
    }

    public function show(Request $request, $earTagId)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $domba    = Domba::with(['kandang', 'induk', 'ayah'])->findOrFail($earTagId);
            $pedigree = $this->buildPedigreeTree($earTagId, 4);
            $coi      = $this->hitungCOI($earTagId);

            return response()->json([
                'domba'             => $domba,
                'pedigree'          => $pedigree,
                'coi'               => $coi,
                'coi_persen'        => round($coi * 100, 2),
                'status_inbreeding' => $this->statusCOI($coi),
            ]);
        }

        return view('silsilah.show', ['earTagId' => $earTagId]);
    }

    public function cekInbreeding(Request $request)
    {
        $request->validate([
            'induk_id'    => 'required|string|exists:domba,ear_tag_id',
            'pejantan_id' => 'required|string|exists:domba,ear_tag_id',
        ]);

        $coi = $this->hitungCOIPasangan(
            $request->induk_id,
            $request->pejantan_id
        );

        return response()->json([
            'coi'         => $coi,
            'coi_persen'  => round($coi * 100, 2),
            'status'      => $this->statusCOI($coi),
            'rekomendasi' => $this->getRekomendasiTeks($coi),
            'aman'        => $coi < 0.0625,
        ]);
    }

    public function rekomendasiPejantan(Request $request)
    {
        $request->validate([
            'induk_id' => 'required|string|exists:domba,ear_tag_id',
        ]);

        $indukanId = $request->induk_id;

        $pejantanList = Domba::where('jenis_kelamin', 'jantan')
            ->where('status', 'aktif')
            ->where('kategori', 'pejantan')
            ->whereNull('deleted_at')
            ->with('kandang')
            ->get();

        $hasil = [];
        foreach ($pejantanList as $pejantan) {
            $coi     = $this->hitungCOIPasangan($indukanId, $pejantan->ear_tag_id);
            $hasil[] = [
                'ear_tag_id' => $pejantan->ear_tag_id,
                'nama'       => $pejantan->nama,
                'ras'        => $pejantan->ras,
                'kandang'    => $pejantan->kandang?->nama_kandang,
                'coi'        => $coi,
                'coi_persen' => round($coi * 100, 2),
                'status'     => $this->statusCOI($coi),
                'aman'       => $coi < 0.0625,
            ];
        }

        usort($hasil, fn($a, $b) => $a['coi'] <=> $b['coi']);

        return response()->json([
            'induk_id'    => $indukanId,
            'rekomendasi' => array_slice($hasil, 0, 10),
        ]);
    }

    private function buildPedigreeTree($earTagId, $maxDepth = 4, $depth = 0): ?array
    {
        if ($depth >= $maxDepth || !$earTagId) return null;

        $domba = Domba::with('kandang')->find($earTagId);
        if (!$domba) return null;

        return [
            'ear_tag_id'    => $domba->ear_tag_id,
            'nama'          => $domba->nama ?? '-',
            'jenis_kelamin' => $domba->jenis_kelamin,
            'ras'           => $domba->ras,
            'kategori'      => $domba->kategori,
            'status'        => $domba->status,
            'tanggal_lahir' => $domba->tanggal_lahir,
            'kandang'       => $domba->kandang?->nama_kandang,
            'induk'         => $this->buildPedigreeTree($domba->induk_id, $maxDepth, $depth + 1),
            'ayah'          => $this->buildPedigreeTree($domba->ayah_id, $maxDepth, $depth + 1),
        ];
    }

    private function getAncestors($earTagId): array
    {
        if (!$earTagId) return [];

        $results = DB::select("
            WITH RECURSIVE leluhur AS (
                SELECT ear_tag_id, nama, induk_id, ayah_id, 0 AS generasi
                FROM domba
                WHERE ear_tag_id = ? AND deleted_at IS NULL

                UNION ALL

                SELECT d.ear_tag_id, d.nama, d.induk_id, d.ayah_id, l.generasi + 1
                FROM domba d
                INNER JOIN leluhur l ON (d.ear_tag_id = l.induk_id OR d.ear_tag_id = l.ayah_id)
                WHERE d.deleted_at IS NULL AND l.generasi < 6
            )
            SELECT DISTINCT ear_tag_id, nama, generasi
            FROM leluhur
            WHERE generasi > 0
            ORDER BY generasi
        ", [$earTagId]);

        return collect($results)->map(fn($r) => [
            'ear_tag_id' => $r->ear_tag_id,
            'nama'       => $r->nama,
            'generasi'   => $r->generasi,
        ])->toArray();
    }

    private function hitungCOI($earTagId): float
    {
        $domba = Domba::find($earTagId);
        if (!$domba || !$domba->induk_id || !$domba->ayah_id) return 0.0;

        return $this->hitungCOIPasangan($domba->induk_id, $domba->ayah_id);
    }

    private function hitungCOIPasangan($indukanId, $pejantanId): float
    {
        if (!$indukanId || !$pejantanId) return 0.0;

        $ancestorInduk    = $this->getAncestors($indukanId);
        $ancestorPejantan = $this->getAncestors($pejantanId);

        $idInduk    = collect($ancestorInduk)->pluck('ear_tag_id')->toArray();
        $idPejantan = collect($ancestorPejantan)->pluck('ear_tag_id')->toArray();
        $commonIds  = array_intersect($idInduk, $idPejantan);

        if (empty($commonIds)) return 0.0;

        $coi = 0.0;
        foreach ($commonIds as $ancestorId) {
            $genInduk    = collect($ancestorInduk)->firstWhere('ear_tag_id', $ancestorId);
            $genPejantan = collect($ancestorPejantan)->firstWhere('ear_tag_id', $ancestorId);

            if ($genInduk && $genPejantan) {
                $n1   = $genInduk['generasi'];
                $n2   = $genPejantan['generasi'];
                $coi += pow(0.5, $n1 + $n2 + 1);
            }
        }

        return round($coi, 6);
    }

    private function statusCOI(float $coi): string
    {
        if ($coi === 0.0)  return 'Tidak Ada Inbreeding';
        if ($coi < 0.0625) return 'Rendah (Aman)';
        if ($coi < 0.125)  return 'Sedang (Perhatian)';
        if ($coi < 0.25)   return 'Tinggi (Berisiko)';
        return 'Sangat Tinggi (Hindari)';
    }

    private function getRekomendasiTeks(float $coi): string
    {
        if ($coi === 0.0)   return 'Tidak ada hubungan kekerabatan. Perkawinan aman dilakukan.';
        if ($coi < 0.0625)  return 'Tingkat inbreeding rendah. Perkawinan masih dapat dilakukan dengan pengawasan.';
        if ($coi < 0.125)   return 'Tingkat inbreeding sedang. Pertimbangkan pejantan lain yang tidak memiliki hubungan kekerabatan.';
        return 'Tingkat inbreeding tinggi. Sangat tidak disarankan. Pilih pejantan lain yang tidak berkerabat.';
    }
}
