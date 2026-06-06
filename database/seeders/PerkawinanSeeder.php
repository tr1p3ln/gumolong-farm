<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerkawinanSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('perkawinan')->count() > 0) {
            $this->command->warn('PerkawinanSeeder: Data sudah ada, skip.');
            return;
        }

        $pejantanIds = DB::table('domba')
            ->where('kategori', 'pejantan')
            ->where('status', 'aktif')
            ->pluck('ear_tag_id')
            ->toArray();

        $indukanIds = DB::table('domba')
            ->where('kategori', 'indukan')
            ->where('status', 'aktif')
            ->pluck('ear_tag_id')
            ->toArray();

        $userId   = DB::table('user')->where('role', 'admin')->value('user_id');
        $kepalaId = DB::table('user')->where('role', 'kepala_kandang')->value('user_id');

        if (empty($pejantanIds) || empty($indukanIds)) {
            $this->command->error('Tidak ada domba pejantan/indukan aktif. Jalankan DombaSeeder dulu.');
            return;
        }

        $now = Carbon::now();

        // 20 pasangan: distribusi status realistis
        $distribusiStatus = [
            'lahir',               // 1
            'lahir',               // 2
            'lahir',               // 3
            'lahir',               // 4
            'lahir',               // 5
            'bunting',             // 6
            'bunting',             // 7
            'bunting',             // 8
            'bunting',             // 9
            'bunting',             // 10
            'bunting',             // 11
            'tidak_bunting',       // 12
            'tidak_bunting',       // 13
            'tidak_bunting',       // 14
            'menunggu_konfirmasi', // 15
            'menunggu_konfirmasi', // 16
            'menunggu_konfirmasi', // 17
            'menunggu_konfirmasi', // 18
            'gagal',               // 19
            'gagal',               // 20
        ];

        shuffle($indukanIds);
        $records = [];

        foreach ($distribusiStatus as $i => $status) {
            $pejantanId = $pejantanIds[$i % count($pejantanIds)];
            $indukanId  = $indukanIds[$i % count($indukanIds)];

            // Tanggal kawin: 30–180 hari lalu (lahir/bunting lebih lama)
            $hariLalu      = in_array($status, ['lahir', 'bunting']) ? rand(90, 180) : rand(20, 60);
            $tanggalKawin  = Carbon::now()->subDays($hariLalu);
            $estimasiLahir = $tanggalKawin->copy()->addDays(150); // ~5 bulan gestasi domba

            $tglKonfirmasi     = null;
            $metodeKonfirmasi  = null;
            $catatanKonfirmasi = null;
            $dikonfirmasiOleh  = null;

            if (in_array($status, ['bunting', 'tidak_bunting', 'lahir'])) {
                $tglKonfirmasi    = $tanggalKawin->copy()->addDays(rand(21, 28))->toDateString();
                $metodeKonfirmasi = in_array($status, ['bunting', 'lahir'])
                    ? (['USG', 'observasi_fisik'])[rand(0, 1)]
                    : 'observasi_fisik';
                $catatanKonfirmasi = match ($status) {
                    'bunting'      => 'Konfirmasi bunting via ' . $metodeKonfirmasi . ', kondisi baik',
                    'tidak_bunting'=> 'Tidak terlihat tanda kebuntingan setelah observasi 4 minggu',
                    'lahir'        => 'Indukan berhasil melahirkan, status diperbarui',
                };
                $dikonfirmasiOleh = $kepalaId;
            }

            $records[] = [
                'pejantan_id'        => $pejantanId,
                'indukan_id'         => $indukanId,
                'user_id'            => $userId,
                'tanggal_perkawinan' => $tanggalKawin->toDateString(),
                'metode'             => $i % 5 === 0 ? 'inseminasi_buatan' : 'alami',
                'estimasi_lahir'     => $estimasiLahir->toDateString(),
                'status'             => $status,
                'tgl_konfirmasi'     => $tglKonfirmasi,
                'metode_konfirmasi'  => $metodeKonfirmasi,
                'catatan_konfirmasi' => $catatanKonfirmasi,
                'dikonfirmasi_oleh'  => $dikonfirmasiOleh,
                'created_at'         => $now,
                'updated_at'         => $now,
            ];
        }

        DB::table('perkawinan')->insert($records);

        $this->command->info('✅ PerkawinanSeeder: ' . count($records) . ' record (lahir×5, bunting×6, tidak_bunting×3, menunggu×4, gagal×2).');
    }
}
