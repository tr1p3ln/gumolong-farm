<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelahiranSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('kelahiran')->count() > 0) {
            $this->command->warn('KelahiranSeeder: Data sudah ada, skip.');
            return;
        }

        $kawinIds = DB::table('perkawinan')
            ->where('status', 'lahir')
            ->pluck('kawin_id')
            ->toArray();

        if (empty($kawinIds)) {
            $this->command->error('Tidak ada perkawinan berstatus lahir. Jalankan PerkawinanSeeder dulu.');
            return;
        }

        $userId = DB::table('user')->where('role', 'pengurus_kandang')->value('user_id');
        $now    = Carbon::now();

        $kelahiranRows = [];
        foreach ($kawinIds as $kawinId) {
            $jmlHidup = rand(1, 3);
            $jmlMati  = rand(0, 1);

            $kelahiranRows[] = [
                'kawin_id'          => $kawinId,
                'user_id'           => $userId,
                'tanggal_kelahiran' => Carbon::now()->subDays(rand(10, 90))->toDateString(),
                'jml_anak_hidup'    => $jmlHidup,
                'jml_anak_mati'     => $jmlMati,
                'bobot_rata_rata'   => round(rand(25, 40) / 10, 2),
                'catatan'           => $jmlMati > 0 ? 'Satu anak lahir lemah, tidak bertahan hidup' : 'Persalinan normal, semua anak sehat',
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }

        DB::table('kelahiran')->insert($kelahiranRows);

        // Ambil lahir_id yang baru dibuat, key-ed by kawin_id
        $lahirMap = DB::table('kelahiran')
            ->pluck('lahir_id', 'kawin_id')
            ->toArray();

        $anakLahirRows = [];
        foreach ($kawinIds as $idx => $kawinId) {
            $lahirId  = $lahirMap[$kawinId];
            $row      = $kelahiranRows[$idx];

            for ($a = 0; $a < $row['jml_anak_hidup']; $a++) {
                $anakLahirRows[] = [
                    'lahir_id'      => $lahirId,
                    'ear_tag_id'    => null,
                    'jenis_kelamin' => $a % 2 === 0 ? 'jantan' : 'betina',
                    'bobot_lahir'   => round(rand(22, 42) / 10, 2),
                    'kondisi'       => 'hidup',
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }

            for ($a = 0; $a < $row['jml_anak_mati']; $a++) {
                $anakLahirRows[] = [
                    'lahir_id'      => $lahirId,
                    'ear_tag_id'    => null,
                    'jenis_kelamin' => 'jantan',
                    'bobot_lahir'   => round(rand(14, 22) / 10, 2),
                    'kondisi'       => 'mati',
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }
        }

        if (! empty($anakLahirRows)) {
            DB::table('anak_lahir')->insert($anakLahirRows);
        }

        $this->command->info('✅ KelahiranSeeder: ' . count($kelahiranRows) . ' kelahiran dan ' . count($anakLahirRows) . ' anak lahir.');
    }
}
