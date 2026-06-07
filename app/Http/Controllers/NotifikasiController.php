<?php

namespace App\Http\Controllers;

use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    private NotifikasiService $service;

    public function __construct(NotifikasiService $service)
    {
        $this->service = $service;
    }

    /**
     * Halaman daftar notifikasi dengan filter tipe.
     */
    public function index(Request $request)
    {
        $userId  = auth()->id();
        $tipe    = $request->get('tipe'); // null = semua

        $notifikasi  = $this->service->getForUser($userId, $tipe, 15);
        $counts      = $this->service->countPerTipe($userId);
        $unreadCount = $this->service->unreadCount($userId);

        return view('notifikasi.index', compact(
            'notifikasi',
            'counts',
            'unreadCount',
            'tipe',
        ));
    }

    /**
     * Tandai satu notifikasi sebagai dibaca (AJAX).
     */
    public function markAsRead(int $id)
    {
        $this->service->markAsRead($id, auth()->id());

        return response()->json(['success' => true]);
    }

    /**
     * Tandai semua notifikasi sebagai dibaca (AJAX).
     */
    public function markAllRead()
    {
        $this->service->markAllAsRead(auth()->id());

        return response()->json(['success' => true]);
    }

    /**
     * Endpoint polling badge lonceng — dipanggil setiap X detik dari navbar.
     */
    public function unreadCount()
    {
        return response()->json([
            'count' => $this->service->unreadCount(auth()->id()),
        ]);
    }

    /**
     * Hapus satu notifikasi.
     */
    public function destroy(int $id)
    {
        \Illuminate\Support\Facades\DB::table('notifikasi')
            ->where('notifikasi_id', $id)
            ->where('user_id', auth()->id())
            ->delete();

        return response()->json(['success' => true]);
    }
}