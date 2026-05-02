<?php

namespace App\Http\Controllers;

use App\Models\TemplateTugasRutin;
use App\Models\Kandang;
use App\Models\User;
use Illuminate\Http\Request;

class TemplateTugasController extends Controller
{
    public function index()
    {
        $templates   = TemplateTugasRutin::with(['kandang', 'petugas'])
            ->orderBy('kandang_id')
            ->orderBy('waktu_default')
            ->get();
        $kandangs    = Kandang::all();
        $petugasList = User::whereIn('role', ['kepala_kandang', 'pengurus_kandang'])->get();

        return view('tugas-harian.template', compact('templates', 'kandangs', 'petugasList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'         => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'kandang_id'    => 'required|exists:kandang,kandang_id',
            'user_id'       => 'nullable|exists:user,user_id',
            'waktu_default' => 'nullable|date_format:H:i',
            'prioritas'     => 'required|in:rendah,sedang,tinggi',
        ]);

        $template = TemplateTugasRutin::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil ditambahkan',
            'data'    => $template->load(['kandang', 'petugas']),
        ]);
    }

    public function update(Request $request, $id)
    {
        $template = TemplateTugasRutin::findOrFail($id);

        $validated = $request->validate([
            'judul'         => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'kandang_id'    => 'required|exists:kandang,kandang_id',
            'user_id'       => 'nullable|exists:user,user_id',
            'waktu_default' => 'nullable|date_format:H:i',
            'prioritas'     => 'required|in:rendah,sedang,tinggi',
        ]);

        $template->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil diperbarui',
            'data'    => $template->fresh(['kandang', 'petugas']),
        ]);
    }

    public function toggle($id)
    {
        $template = TemplateTugasRutin::findOrFail($id);
        $template->update(['is_active' => !$template->is_active]);

        $label = $template->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'message' => "Template berhasil $label",
            'data'    => $template,
        ]);
    }

    public function destroy($id)
    {
        TemplateTugasRutin::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil dihapus',
        ]);
    }
}
