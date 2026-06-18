<?php

namespace App\Http\Controllers;

use App\Models\Domba;
use App\Models\Kandang;
use App\Models\Penimbangan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class DombaController extends Controller
{
    public function index(Request $request)
    {
        $query = Domba::with([
            'kandang',
            'penimbangan' => function ($q) {
                $q->orderBy('tanggal_timbang', 'desc')->limit(1);
            },
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ear_tag_id', 'ilike', "%{$search}%")
                  ->orWhere('nama', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $summary = [
            'total'     => Domba::count(),
            'aktif'     => Domba::where('status', 'aktif')->count(),
            'karantina' => Domba::where('status', 'karantina')->count(),
            'terjual'   => Domba::where('status', 'terjual')->count(),
            'mati'      => Domba::where('status', 'mati')->count(),
        ];

        $dombas   = $query->paginate(10)->withQueryString();
        $kandangs = Kandang::all();

        return view('domba.index', compact('dombas', 'summary', 'kandangs'));
    }

    public function generateEarTag(Request $request): JsonResponse
    {
        $jenisKelamin = $request->input('jenis_kelamin', 'jantan');

        if (!in_array($jenisKelamin, ['jantan', 'betina'], true)) {
            return response()->json(['error' => 'jenis_kelamin tidak valid'], 422);
        }

        $earTag = Domba::generateEarTag($jenisKelamin);

        return response()->json([
            'ear_tag'       => $earTag,
            'jenis_kelamin' => $jenisKelamin,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama'                 => ['nullable', 'string', 'max:255'],
            'jenis_kelamin'        => ['required', 'in:jantan,betina'],
            'ras'                  => ['required', 'string', 'max:255'],
            'tanggal_lahir'        => ['nullable', 'date'],
            'kategori'             => ['required', 'in:cempe,dara,indukan,pejantan'],
            'status'               => ['nullable', 'in:aktif,terjual,mati,karantina'],
            'asal'                 => ['nullable', 'in:lahir_di_kandang,dari_luar'],
            'catatan'              => ['nullable', 'string'],
            'kandang_id'           => ['required', 'exists:kandang,kandang_id'],
            'induk_id'             => ['nullable', 'exists:domba,ear_tag_id'],
            'ayah_id'              => ['nullable', 'exists:domba,ear_tag_id'],
            'berat_awal'           => ['nullable', 'numeric', 'min:0'],
            'tanggal_timbang_awal' => ['nullable', 'date', 'required_with:berat_awal'],
        ]);

        try {
            $earTagId = DB::transaction(function () use ($validated) {
                $earTagId = Domba::generateEarTag($validated['jenis_kelamin']);

                Domba::create([
                    'ear_tag_id'    => $earTagId,
                    'nama'          => $validated['nama']          ?? null,
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'ras'           => $validated['ras'],
                    'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                    'kategori'      => $validated['kategori'],
                    'status'        => $validated['status']        ?? 'aktif',
                    'asal'          => $validated['asal']          ?? 'dari_luar',
                    'catatan'       => $validated['catatan']       ?? null,
                    'kandang_id'    => $validated['kandang_id'],
                    'induk_id'      => $validated['induk_id']      ?? null,
                    'ayah_id'       => $validated['ayah_id']       ?? null,
                ]);

                if (!empty($validated['berat_awal']) && !empty($validated['tanggal_timbang_awal'])) {
                    Penimbangan::create([
                        'ear_tag_id'      => $earTagId,
                        'tanggal_timbang' => $validated['tanggal_timbang_awal'],
                        'berat_kg'        => $validated['berat_awal'],
                    ]);
                }

                return $earTagId;
            });
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan domba: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success'    => true,
            'ear_tag_id' => $earTagId,
            'message'    => "Domba {$earTagId} berhasil ditambahkan.",
        ], 201);
    }

    /**
     * Helper Method: Mengambil SEMUA relasi domba (Pertumbuhan, Medis, Vaksin, Pakan)
     * Digunakan oleh show() untuk Modal dan exportPdf() untuk Cetak PDF
     */
    private function getDombaCompleteData(string $earTagId)
    {
        // 1. Fetch data domba dan relasi dasar
        $domba = Domba::with([
            'kandang',
            'induk',
            'ayah',
            'penimbangan' => function ($q) {
                $q->orderByDesc('tanggal_timbang');
            }
        ])->findOrFail($earTagId);

        // 2. Fetch Rekam Medis
        $medicalRecords = DB::table('medical_record')
            ->where('ear_tag_id', $earTagId)
            ->orderByDesc('tanggal_sakit')
            ->get();

        $rekamIds = $medicalRecords->pluck('rekam_id')->toArray();

        // 3. Fetch Pemakaian Obat
        $pemakaianObat = [];
        if (!empty($rekamIds)) {
            $pemakaianObat = DB::table('pemakaian_obat as po')
                ->join('obat_vaksin as ov', 'po.obat_id', '=', 'ov.obat_id')
                ->whereIn('po.rekam_id', $rekamIds)
                ->select('po.*', 'ov.nama_obat', 'ov.satuan')
                ->get()
                ->groupBy('rekam_id');
        }

        $medicalRecords->transform(function ($item) use ($pemakaianObat) {
            $item->pemakaian_obat = isset($pemakaianObat[$item->rekam_id])
                ? $pemakaianObat[$item->rekam_id]
                : [];
            return $item;
        });

        $domba->medical_record = $medicalRecords;

        // 4. Fetch Vaksinasi
        $vaksinasi = DB::table('vaksinasi as v')
            ->join('obat_vaksin as ov', 'v.obat_id', '=', 'ov.obat_id')
            ->where('v.ear_tag_id', $earTagId)
            ->orderByDesc('v.tanggal_vaksinasi')
            ->select('v.*', 'ov.nama_obat', 'ov.satuan')
            ->get();

        $domba->vaksinasi = $vaksinasi;

        // 5. Fetch Pemberian Pakan (Log & Total 30 Hari)
        $pemberianPakan = DB::table('pemberian_pakan as pp')
            ->join('pakan_stok as ps', 'pp.pakan_id', '=', 'ps.pakan_id')
            ->where('pp.ear_tag_id', $earTagId)
            ->select('pp.*', 'ps.nama_pakan', 'ps.jenis')
            ->orderByDesc('pp.tanggal_pemberian')
            ->orderByDesc('pp.created_at')
            ->limit(30)
            ->get();

        $domba->pemberian_pakan = $pemberianPakan;

        $totalPakan30Hari = DB::table('pemberian_pakan')
            ->where('ear_tag_id', $earTagId)
            ->where('tanggal_pemberian', '>=', now()->subDays(30)->toDateString())
            ->sum('jumlah_gram');

        // Convert gram ke KG
        $domba->total_pakan_30_hari = $totalPakan30Hari / 1000;

        return $domba;
    }

    public function suggest(Request $request): JsonResponse
    {
        $q  = $request->input('q', '');
        $jk = $request->input('jenis_kelamin');

        $query = Domba::with('kandang')
            ->where('status', 'aktif')
            ->where(function ($sub) use ($q) {
                $sub->where('ear_tag_id', 'ilike', "%{$q}%")
                    ->orWhere('nama', 'ilike', "%{$q}%");
            });

        if ($jk && in_array($jk, ['jantan', 'betina'], true)) {
            $query->where('jenis_kelamin', $jk);
        }

        $results = $query->limit(8)->get()->map(fn($d) => [
            'ear_tag_id'  => $d->ear_tag_id,
            'nama'        => $d->nama,
            'ras'         => $d->ras,
            'kategori'    => $d->kategori,
            'kandang'     => $d->kandang?->nama_kandang ?? '—',
        ]);

        return response()->json($results);
    }

    public function show(string $earTagId): JsonResponse
    {
        // Gunakan fungsi helper, lalu return menjadi format JSON untuk Alpine Modal
        $domba = $this->getDombaCompleteData($earTagId);

        return response()->json([
            'success' => true,
            'data'    => $domba,
        ]);
    }

    public function exportPdf(string $earTagId)
    {
        // Gunakan fungsi helper, lalu pass data ke tampilan PDF Profil
        $domba = $this->getDombaCompleteData($earTagId);
        $theme = config('pdf_theme');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('domba.pdf-profil', [
            'domba' => $domba,
            'theme' => $theme
        ])
        ->setPaper($theme['paper_size'], $theme['paper_orientation'])
        ->setOptions([
            'defaultFont'          => $theme['font_family'],
            'isRemoteEnabled'      => false,
            'isHtml5ParserEnabled' => true,
        ]);

        $filename = 'Profil-Domba-' . $domba->ear_tag_id . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->stream($filename);
    }

    public function update(Request $request, string $earTagId): JsonResponse
    {
        $domba = Domba::findOrFail($earTagId);

        $validated = $request->validate([
            'nama'          => ['nullable', 'string', 'max:255'],
            'ras'           => ['sometimes', 'required', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'kategori'      => ['sometimes', 'required', 'in:cempe,dara,indukan,pejantan'],
            'status'        => ['sometimes', 'required', 'in:aktif,terjual,mati,karantina'],
            'asal'          => ['sometimes', 'required', 'in:lahir_di_kandang,dari_luar'],
            'catatan'       => ['nullable', 'string'],
            'kandang_id'    => ['sometimes', 'required', 'exists:kandang,kandang_id'],
            'induk_id'      => ['nullable', 'exists:domba,ear_tag_id'],
            'ayah_id'       => ['nullable', 'exists:domba,ear_tag_id'],
        ]);

        $domba->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Domba {$earTagId} berhasil diperbarui.",
        ]);
    }

    public function destroy(string $earTagId): JsonResponse
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Super Admin yang dapat menghapus data domba.',
            ], 403);
        }

        $domba = Domba::findOrFail($earTagId);
        $domba->delete();

        return response()->json([
            'success' => true,
            'message' => "Domba {$earTagId} berhasil dihapus dari daftar aktif.",
        ]);
    }
}
