<?php

namespace App\Services;

/**
 * Service untuk mengecek tingkat kekerabatan antara pejantan dan indukan
 * sebelum melakukan perkawinan, untuk mencegah inbreeding sesuai
 * FR-7.4 Silsilah Module.
 */
class InbreedingService
{
    /**
     * @param  int  $pejantanId  ID domba pejantan
     * @param  int  $indukanId   ID domba indukan
     * @return array             Hasil pengecekan kekerabatan
     */
    public function checkRelationship(int $pejantanId, int $indukanId): array
    {
        throw new \RuntimeException('Not implemented yet — assigned to Dev D');
    }
}
