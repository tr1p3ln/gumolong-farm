<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenimbanganSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('penimbangan')->count() > 0) {
            $this->command->warn('PenimbanganSeeder: Data sudah ada, skip.');
            return;
        }

        $kepalaId = DB::table('user')->where('role', 'kepala_kandang')->value('user_id');

        // 20 domba aktif: campuran pejantan, indukan, dara, cempe
        $dombaTags = DB::table('domba')
            ->where('status', 'aktif')
            ->whereIn('kategori', ['pejantan', 'indukan', 'dara', 'cempe'])
            ->orderBy('ear_tag_id')
            ->limit(20)
            ->pluck('ear_tag_id')
            ->toArray();

        if (empty($dombaTags)) {
            $this->command->error('Tidak ada domba aktif. Jalankan DombaSeeder dulu.');
            return;
        }

        $now     = Carbon::now();
        $records = [];

        foreach ($dombaTags as $tag) {
            // Berat awal berbeda per kategori berdasarkan prefix tag
            $prefix   = substr($tag, 0, 1);
            $beratBase = match ($prefix) {
                'P' => rand(35, 55),   // pejantan
                'I' => rand(28, 48),   // indukan
                'R' => rand(20, 32),   // dara
                'A' => rand(8, 16),    // cempe
                default => rand(20, 40),
            };
            $increment = rand(1, 3); // kg per bulan

            // 4 timbangan: 3 bulan lalu, 2 bulan lalu, 1 bulan lalu, bulan ini
            for ($bulanLalu = 3; $bulanLalu >= 0; $bulanLalu--) {
                $berat    = $beratBase + ($increment * (3 - $bulanLalu));
                $tanggal  = Carbon::now()->subMonths($bulanLalu)->startOfMonth()->addDays(rand(1, 7));
                $adg      = $bulanLalu < 3
                    ? round($increment / 30, 3)
                    : null;
                $statusV  = $bulanLalu > 0 ? 'valid' : 'pending';

                $records[] = [
                    'ear_tag_id'       => $tag,
                    'tanggal_timbang'  => $tanggal->toDateString(),
                    'berat_kg'         => round($berat, 2),
                    'adg'              => $adg,
                    'catatan'          => $bulanLalu > 0 ? 'Penimbangan rutin bulanan' : null,
                    'status_validasi'  => $statusV,
                    'divalidasi_oleh'  => $statusV === 'valid' ? $kepalaId : null,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }
        }

        foreach (array_chunk($records, 50) as $chunk) {
            DB::table('penimbangan')->insert($chunk);
        }

        $this->command->info('✅ PenimbanganSeeder: ' . count($records) . ' record (' . count($dombaTags) . ' domba × 4 timbangan).');
    }
}
