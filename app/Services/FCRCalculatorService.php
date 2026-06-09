<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * FCR = Total Pakan (kg) / Pertambahan Bobot Badan (kg)
 *
 * Threshold FCR domba:
 *   FCR < 5.0  → Sangat Efisien
 *   FCR 5–7    → Normal
 *   FCR 7–9    → Kurang Efisien
 *   FCR > 9.0  → Perlu Evaluasi
 */
class FCRCalculatorService
{
    // ──────────────────────────────────────────────────────────────
    // PUBLIC API
    // ──────────────────────────────────────────────────────────────

    /**
     * Hitung FCR satu domba dalam periode tertentu.
     * FCR = Total Pakan (kg) / Delta Bobot (kg)
     */
    public function calculate(string $earTagId, string $periodeAwal, string $periodeAkhir): ?float
{
    $this->validatePeriode($periodeAwal, $periodeAkhir);

    $totalPakanKg = $this->getTotalPakanKg($earTagId, $periodeAwal, $periodeAkhir);
    if ($totalPakanKg === null || $totalPakanKg <= 0) return null;

    // Ambil berat pertama (awal) dan berat terakhir (sekarang)
    $beratAwal  = $this->getBeratPertama($earTagId);
    $beratAkhir = $this->getBeratTerakhir($earTagId);

    if ($beratAwal === null || $beratAkhir === null) return null;
    if ($beratAkhir <= $beratAwal) return null;

    $delta = round($beratAkhir - $beratAwal, 3);
    return round($totalPakanKg / $delta, 2);
}

    /**
     * Shortcut: FCR 30 hari terakhir.
     */
    public function calculateLast30Days(string $earTagId): ?float
    {
        return $this->calculate(
            $earTagId,
            now()->subDays(30)->toDateString(),
            now()->toDateString()
        );
    }

    /**
     * Bulk hitung FCR banyak domba sekaligus.
     * Return: ['J-001' => 5.2, 'J-002' => null, ...]
     */
    public function calculateBulk(array $earTagIds, string $periodeAwal, string $periodeAkhir): array
    {
        $this->validatePeriode($periodeAwal, $periodeAkhir);
        if (empty($earTagIds)) return [];

        $pakanRows  = DB::table('pemberian_pakan')
            ->whereIn('ear_tag_id', $earTagIds)
            ->whereBetween('tanggal_pemberian', [$periodeAwal, $periodeAkhir])
            ->selectRaw('ear_tag_id, SUM(jumlah_gram) / 1000.0 as total_kg')
            ->groupBy('ear_tag_id')
            ->pluck('total_kg', 'ear_tag_id');

        $beratAwal  = $this->getBatchBerat($earTagIds, $periodeAwal,  'awal');
        $beratAkhir = $this->getBatchBerat($earTagIds, $periodeAkhir, 'akhir');

        $result = [];
        foreach ($earTagIds as $id) {
            $pakan = $pakanRows[$id] ?? null;
            $awal  = $beratAwal[$id]  ?? null;
            $akhir = $beratAkhir[$id] ?? null;

            if (!$pakan || $pakan <= 0)  { $result[$id] = null; continue; }
            if (!$awal  || !$akhir)      { $result[$id] = null; continue; }

            $delta = $akhir - $awal;
            if ($delta <= 0)             { $result[$id] = null; continue; }

            $result[$id] = round($pakan / $delta, 2);
        }

        return $result;
    }

    /**
     * Status FCR berdasarkan threshold domba yang benar.
     *
     * FCR < 5   → sangat_efisien
     * FCR 5–7   → normal
     * FCR 7–9   → kurang_efisien
     * FCR > 9   → perlu_evaluasi
     */
    public function getStatus(?float $fcr): string
    {
        return match(true) {
            $fcr === null => 'tidak_ada_data',
            $fcr < 5.0    => 'sangat_efisien',
            $fcr <= 7.0   => 'normal',
            $fcr <= 9.0   => 'kurang_efisien',
            default       => 'perlu_evaluasi',
        };
    }

    /**
     * Label tampil untuk badge UI.
     */
    public function getStatusLabel(?float $fcr): string
    {
        return match($this->getStatus($fcr)) {
            'sangat_efisien' => 'Sangat Efisien',
            'normal'         => 'Normal',
            'kurang_efisien' => 'Kurang Efisien',
            'perlu_evaluasi' => 'Perlu Evaluasi',
            default          => '—',
        };
    }

    /**
     * Warna badge untuk UI (Tailwind classes).
     */
    public function getStatusColor(?float $fcr): array
    {
        return match($this->getStatus($fcr)) {
            'sangat_efisien' => ['bg' => 'bg-green-100',  'text' => 'text-green-700',  'hex' => '#15803d'],
            'normal'         => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'hex' => '#1d4ed8'],
            'kurang_efisien' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'hex' => '#a16207'],
            'perlu_evaluasi' => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'hex' => '#b91c1c'],
            default          => ['bg' => 'bg-gray-100',   'text' => 'text-gray-500',   'hex' => '#6b7280'],
        };
    }

    /**
     * Rata-rata pakan harian dalam gram.
     * Rumus: SUM(jumlah_gram) / jumlah hari dalam periode
     *
     * @return float|null  gram per hari
     */
    public function getRataRataPakanHarian(string $earTagId, string $periodeAwal, string $periodeAkhir): ?float
    {
        $this->validatePeriode($periodeAwal, $periodeAkhir);

        $totalGram = DB::table('pemberian_pakan')
            ->where('ear_tag_id', $earTagId)
            ->whereBetween('tanggal_pemberian', [$periodeAwal, $periodeAkhir])
            ->sum('jumlah_gram');

        if (!$totalGram) return null;

        // Hitung jumlah hari unik yang ada datanya (bukan selisih tanggal)
        $hariAda = DB::table('pemberian_pakan')
            ->where('ear_tag_id', $earTagId)
            ->whereBetween('tanggal_pemberian', [$periodeAwal, $periodeAkhir])
            ->distinct()
            ->count('tanggal_pemberian');

        if ($hariAda === 0) return null;

        return round($totalGram / $hariAda, 1);
    }

    /**
     * Detail lengkap FCR + semua komponen perhitungan.
     * Dipakai di endpoint stats() controller.
     */
    public function detail(string $earTagId, string $periodeAwal, string $periodeAkhir): array
    {
        $this->validatePeriode($periodeAwal, $periodeAkhir);

        $totalPakanKg = $this->getTotalPakanKg($earTagId, $periodeAwal, $periodeAkhir);
        $beratAwal    = $this->getBeratPertama($earTagId);
        $beratAkhir   = $this->getBeratTerakhir($earTagId);
        $deltaBobot   = ($beratAwal !== null && $beratAkhir !== null && $beratAkhir > $beratAwal)
                            ? round($beratAkhir - $beratAwal, 3)
                            : null;

        $fcr = null;
        if ($totalPakanKg > 0 && $deltaBobot !== null && $deltaBobot > 0) {
            $fcr = round($totalPakanKg / $deltaBobot, 2);
        }

        $rataHarian = $this->getRataRataPakanHarian($earTagId, $periodeAwal, $periodeAkhir);

        return [
            'fcr'                  => $fcr,
            'status'               => $this->getStatus($fcr),
            'status_label'         => $this->getStatusLabel($fcr),
            'status_color'         => $this->getStatusColor($fcr),
            'total_pakan_kg'       => $totalPakanKg,
            'rata_pakan_harian_gr' => $rataHarian,
            'berat_awal_kg'        => $beratAwal,
            'berat_akhir_kg'       => $beratAkhir,
            'delta_bobot_kg'       => $deltaBobot,
            'periode_awal'         => $periodeAwal,
            'periode_akhir'        => $periodeAkhir,
        ];
    }

    // ──────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────────

    private function getTotalPakanKg(string $earTagId, string $awal, string $akhir): ?float
    {
        $total = DB::table('pemberian_pakan')
            ->where('ear_tag_id', $earTagId)
            ->whereBetween('tanggal_pemberian', [$awal, $akhir])
            ->sum('jumlah_gram');

        return $total > 0 ? round($total / 1000, 4) : null;
    }

    private function getDeltaBobot(string $earTagId, string $awal, string $akhir): ?float
    {
        $beratAwal  = $this->getBeratTerdekat($earTagId, $awal,  'awal');
        $beratAkhir = $this->getBeratTerdekat($earTagId, $akhir, 'akhir');

        if ($beratAwal === null || $beratAkhir === null) return null;

        return round($beratAkhir - $beratAwal, 3);
    }

    private function getBeratTerdekat(string $earTagId, string $tanggal, string $arahPencarian): ?float
    {
        if ($arahPencarian === 'awal') {
            // Ambil timbangan PERTAMA yang pernah ada
            return DB::table('penimbangan')
                ->where('ear_tag_id', $earTagId)
                ->orderBy('tanggal_timbang', 'ASC')
                ->value('berat_kg');
        }

        // Ambil timbangan TERAKHIR yang pernah ada
        return DB::table('penimbangan')
            ->where('ear_tag_id', $earTagId)
            ->orderBy('tanggal_timbang', 'DESC')
            ->value('berat_kg');
    }
    private function getBatchBerat(array $earTagIds, string $tanggal, string $arahPencarian): array
    {
        $toleransiHari = 14;

        if ($arahPencarian === 'awal') {
            $batas = date('Y-m-d', strtotime($tanggal . ' -' . $toleransiHari . ' days'));
            $order = 'DESC';
            $range = [$batas, $tanggal];
        } else {
            $batas = date('Y-m-d', strtotime($tanggal . ' +' . $toleransiHari . ' days'));
            $order = 'ASC';
            $range = [$tanggal, $batas];
        }

        $rows = DB::table(DB::raw('(
            SELECT DISTINCT ON (ear_tag_id) ear_tag_id, berat_kg
            FROM penimbangan
            WHERE ear_tag_id = ANY(?)
            AND tanggal_timbang BETWEEN ? AND ?
            ORDER BY ear_tag_id, tanggal_timbang ' . $order . '
        ) as sub'))
        ->setBindings(['{' . implode(',', $earTagIds) . '}', $range[0], $range[1]])
        ->select('ear_tag_id', 'berat_kg')
        ->get();

        return $rows->pluck('berat_kg', 'ear_tag_id')->toArray();
    }

    private function validatePeriode(string $awal, string $akhir): void
    {
        if (!strtotime($awal) || !strtotime($akhir)) {
            throw new InvalidArgumentException("Format tanggal tidak valid. Gunakan Y-m-d.");
        }
        if ($awal > $akhir) {
            throw new InvalidArgumentException("Periode awal ($awal) tidak boleh lebih besar dari periode akhir ($akhir).");
        }
    }

        private function getBeratPertama(string $earTagId): ?float
    {
        return DB::table('penimbangan')
            ->where('ear_tag_id', $earTagId)
            ->orderBy('tanggal_timbang', 'ASC')
            ->value('berat_kg');
    }

    private function getBeratTerakhir(string $earTagId): ?float
    {
        return DB::table('penimbangan')
            ->where('ear_tag_id', $earTagId)
            ->orderBy('tanggal_timbang', 'DESC')
            ->value('berat_kg');
    }
}