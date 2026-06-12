<?php

namespace App\Http\Controllers;

use App\Models\PakanStok;
use App\Models\PemberianPakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\NotifikasiService;

class StokPakanController extends Controller
{
    public function index(Request $request)
    {
        // --- Stok cards ---
        $stokList = PakanStok::orderBy('jenis')->get();

        // --- Prediksi restock (berdasarkan rata-rata pemberian 30 hari) ---
        $prediksi = $this->hitungPrediksiRestock($stokList);

        // --- Filter log pemberian pakan ---
        $query = PemberianPakan::with(['pakanStok', 'domba', 'user'])
            ->orderByDesc('tanggal_pemberian');

        if ($request->filled('dari')) {
            $query->whereDate('tanggal_pemberian', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal_pemberian', '<=', $request->sampai);
        }
        if ($request->filled('jenis_pakan') && $request->jenis_pakan !== 'semua') {
            $query->whereHas('pakanStok', fn($q) => $q->where('jenis', $request->jenis_pakan));
        }

        $logPemberian = $query->paginate(10)->withQueryString();

        // --- Alert peringatan / kritis ---
        $peringatan = $stokList->filter(fn($s) => $s->status_stok === 'menipis');
        $kritis     = $stokList->filter(fn($s) => $s->status_stok === 'habis');

        // --- Jenis pakan untuk dropdown filter ---
        $jenisOptions = PakanStok::select('jenis')->distinct()->pluck('jenis');

        $dombaList = \App\Models\Domba::orderBy('ear_tag_id')->get();

        return view('stok-pakan.index', compact(
            'stokList', 'prediksi', 'logPemberian', 'peringatan', 'kritis', 'jenisOptions', 'dombaList'
        ));
    }

    // Tambah stok manual (penerimaan dari supplier dll), tidak lewat pemberian_pakan
    public function catatMasuk(Request $request)
    {
        $request->validate([
            'pakan_id'   => 'required|exists:pakan_stok,pakan_id',
            'jumlah'     => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $pakan = PakanStok::lockForUpdate()->findOrFail($request->pakan_id);
            $pakan->jumlah_stok    += $request->jumlah;
            $pakan->tanggal_update  = now();
            $pakan->save();
        });

        return redirect()->route('stok-pakan.index')
            ->with('success', 'Stok pakan berhasil ditambahkan.');
    }

    // Catat pemberian pakan ke domba — mengurangi stok & menyimpan ke pemberian_pakan
    public function catatKeluar(Request $request)
    {
        $request->validate([
            'pakan_id'          => 'required|exists:pakan_stok,pakan_id',
            'ear_tag_id'        => 'required|exists:domba,ear_tag_id',
            'jumlah'            => 'required|numeric|min:0.01',
            'keterangan'        => 'nullable|string|max:255',
            'tanggal_pemberian' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request) {
            $pakan = PakanStok::lockForUpdate()->findOrFail($request->pakan_id);

            if ($request->jumlah > $pakan->jumlah_stok) {
                throw new \Exception('Jumlah pemberian melebihi stok yang tersedia ('
                    . $pakan->jumlah_stok . ' ' . $pakan->satuan . ').');
            }

            $pakan->jumlah_stok   -= $request->jumlah;
            $pakan->tanggal_update = now();
            $pakan->save();
            // ── Notifikasi stok menipis ───────────────────────────────
            if ($pakan->jumlah_stok <= ($pakan->stok_minimum ?? 0)) {
                (new \App\Services\NotifikasiService())->stokTipis(
                    $pakan->nama_pakan,
                    $pakan->jumlah_stok,
                    $pakan->satuan,
                    $pakan->stok_minimum ?? 0
                );
            }


            PemberianPakan::create([
                'pakan_id'          => $pakan->pakan_id,
                'ear_tag_id'        => $request->ear_tag_id,
                'user_id'           => Auth::id(),
                'sesi'              => 'pagi',
                'jumlah_gram'       => $request->jumlah * 1000,
                'satuan'            => $pakan->satuan,
                'sisa_stok'         => $pakan->jumlah_stok,
                'keterangan'        => $request->keterangan,
                'tanggal_pemberian' => $request->tanggal_pemberian ?? now(),
            ]);
        });

        return redirect()->route('stok-pakan.index')
            ->with('success', 'Pemberian pakan berhasil dicatat.');
    }

    // ------------------------------------------------------- PRIVATE HELPERS
    private function hitungPrediksiRestock($stokList): array
    {
        $hasil = [];

        foreach ($stokList as $pakan) {
            // Total pakan yang diberikan (keluar) dalam 30 hari terakhir
            $totalKeluarKg = PemberianPakan::where('pakan_id', $pakan->pakan_id)
                ->where('tanggal_pemberian', '>=', now()->subDays(30))
                ->sum('jumlah_gram') / 1000;

            $avgPerHari = round($totalKeluarKg / 30, 1);

            $sisaHari = ($avgPerHari > 0)
                ? (int) floor($pakan->jumlah_stok / $avgPerHari)
                : null;

            $tglRestock = ($sisaHari !== null)
                ? now()->addDays($sisaHari)->format('d M Y')
                : null;

            $hasil[] = [
                'pakan_id'     => $pakan->pakan_id,
                'nama_pakan'   => $pakan->nama_pakan,
                'avg_per_hari' => $avgPerHari,
                'sisa_hari'    => $sisaHari,
                'tgl_restock'  => $tglRestock,
                'status_stok'  => $pakan->status_stok,
                'satuan'       => $pakan->satuan,
            ];
        }

        return $hasil;
    }
}