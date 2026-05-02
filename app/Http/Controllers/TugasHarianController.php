<?php

namespace App\Http\Controllers;

use App\Models\TugasHarian;
use App\Models\TemplateTugasRutin;
use App\Models\Kandang;
use App\Models\User;
use Illuminate\Http\Request;

class TugasHarianController extends Controller
{
    public function index(Request $request)
    {
        $tanggal        = $request->get('tanggal', today()->toDateString());
        $kandangId      = $request->get('kandang_id');
        $status         = $request->get('status');
        $selectedUserId = $request->get('user_id');
        $selectedTipe   = $request->get('tipe');

        $query = TugasHarian::with(['kandang', 'petugas'])
            ->whereDate('tanggal', $tanggal);

        if ($kandangId)      $query->where('kandang_id', $kandangId);
        if ($status)         $query->where('status', $status);
        if ($selectedUserId) $query->where('user_id', $selectedUserId);
        if ($selectedTipe)   $query->where('tipe', $selectedTipe);

        $tugas = $query->orderBy('prioritas', 'desc')
                       ->orderBy('created_at', 'asc')
                       ->get();

        $kandangs = Kandang::all();

        $summaryPerKandang = [];
        foreach ($kandangs as $kandang) {
            $tugasKandang = TugasHarian::whereDate('tanggal', $tanggal)
                ->where('kandang_id', $kandang->kandang_id)
                ->get();

            $summaryPerKandang[$kandang->kandang_id] = [
                'kandang'        => $kandang,
                'total'          => $tugasKandang->count(),
                'selesai'        => $tugasKandang->where('status', 'selesai')->count(),
                'dalam_proses'   => $tugasKandang->where('status', 'dalam_proses')->count(),
                'belum'          => $tugasKandang->where('status', 'belum')->count(),
                'dilewati'       => $tugasKandang->where('status', 'dilewati')->count(),
                'persen_selesai' => $tugasKandang->count() > 0
                    ? round($tugasKandang->where('status', 'selesai')->count() / $tugasKandang->count() * 100)
                    : 0,
            ];
        }

        $semua = TugasHarian::whereDate('tanggal', $tanggal)->get();
        $globalSummary = [
            'total'          => $semua->count(),
            'selesai'        => $semua->where('status', 'selesai')->count(),
            'dalam_proses'   => $semua->where('status', 'dalam_proses')->count(),
            'belum'          => $semua->where('status', 'belum')->count(),
            'dilewati'       => $semua->where('status', 'dilewati')->count(),
            'persen_selesai' => $semua->count() > 0
                ? round($semua->where('status', 'selesai')->count() / $semua->count() * 100)
                : 0,
        ];

        $petugasList = User::whereIn('role', ['kepala_kandang', 'pengurus_kandang'])->get();

        return view('tugas-harian.index', compact(
            'tugas', 'kandangs', 'summaryPerKandang', 'globalSummary',
            'tanggal', 'petugasList', 'selectedUserId', 'selectedTipe'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'       => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'kandang_id'  => 'required|exists:kandang,kandang_id',
            'user_id'     => 'nullable|exists:user,user_id',
            'tanggal'     => 'required|date',
            'tipe'        => 'required|in:rutin,kondisional',
            'prioritas'   => 'required|in:rendah,sedang,tinggi',
            'waktu_mulai' => 'nullable|date_format:H:i',
        ]);

        $validated['status']      = 'belum';
        $validated['assigned_by'] = auth()->id();

        $tugas = TugasHarian::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil ditambahkan',
            'data'    => $tugas->load(['kandang', 'petugas']),
        ]);
    }

    public function show($id)
    {
        $tugas = TugasHarian::with(['kandang', 'petugas'])->findOrFail($id);
        return response()->json($tugas);
    }

    public function update(Request $request, $id)
    {
        $tugas = TugasHarian::findOrFail($id);

        $validated = $request->validate([
            'judul'      => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
            'kandang_id' => 'required|exists:kandang,kandang_id',
            'user_id'    => 'nullable|exists:user,user_id',
            'tanggal'    => 'required|date',
            'tipe'       => 'required|in:rutin,kondisional',
            'prioritas'  => 'required|in:rendah,sedang,tinggi',
        ]);

        $tugas->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil diperbarui',
            'data'    => $tugas->fresh(['kandang', 'petugas']),
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $tugas = TugasHarian::findOrFail($id);

        $validated = $request->validate([
            'status'               => 'required|in:belum,dalam_proses,selesai,dilewati',
            'catatan_penyelesaian' => 'nullable|string',
            'waktu_selesai'        => 'nullable|date_format:H:i',
        ]);

        if ($validated['status'] === 'dalam_proses' && !$tugas->waktu_mulai) {
            $validated['waktu_mulai'] = now()->format('H:i');
        }

        if (in_array($validated['status'], ['selesai', 'dilewati']) && !$tugas->waktu_selesai) {
            $validated['waktu_selesai'] = now()->format('H:i');
        }

        $tugas->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Status tugas diperbarui',
            'data'    => $tugas->fresh(['kandang', 'petugas']),
        ]);
    }

    public function destroy($id)
    {
        TugasHarian::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dihapus',
        ]);
    }

    // ─── BULK REASSIGN ────────────────────────────────────────────────────────
    // Digunakan saat petugas absen/sakit: pindahkan banyak tugas sekaligus

    public function bulkReassign(Request $request)
    {
        $validated = $request->validate([
            'ids'     => 'required|array|min:1',
            'ids.*'   => 'integer|exists:tugas_harian,id',
            'user_id' => 'required|exists:user,user_id',
        ]);

        $count = TugasHarian::whereIn('id', $validated['ids'])
            ->update([
                'user_id'     => $validated['user_id'],
                'assigned_by' => auth()->id(),
            ]);

        $petugas = User::where('user_id', $validated['user_id'])->first();

        return response()->json([
            'success' => true,
            'message' => "$count tugas berhasil dipindahkan ke {$petugas->nama}",
            'count'   => $count,
        ]);
    }

    // ─── GENERATE RUTIN ───────────────────────────────────────────────────────
    // Generate tugas harian dari template rutin untuk tanggal tertentu

    public function generateRutin(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
        ]);

        $templates = TemplateTugasRutin::where('is_active', true)->get();
        $generated = 0;

        foreach ($templates as $template) {
            $exists = TugasHarian::whereDate('tanggal', $validated['tanggal'])
                ->where('kandang_id', $template->kandang_id)
                ->where('judul', $template->judul)
                ->where('tipe', 'rutin')
                ->exists();

            if ($exists) continue;

            TugasHarian::create([
                'judul'       => $template->judul,
                'deskripsi'   => $template->deskripsi,
                'kandang_id'  => $template->kandang_id,
                'user_id'     => $template->user_id,
                'tanggal'     => $validated['tanggal'],
                'tipe'        => 'rutin',
                'prioritas'   => $template->prioritas,
                'status'      => 'belum',
                'waktu_mulai' => $template->waktu_default
                    ? \Carbon\Carbon::parse($template->waktu_default)->format('H:i')
                    : null,
                'assigned_by' => auth()->id(),
            ]);

            $generated++;
        }

        $skipped = $templates->count() - $generated;

        return response()->json([
            'success'   => true,
            'message'   => "$generated tugas rutin di-generate" . ($skipped > 0 ? ", $skipped dilewati (sudah ada)" : ""),
            'generated' => $generated,
            'skipped'   => $skipped,
        ]);
    }

    // ─── MOBILE ───────────────────────────────────────────────────────────────

    public function mobile(Request $request)
    {
        $tanggal = today()->toDateString();
        $userId  = auth()->id();

        $tugas = TugasHarian::with(['kandang'])
            ->whereDate('tanggal', $tanggal)
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereNull('user_id');
            })
            ->orderByRaw("CASE status
                WHEN 'dalam_proses' THEN 1
                WHEN 'belum' THEN 2
                WHEN 'dilewati' THEN 3
                WHEN 'selesai' THEN 4
                END")
            ->orderBy('prioritas', 'desc')
            ->get();

        $summary = [
            'total'   => $tugas->count(),
            'selesai' => $tugas->where('status', 'selesai')->count(),
            'belum'   => $tugas->whereIn('status', ['belum', 'dalam_proses'])->count(),
            'persen'  => $tugas->count() > 0
                ? round($tugas->where('status', 'selesai')->count() / $tugas->count() * 100)
                : 0,
        ];

        return view('tugas-harian.mobile', compact('tugas', 'summary', 'tanggal'));
    }
}
