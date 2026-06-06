<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PemberianPakanSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('pemberian_pakan')->count() > 0) {
            $this->command->warn('PemberianPakanSeeder: Data sudah ada, skip.');
            return;
        }

        $pakanIds = DB::table('pakan_stok')->pluck('pakan_id')->toArray();
        $dombaTags = DB::table('domba')
            ->where('status', 'aktif')
            ->orderBy('ear_tag_id')
            ->limit(15)
            ->pluck('ear_tag_id')
            ->toArray();

        $userId = DB::table('user')->where('role', 'pengurus_kandang')->value('user_id');

        if (empty($pakanIds)) {
            $this->command->error('Tidak ada data pakan stok. Jalankan PakanStokSeeder dulu.');
            return;
        }
        if (empty($dombaTags)) {
            $this->command->error('Tidak ada domba aktif. Jalankan DombaSeeder dulu.');
            return;
        }

        $now     = Carbon::now();
        $records = [];

        // 7 hari terakhir × 15 domba × 2 sesi (pagi + sore) = 210 record
        foreach ($dombaTags as $tag) {
            $prefix  = substr($tag, 0, 1);
            $baseGram = match ($prefix) {
                'P' => 400,   // pejantan lebih banyak
                'I' => 350,   // indukan
                'R' => 280,   // dara
                'A' => 180,   // cempe
                default => 300,
            };

            for ($hari = 6; $hari >= 0; $hari--) {
                $tanggal = Carbon::now()->subDays($hari)->toDateString();

                foreach (['pagi', 'sore'] as $sesi) {
                    // Pagi: rumput lebih dominan; sore: konsentrat
                    $records[] = [
                        'pakan_id'          => $pakanIds[array_rand($pakanIds)],
                        'ear_tag_id'        => $tag,
                        'user_id'           => $userId,
                        'tanggal_pemberian' => $tanggal,
                        'sesi'              => $sesi,
                        'jumlah_gram'       => $baseGram + rand(-50, 50),
                        'created_at'        => $now,
                        'updated_at'        => $now,
                    ];
                }
            }
        }

        foreach (array_chunk($records, 100) as $chunk) {
            DB::table('pemberian_pakan')->insert($chunk);
        }

        $this->command->info('✅ PemberianPakanSeeder: ' . count($records) . ' record (15 domba × 7 hari × 2 sesi).');
    }
}
