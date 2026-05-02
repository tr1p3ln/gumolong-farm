<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Domba;
use App\Models\TugasHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobilePKController extends Controller
{
    public function dashboard()
    {
        $tanggal = today()->toDateString();
        $userId  = auth()->id();

        $tugas = TugasHarian::whereDate('tanggal', $tanggal)
            ->where(fn($q) => $q->where('user_id', $userId)->orWhereNull('user_id'))
            ->get();

        $summary = [
            'total'   => $tugas->count(),
            'selesai' => $tugas->where('status', 'selesai')->count(),
            'persen'  => $tugas->count() > 0
                ? round($tugas->where('status', 'selesai')->count() / $tugas->count() * 100)
                : 0,
        ];

        $totalDomba = Domba::where('status', 'aktif')->count();
        $pejantan   = Domba::where('status', 'aktif')->where('jenis_kelamin', 'jantan')->count();
        $betina     = Domba::where('status', 'aktif')->where('jenis_kelamin', 'betina')->count();
        $perhatian  = DB::table('medical_record')->whereIn('status', ['sakit', 'dalam_perawatan'])->count();

        $recentTugas = $tugas->sortByDesc('updated_at')->take(3)->values();

        return view('mobile.pk.dashboard', compact(
            'summary', 'totalDomba', 'pejantan', 'betina', 'perhatian', 'tanggal', 'recentTugas'
        ));
    }

    public function tugas()
    {
        $tanggal = today()->toDateString();
        $userId  = auth()->id();

        $tugas = TugasHarian::with(['kandang'])
            ->whereDate('tanggal', $tanggal)
            ->where(fn($q) => $q->where('user_id', $userId)->orWhereNull('user_id'))
            ->orderByRaw("CASE status
                WHEN 'dalam_proses' THEN 1
                WHEN 'belum'        THEN 2
                WHEN 'dilewati'     THEN 3
                WHEN 'selesai'      THEN 4
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

        return view('mobile.pk.tugas', compact('tugas', 'summary', 'tanggal'));
    }

    public function timbangan()
    {
        $dombaList = Domba::where('status', 'aktif')
            ->orderBy('ear_tag_id')
            ->get(['ear_tag_id', 'nama', 'kandang_id']);

        return view('mobile.pk.timbangan', compact('dombaList'));
    }

    public function storeTimbangan(Request $request)
    {
        $validated = $request->validate([
            'ear_tag_id'      => 'required|exists:domba,ear_tag_id',
            'berat_kg'        => 'required|numeric|min:0.1|max:999',
            'tanggal_timbang' => 'required|date',
            'catatan'         => 'nullable|string|max:500',
        ]);

        DB::table('penimbangan')->insert([
            'ear_tag_id'      => $validated['ear_tag_id'],
            'tanggal_timbang' => $validated['tanggal_timbang'],
            'berat_kg'        => $validated['berat_kg'],
            'catatan'         => $validated['catatan'] ?? null,
            'status_validasi' => 'pending',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return redirect()->route('pk.timbangan')
            ->with('success', 'Data timbangan berhasil disimpan. Menunggu validasi Kepala Kandang.');
    }

    public function kesehatan()
    {
        $dombaList = Domba::where('status', 'aktif')
            ->orderBy('ear_tag_id')
            ->get(['ear_tag_id', 'nama']);

        return view('mobile.pk.kesehatan', compact('dombaList'));
    }

    public function storeKesehatan(Request $request)
    {
        $validated = $request->validate([
            'ear_tag_id'        => 'required|exists:domba,ear_tag_id',
            'gejala'            => 'required|string|max:1000',
            'tingkat_keparahan' => 'required|in:ringan,sedang,parah',
            'catatan'           => 'nullable|string|max:500',
        ]);

        // Append severity to gejala for the gejala field
        $gejalaTeks = '[' . strtoupper($validated['tingkat_keparahan']) . '] ' . $validated['gejala'];
        if (!empty($validated['catatan'])) {
            $gejalaTeks .= ' | Catatan: ' . $validated['catatan'];
        }

        DB::table('medical_record')->insert([
            'ear_tag_id'   => $validated['ear_tag_id'],
            'tanggal_sakit'=> today(),
            'gejala'       => $gejalaTeks,
            'diagnosa'     => null,
            'status'       => 'sakit',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return redirect()->route('pk.dashboard')
            ->with('success', 'Laporan kesehatan berhasil dikirim ke Kepala Kandang.');
    }

    public function kelahiran()
    {
        $induks = Domba::where('status', 'aktif')
            ->where('jenis_kelamin', 'betina')
            ->orderBy('ear_tag_id')
            ->get(['ear_tag_id', 'nama']);

        return view('mobile.pk.kelahiran', compact('induks'));
    }

    public function storeKelahiran(Request $request)
    {
        $validated = $request->validate([
            'indukan_id'        => 'required|exists:domba,ear_tag_id',
            'tanggal_kelahiran' => 'required|date',
            'jml_anak_hidup'    => 'required|integer|min:0|max:6',
            'jml_anak_mati'     => 'required|integer|min:0',
            'catatan'           => 'nullable|string|max:500',
        ]);

        $kawinId = DB::table('perkawinan')
            ->where('indukan_id', $validated['indukan_id'])
            ->whereIn('status', ['menunggu_konfirmasi', 'bunting'])
            ->latest('tanggal_perkawinan')
            ->value('kawin_id');

        if (!$kawinId) {
            return back()
                ->withErrors(['indukan_id' => 'Tidak ada data perkawinan aktif untuk domba ini. Hubungi Kepala Kandang.'])
                ->withInput();
        }

        DB::table('kelahiran')->insert([
            'kawin_id'          => $kawinId,
            'user_id'           => auth()->id(),
            'tanggal_kelahiran' => $validated['tanggal_kelahiran'],
            'jml_anak_hidup'    => $validated['jml_anak_hidup'],
            'jml_anak_mati'     => $validated['jml_anak_mati'],
            'catatan'           => $validated['catatan'] ?? null,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        DB::table('perkawinan')
            ->where('kawin_id', $kawinId)
            ->update(['status' => 'lahir', 'updated_at' => now()]);

        return redirect()->route('pk.dashboard')
            ->with('success', 'Data kelahiran berhasil dicatat. Kepala Kandang akan melakukan verifikasi dan assign ear tag.');
    }
}
