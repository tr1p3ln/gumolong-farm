<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VaksinasiSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('vaksinasi')->count() > 0) {
            $this->command->warn('VaksinasiSeeder: Data sudah ada, skip.');
            return;
        }

        $vaksinRows = DB::table('obat_vaksin')
            ->where('tipe', 'vaksin')
            ->get(['obat_id', 'interval_vaksinasi'])
            ->toArray();

        if (empty($vaksinRows)) {
            $this->command->error('Tidak ada vaksin. Jalankan ObatVaksinSeeder dulu.');
            return;
        }

        // Map: obat_id => interval
        $vaksinMap = [];
        foreach ($vaksinRows as $row) {
            $vaksinMap[$row->obat_id] = $row->interval_vaksinasi ?? 365;
        }
        $vaksinIds = array_keys($vaksinMap);

        $dombaTags = DB::table('domba')
            ->where('status', 'aktif')
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

        foreach ($dombaTags as $idx => $tag) {
            // Setiap domba mendapat 2-3 vaksin acak
            shuffle($vaksinIds);
            $jumlahVaksin = rand(2, 3);

            for ($v = 0; $v < $jumlahVaksin; $v++) {
                $obatId   = $vaksinIds[$v];
                $interval = $vaksinMap[$obatId];

                $tanggalVaksinasi  = Carbon::now()->subDays(rand(30, 200));
                $tanggalBerikutnya = $tanggalVaksinasi->copy()->addDays($interval);

                $catatan = match ($v) {
                    0 => 'Vaksinasi program tahunan perdana',
                    1 => 'Vaksinasi booster atau jadwal berkala',
                    default => 'Vaksinasi tambahan sesuai kondisi',
                };

                $records[] = [
                    'ear_tag_id'         => $tag,
                    'obat_id'            => $obatId,
                    'tanggal_vaksinasi'  => $tanggalVaksinasi->toDateString(),
                    'tanggal_berikutnya' => $tanggalBerikutnya->toDateString(),
                    'catatan'            => $catatan,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];
            }
        }

        foreach (array_chunk($records, 50) as $chunk) {
            DB::table('vaksinasi')->insert($chunk);
        }

        $this->command->info('✅ VaksinasiSeeder: ' . count($records) . ' record vaksinasi untuk ' . count($dombaTags) . ' domba.');
    }
}
