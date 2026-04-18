<?php

namespace App\Services;

/**
 * Service untuk membangun pohon silsilah (pedigree tree) domba
 * secara rekursif sampai N generasi ke atas.
 */
class SilsilahService
{
    /**
     * @param  int  $dombaId  ID domba yang akan dibangun silsilahnya
     * @param  int  $depth    Jumlah generasi ke atas yang diambil (default: 3)
     * @return array          Struktur pohon silsilah
     */
    public function buildPedigreeTree(int $dombaId, int $depth = 3): array
    {
        throw new \RuntimeException('Not implemented yet — assigned to Dev D');
    }
}
