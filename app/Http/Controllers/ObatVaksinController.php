<?php

namespace App\Http\Controllers;

use App\Models\ObatVaksin;
use App\Models\MedicalRecord;
use App\Models\PemakaianObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\NotifikasiService;

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
    $riwayatPemakaian = DB::table('pemakaian_obat as p')
    ->join('obat_vaksin as o', 'p.obat_id', '=', 'o.obat_id')
    ->join('medical_record as m', 'p.rekam_id', '=', 'm.rekam_id')
    ->select(
        'p.pakai_id',   
        'p.tanggal_pakai',
        'p.jumlah',
        'p.cara_pemberian',
        'p.catatan',
        'o.nama_obat',
        'o.satuan',
        'o.tipe',
        'm.ear_tag_id',
        'm.rekam_id'
    )
    ->orderByDesc('p.tanggal_pakai')
    ->orderByDesc('p.created_at')
    ->paginate(10, ['*'], 'riwayat_page')
    ->withQueryString();

    // ── Jadwal Vaksinasi ──────────────────────────────────────────────
$jadwalVaksinasi = DB::table('pemakaian_obat as p')
    ->join('obat_vaksin as o', 'p.obat_id', '=', 'o.obat_id')
    ->join('medical_record as m', 'p.rekam_id', '=', 'm.rekam_id')
    ->whereNotNull('o.interval_vaksinasi')
    ->select(
        'p.pakai_id',
        'p.tanggal_pakai',
        'p.rekam_id',
        'o.nama_obat',
        'o.interval_vaksinasi',
        'o.satuan',
        'm.ear_tag_id'
    )
    ->orderByDesc('p.tanggal_pakai')
    // Ambil pemakaian terakhir per kombinasi domba+obat
    ->get()
    ->groupBy(fn($r) => $r->ear_tag_id . '_' . $r->nama_obat)
    ->map(fn($group) => $group->first()) // ambil yang terbaru
    ->map(function ($row) {
        $tanggalBerikutnya = \Carbon\Carbon::parse($row->tanggal_pakai)
            ->addMonths($row->interval_vaksinasi);
        $hariLagi = now()->startOfDay()->diffInDays($tanggalBerikutnya->startOfDay(), false);

        $row->tanggal_berikutnya = $tanggalBerikutnya->format('d M Y');
        $row->hari_lagi          = (int) $hariLagi;
        $row->status_jadwal      = match(true) {
            $hariLagi < 0    => 'jatuh_tempo',
            $hariLagi <= 7   => 'mendekati',
            $hariLagi <= 30  => 'segera',
            default          => 'aman',
        };
        return $row;
    })
    ->sortBy('hari_lagi')
    ->values();

    return view('obat-vaksin.index', compact('obatVaksin', 'riwayatPemakaian', 'jadwalVaksinasi'));

    return view('obat-vaksin.index', compact('obatVaksin', 'riwayatPemakaian'));
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
        // ── Cek expired ──
        $this->cekDanKirimNotifExpired($obat);

        return redirect()->route('obat-vaksin.index')
                        ->with('success', "{$obat->nama_obat} ({$obat->formatted_id}) berhasil ditambahkan.");
    }

    public function show(string $id)
    {
        $obat = ObatVaksin::findOrFail($id);

        return response()->json([
            'obat_id'              => $obat->obat_id,
            'formatted_id'         => $obat->formatted_id,
            'nama_obat'            => $obat->nama_obat,
            'tipe'                 => $obat->tipe,
            'satuan'               => $obat->satuan,
            'stok'                 => $obat->stok,
            'stok_minimum'         => $obat->stok_minimum,
            'harga_beli'           => $obat->harga_beli,
            'harga_beli_formatted' => $obat->harga_beli_formatted,
            'tanggal_expired'      => $obat->tanggal_expired?->format('Y-m-d'),
            'interval_vaksinasi'   => $obat->interval_vaksinasi,
            'keterangan'           => $obat->keterangan,
            'status'               => $obat->status,
            'created_at'           => $obat->created_at?->format('d M Y, H:i'),
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
        // ── Cek expired ──
        $this->cekDanKirimNotifExpired($obat);
        return redirect()->route('obat-vaksin.index')
                        ->with('success', "{$obat->nama_obat} ({$obat->formatted_id}) berhasil diperbarui.");
    }

    public function destroy(string $id)
    {
        $obat = ObatVaksin::findOrFail($id);
        $info = "{$obat->nama_obat} ({$obat->formatted_id})";

        DB::table('vaksinasi')->where('obat_id', $id)->delete();
        DB::table('pemakaian_obat')->where('obat_id', $id)->delete();

        $obat->delete();

        return redirect()->route('obat-vaksin.index')
                        ->with('success', "{$info} berhasil dihapus.");
    }

    // ── Search rekam medis (AJAX) ─────────────────────────────────
    // GET /obat-vaksin/search-rekam?q=...
    public function searchRekam(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        // Perbaikan: Query yang benar sesuai struktur database
        $results = DB::table('medical_record')
            ->select(
                'rekam_id',
                'ear_tag_id',
                'tanggal_sakit',
                'gejala',
                'diagnosa',
                'status',
                'created_at'
            )
            ->where(function ($query) use ($q) {
                // Cari by rekam_id (angka) atau ear_tag_id
                if (is_numeric($q)) {
                    $query->where('rekam_id', '=', (int)$q);
                } else {
                    $query->where('ear_tag_id', 'ilike', "%{$q}%");
                }
            })
            ->orderByDesc('tanggal_sakit')
            ->limit(8)
            ->get()
            ->map(function ($row) {
                // Format tanggal menjadi format yang lebih readable
                $row->tanggal_sakit = \Carbon\Carbon::parse($row->tanggal_sakit)->format('d M Y');
                return $row;
            });

        return response()->json($results);
    }

    
    // ── Store pemakaian obat dari modal Catat Pakai ───────────────
    // POST /obat-vaksin/pemakaian
    public function storePemakaian(Request $request)
    {
        $request->validate([
            'obat_id'           => 'required|exists:obat_vaksin,obat_id',
            'rekam_id'          => 'required|exists:medical_record,rekam_id',
            'tanggal_pemberian' => 'required|date',
            'jumlah_dosis'      => 'required|numeric|min:0.1',
            'cara_pemberian'    => 'nullable|string|max:255',
            'catatan'           => 'nullable|string|max:1000',
        ]);

        $obat = ObatVaksin::findOrFail($request->obat_id);
        $rekam = DB::table('medical_record')->where('rekam_id', $request->rekam_id)->first();

        if (!$rekam) {
            return redirect()->back()
                ->withErrors(['rekam_id' => 'Rekam medis tidak ditemukan.'])
                ->withInput();
        }

        if ($obat->stok < $request->jumlah_dosis) {
            return redirect()->back()
                ->withErrors(['jumlah_dosis' => "Stok tidak cukup. Stok tersedia: {$obat->stok} {$obat->satuan}."])
                ->withInput();
        }

        DB::transaction(function () use ($request, $obat, $rekam) {
        DB::table('pemakaian_obat')->insert([
            'rekam_id'       => $request->rekam_id,
            'obat_id'        => $request->obat_id,
            'jumlah'         => $request->jumlah_dosis,
            'cara_pemberian' => $request->cara_pemberian,
            'catatan'        => $request->catatan,
            'tanggal_pakai'  => $request->tanggal_pemberian,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // Kurangi stok
        $obat->decrement('stok', $request->jumlah_dosis);

        // ── Notifikasi pemberian obat/vaksin ──────────────────────
        $notifService = new NotifikasiService();
        $tipe  = $obat->tipe === 'vaksin' ? 'vaksin' : 'vaksin';
        $pesan = ucfirst($obat->tipe) . " {$obat->nama_obat} diberikan kepada domba {$rekam->ear_tag_id} "
            . "sebanyak {$request->jumlah_dosis} {$obat->satuan}.";
        $notifService->send(auth()->id(), $tipe, $pesan, $rekam->ear_tag_id);

        // ── Notifikasi stok tipis jika stok setelah pengurangan <= minimum ──
        $stokBaru = $obat->stok - $request->jumlah_dosis;
        if ($stokBaru <= $obat->stok_minimum) {
            $notifService->stokTipis(
                $obat->nama_obat,
                $stokBaru,
                $obat->satuan,
                $obat->stok_minimum
            );
        }
    });

        return redirect()->route('obat-vaksin.index')
            ->with('success', "Pemakaian {$obat->nama_obat} ({$obat->formatted_id}) berhasil dicatat. Stok berkurang {$request->jumlah_dosis} {$obat->satuan}.");
            
    }
    private function cekDanKirimNotifExpired(ObatVaksin $obat): void
    {
        if (!$obat->tanggal_expired) return;

        $hariLagi = (int) now()->diffInDays($obat->tanggal_expired, false);

        if ($hariLagi <= 0) {
            $pesan = "{$obat->nama_obat} sudah kedaluwarsa sejak "
                . \Carbon\Carbon::parse($obat->tanggal_expired)->format('d M Y')
                . ". Segera keluarkan dari stok.";
            (new NotifikasiService())->broadcast('expired', $pesan);

        } elseif ($hariLagi <= 30) {
            (new NotifikasiService())->expired($obat->nama_obat, $hariLagi);
        }
    }

    public function create() {}
    public function edit(string $id) {}
}