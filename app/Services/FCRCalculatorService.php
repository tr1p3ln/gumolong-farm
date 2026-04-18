<?php

namespace App\Services;

/**
 * Service untuk menghitung Feed Conversion Ratio (FCR) per ekor domba
 * berdasarkan konsumsi pakan dan pertambahan bobot dalam periode tertentu.
 */
class FCRCalculatorService
{
    /**
     * @param  int     $dombaId      ID domba yang dihitung FCR-nya
     * @param  string  $periodeAwal  Tanggal awal periode (Y-m-d)
     * @param  string  $periodeAkhir Tanggal akhir periode (Y-m-d)
     * @return float                 Nilai FCR
     */
    public function calculate(int $dombaId, string $periodeAwal, string $periodeAkhir): float
    {
        throw new \RuntimeException('Not implemented yet — assigned to Dev B');
    }
}
