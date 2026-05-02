<?php

namespace App\Http\Controllers;

use App\Models\Kandang;
use Illuminate\Http\Request;

class KandangController extends Controller
{
    public function index()
    {
        $kandangs = Kandang::withCount([
            'domba as domba_aktif' => fn($q) => $q->where('status', 'aktif'),
            'domba as domba_total',
        ])->orderBy('tipe')->orderBy('nama_kandang')->get();

        $stats = [
            'total'     => $kandangs->count(),
            'kapasitas' => $kandangs->sum('kapasitas'),
            'terisi'    => $kandangs->sum('domba_aktif'),
            'tersedia'  => max(0, $kandangs->sum('kapasitas') - $kandangs->sum('domba_aktif')),
            'persen'    => $kandangs->sum('kapasitas') > 0
                ? round($kandangs->sum('domba_aktif') / $kandangs->sum('kapasitas') * 100)
                : 0,
        ];

        return view('kandang.index', compact('kandangs', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kandang' => 'required|string|max:100|unique:kandang,nama_kandang',
            'tipe'         => 'required|in:utama,isolasi,kawin,persalinan',
            'kapasitas'    => 'required|integer|min:1|max:9999',
        ], [
            'nama_kandang.unique' => 'Nama kandang sudah digunakan.',
            'kapasitas.min'       => 'Kapasitas minimal 1 ekor.',
            'kapasitas.max'       => 'Kapasitas maksimal 9999 ekor.',
        ]);

        $kandang = Kandang::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Kandang \"{$kandang->nama_kandang}\" berhasil ditambahkan.",
            'data'    => $kandang,
        ], 201);
    }

    public function show($id)
    {
        $kandang = Kandang::withCount([
            'domba as domba_aktif' => fn($q) => $q->where('status', 'aktif'),
            'domba as domba_total',
        ])->findOrFail($id);

        return response()->json($kandang);
    }

    public function update(Request $request, $id)
    {
        $kandang = Kandang::findOrFail($id);

        $validated = $request->validate([
            'nama_kandang' => "required|string|max:100|unique:kandang,nama_kandang,{$id},kandang_id",
            'tipe'         => 'required|in:utama,isolasi,kawin,persalinan',
            'kapasitas'    => 'required|integer|min:1|max:9999',
        ], [
            'nama_kandang.unique' => 'Nama kandang sudah digunakan.',
            'kapasitas.min'       => 'Kapasitas minimal 1 ekor.',
        ]);

        // Kapasitas baru tidak boleh di bawah jumlah domba aktif saat ini
        $aktif = $kandang->domba()->where('status', 'aktif')->count();
        if ($validated['kapasitas'] < $aktif) {
            return response()->json([
                'success' => false,
                'message' => "Kapasitas tidak boleh kurang dari jumlah domba aktif saat ini ({$aktif} ekor).",
            ], 422);
        }

        $kandang->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Kandang \"{$kandang->nama_kandang}\" berhasil diperbarui.",
            'data'    => $kandang,
        ]);
    }

    public function destroy($id)
    {
        $kandang = Kandang::findOrFail($id);

        $aktif = $kandang->domba()->where('status', 'aktif')->count();
        if ($aktif > 0) {
            return response()->json([
                'success' => false,
                'message' => "Kandang tidak dapat dihapus. Masih ada {$aktif} domba aktif. Pindahkan domba terlebih dahulu.",
            ], 422);
        }

        $nama = $kandang->nama_kandang;
        $kandang->delete();

        return response()->json([
            'success' => true,
            'message' => "Kandang \"{$nama}\" berhasil dihapus.",
        ]);
    }
}
