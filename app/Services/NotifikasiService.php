<?php

namespace App\Services;

/**
 * Service untuk mengirim notifikasi ke user (Admin, Kepala Kandang,
 * atau Pengurus Kandang) berdasarkan tipe event sistem.
 */
class NotifikasiService
{
    /**
     * @param  int    $userId   ID user penerima notifikasi
     * @param  string $type     Tipe event yang memicu notifikasi
     * @param  array  $payload  Data tambahan yang disertakan dalam notifikasi
     * @return void
     */
    public function send(int $userId, string $type, array $payload): void
    {
        throw new \RuntimeException('Not implemented yet — assigned to Dev D');
    }
}
