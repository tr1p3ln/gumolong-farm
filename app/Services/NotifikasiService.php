<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Service untuk mengirim dan mengelola notifikasi sistem.
 *
 * TIPE NOTIFIKASI:
 *   stok_tipis   → stok pakan/obat hampir habis
 *   expired      → obat/vaksin mendekati kadaluarsa
 *   hpl          → domba mendekati hari perkiraan lahir
 *   vaksin       → jadwal vaksinasi
 *   adg_alert    → ADG domba di bawah standar
 *   pakan        → pemberian pakan dicatat (opsional)
 *   obat         → pemberian obat/vaksin dicatat (opsional)
 */
class NotifikasiService
{
    /**
     * Kirim notifikasi ke satu user.
     *
     * @param  int         $userId
     * @param  string      $tipe     stok_tipis|expired|hpl|vaksin|adg_alert|pakan|obat
     * @param  string      $pesan    Teks notifikasi
     * @param  string|null $earTagId Ear tag domba terkait (opsional)
     */
    public function send(int $userId, string $tipe, string $pesan, ?string $earTagId = null): void
    {
        DB::table('notifikasi')->insert([
            'user_id'            => $userId,
            'ear_tag_id'         => $earTagId,
            'tipe'               => $tipe,
            'pesan'              => $pesan,
            'sudah_dibaca'       => false,
            'tanggal_notifikasi' => now(),
        ]);
    }

    /**
     * Kirim notifikasi ke semua user aktif (broadcast).
     * Berguna untuk alert global seperti stok tipis.
     */
    public function broadcast(string $tipe, string $pesan, ?string $earTagId = null): void
    {
        $userIds = DB::table('user')
            ->pluck('user_id'); // ← hapus whereNull deleted_at

        $now  = now();
        $rows = $userIds->map(fn($uid) => [
            'user_id'            => $uid,
            'ear_tag_id'         => $earTagId,
            'tipe'               => $tipe,
            'pesan'              => $pesan,
            'sudah_dibaca'       => false,
            'tanggal_notifikasi' => $now,
        ])->toArray();

        if (!empty($rows)) {
            DB::table('notifikasi')->insert($rows);
        }
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca.
     */
    public function markAsRead(int $notifikasiId, int $userId): void
    {
        DB::table('notifikasi')
            ->where('notifikasi_id', $notifikasiId)
            ->where('user_id', $userId)
            ->update(['sudah_dibaca' => true]);
    }

    /**
     * Tandai semua notifikasi user sebagai sudah dibaca.
     */
    public function markAllAsRead(int $userId): void
    {
        DB::table('notifikasi')
            ->where('user_id', $userId)
            ->where('sudah_dibaca', false)
            ->update(['sudah_dibaca' => true]);
    }

    /**
     * Hitung notifikasi belum dibaca milik user.
     */
    public function unreadCount(int $userId): int
    {
        return DB::table('notifikasi')
            ->where('user_id', $userId)
            ->where('sudah_dibaca', false)
            ->count();
    }

    /**
     * Ambil notifikasi untuk halaman index dengan filter tipe.
     */
    public function getForUser(int $userId, ?string $tipe = null, int $perPage = 15)
    {
        $query = DB::table('notifikasi')
            ->where('user_id', $userId)
            ->orderByDesc('tanggal_notifikasi');

        if ($tipe) {
            $query->where('tipe', $tipe);
        }

        return $query->paginate($perPage);
    }

    /**
     * Hitung per tipe untuk badge filter tabs.
     */
    public function countPerTipe(int $userId): array
    {
        $counts = DB::table('notifikasi')
            ->where('user_id', $userId)
            ->select('tipe', DB::raw('COUNT(*) as total'))
            ->groupBy('tipe')
            ->pluck('total', 'tipe')
            ->toArray();

        $total = array_sum($counts);

    return [
        'semua'        => $total,
        'stok_menipis' => $counts['stok_menipis'] ?? 0, 
        'expired'      => $counts['expired']      ?? 0,
        'hpl'          => $counts['hpl']          ?? 0,
        'vaksin'       => $counts['vaksin']       ?? 0,
        'adg_rendah'   => $counts['adg_rendah']   ?? 0, 
    ];
    }

    // ── HELPER SHORTCUTS ──────────────────────────────────────────

    /** Notif stok tipis — broadcast ke semua user */
    public function stokTipis(string $namaProduk, float $sisaStok, string $satuan, float $minimum): void
    {
        $pesan = "Stok {$namaProduk} sisa {$sisaStok} {$satuan} — di bawah batas minimum stok ({$minimum} {$satuan}).";
        $this->broadcast('stok_menipis', $pesan); // ← ganti stok_tipis jadi stok_menipis
    }

    /** Notif obat/vaksin mendekati expired */
    public function expired(string $namaObat, int $hariLagi, ?string $earTagId = null): void
    {
        $pesan = "{$namaObat} akan kedaluwarsa dalam {$hariLagi} hari.";
        $this->broadcast('expired', $pesan, $earTagId);
    }

    /** Notif domba mendekati HPL */
    public function hpl(string $earTagId, string $namaDomba, int $hariLagi): void
    {
        $pesan = "Indukan {$namaDomba} ({$earTagId}) diperkirakan melahirkan dalam {$hariLagi} hari.";
        $this->broadcast('hpl', $pesan, $earTagId);
    }

    /** Notif jadwal vaksinasi */
    public function vaksin(string $namaVaksin, int $jumlahDomba, string $tanggal): void
    {
        $pesan = "{$jumlahDomba} domba dijadwalkan vaksinasi {$namaVaksin} pada {$tanggal}.";
        $this->broadcast('vaksin', $pesan);
    }

    /** Notif ADG di bawah standar */
    public function adgAlert(string $earTagId, string $namaDomba, float $adg, float $standar): void
    {
        $pesan = "Domba {$namaDomba} ({$earTagId}) — ADG terhitung: {$adg} kg/hari, di bawah standar (≥ {$standar} kg/hari).";
        $this->broadcast('adg_alert', $pesan, $earTagId);
    }
}