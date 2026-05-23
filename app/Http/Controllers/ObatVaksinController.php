<?php

namespace App\Http\Controllers;

use App\Models\ObatVaksin;
use Illuminate\Http\Request;

class ObatVaksinController extends Controller
{
    public function index(Request $request)
    {
        $query = ObatVaksin::query();

        if ($request->filled('search')) {
            $query->where('nama_obat', 'ilike', "%{$request->search}%");
        }
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $obatVaksin = $query->orderBy('created_at', 'desc')
                            ->paginate(10)
                            ->withQueryString();

        return view('obat-vaksin.index', compact('obatVaksin'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_obat'          => 'required|string|max:255',
            'tipe'               => 'required|in:obat,vaksin,vitamin',
            'satuan'             => 'required|in:ml,dosis,tablet',
            'stok'               => 'required|integer|min:0',
            'stok_minimum'       => 'required|integer|min:0',
            'harga_beli'         => 'nullable|numeric|min:0',
            'tanggal_expired'    => 'nullable|date',
            'interval_vaksinasi' => 'nullable|integer|min:1',
            'keterangan'         => 'nullable|string|max:1000',
        ]);

        $obat = ObatVaksin::create([
            'nama_obat'          => $request->nama_obat,
            'tipe'               => $request->tipe,
            'satuan'             => $request->satuan,
            'stok'               => $request->stok,
            'stok_minimum'       => $request->stok_minimum,
            'harga_beli'         => $request->harga_beli ?: null,
            'tanggal_expired'    => $request->tanggal_expired ?: null,
            'interval_vaksinasi' => $request->interval_vaksinasi ?: null,
            'keterangan'         => $request->keterangan ?: null,
        ]);

        return redirect()->route('obat-vaksin.index')
                        ->with('success', "{$obat->nama_obat} ({$obat->formatted_id}) berhasil ditambahkan.");
    }

    public function show(string $id)
    {
        $obat = ObatVaksin::findOrFail($id);

        return response()->json([
            'obat_id'            => $obat->obat_id,
            'formatted_id'       => $obat->formatted_id,
            'nama_obat'          => $obat->nama_obat,
            'tipe'               => $obat->tipe,
            'satuan'             => $obat->satuan,
            'stok'               => $obat->stok,
            'stok_minimum'       => $obat->stok_minimum,
            'harga_beli'         => $obat->harga_beli,
            'harga_beli_formatted' => $obat->harga_beli_formatted,
            'tanggal_expired'    => $obat->tanggal_expired?->format('Y-m-d'),
            'interval_vaksinasi' => $obat->interval_vaksinasi,
            'keterangan'         => $obat->keterangan,
            'status'             => $obat->status,
            'created_at'         => $obat->created_at?->format('d M Y, H:i'),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $obat = ObatVaksin::findOrFail($id);

        $request->validate([
            'nama_obat'          => 'required|string|max:255',
            'tipe'               => 'required|in:obat,vaksin,vitamin',
            'satuan'             => 'required|in:ml,dosis,tablet',
            'stok'               => 'required|integer|min:0',
            'stok_minimum'       => 'required|integer|min:0',
            'harga_beli'         => 'nullable|numeric|min:0',
            'tanggal_expired'    => 'nullable|date',
            'interval_vaksinasi' => 'nullable|integer|min:1',
            'keterangan'         => 'nullable|string|max:1000',
        ]);

        $obat->update([
            'nama_obat'          => $request->nama_obat,
            'tipe'               => $request->tipe,
            'satuan'             => $request->satuan,
            'stok'               => $request->stok,
            'stok_minimum'       => $request->stok_minimum,
            'harga_beli'         => $request->harga_beli ?: null,
            'tanggal_expired'    => $request->tanggal_expired ?: null,
            'interval_vaksinasi' => $request->interval_vaksinasi ?: null,
            'keterangan'         => $request->keterangan ?: null,
        ]);

        return redirect()->route('obat-vaksin.index')
                        ->with('success', "{$obat->nama_obat} ({$obat->formatted_id}) berhasil diperbarui.");
    }

    public function destroy(string $id)
    {
        $obat = ObatVaksin::findOrFail($id);
        $info = "{$obat->nama_obat} ({$obat->formatted_id})";
        $obat->delete();

        return redirect()->route('obat-vaksin.index')
                        ->with('success', "{$info} berhasil dihapus.");
    }

    public function create() {}
    public function edit(string $id) {}
}