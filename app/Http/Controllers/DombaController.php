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

    public function show(string $earTagId): JsonResponse
    {
        $domba = Domba::with([
            'kandang',
            'induk',
            'ayah',
            'penimbangan' => function ($q) {
                $q->orderByDesc('tanggal_timbang')->limit(5);
            },
        ])->findOrFail($earTagId);

        return response()->json([
            'success' => true,
            'data'    => $domba,
        ]);
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

        // ear_tag_id & jenis_kelamin di-lock — tidak akan ikut ter-update
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
        $domba->delete(); // SoftDeletes — set deleted_at, data tetap tersimpan

        return response()->json([
            'success' => true,
            'message' => "Domba {$earTagId} berhasil dihapus dari daftar aktif.",
        ]);
    }
}
