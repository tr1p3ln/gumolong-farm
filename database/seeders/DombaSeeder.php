<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DombaSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil kandang_id yang tersedia
        $kandangUtama = DB::table('kandang')->where('tipe', 'utama')->pluck('kandang_id')->toArray();
        $kandangIsolasi = DB::table('kandang')->where('tipe', 'isolasi')->value('kandang_id');

        if (empty($kandangUtama)) {
            $this->command->error('Tidak ada kandang utama. Jalankan DatabaseSeeder dulu.');
            return;
        }

        $defaultKandang = $kandangUtama[0];
        $kandangB       = $kandangUtama[1] ?? $defaultKandang;

        $ras = ['Garut', 'Garut', 'Garut', 'Merino', 'Batur', 'Texel', 'Jawa Randu'];

        $namaJantan = [
            'Bagas','Bima','Dewa','Arga','Bayu','Cakra','Fajar','Galih',
            'Hendra','Irwan','Jaya','Kresna','Langit','Mahesa','Nanda',
            'Oka','Pandu','Raja','Satria','Titan','Wira','Yudha','Zaki',
            'Aldo','Benny',
        ];

        $namaBetina = [
            'Sari','Dewi','Ayu','Putri','Bunga','Melati','Mawar','Kenanga',
            'Dahlia','Anggrek','Lily','Cempaka','Jasmin','Seruni','Aster',
            'Wulan','Ratih','Sekar','Endah','Nadia','Sinta','Fatimah',
            'Aisyah','Laras','Rini',
        ];

        $now    = Carbon::now();
        $domba  = [];
        $existing = DB::table('domba')->pluck('ear_tag_id')->flip()->toArray();

        $jantanIdx  = 0;
        $betinaIdx  = 0;

        // Helper: tambah record jika ear_tag belum ada
        $add = function (array $row) use (&$domba, &$existing, $now) {
            if (isset($existing[$row['ear_tag_id']])) return;
            $domba[] = array_merge($row, [
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
        };

        // ── Pejantan (25 ekor) ──────────────────────────────────────
        for ($i = 1; $i <= 25; $i++) {
            $tag    = 'P-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $status = $i <= 20 ? 'aktif' : ($i <= 23 ? 'terjual' : 'mati');
            $add([
                'ear_tag_id'    => $tag,
                'nama'          => $namaJantan[$jantanIdx++ % count($namaJantan)],
                'jenis_kelamin' => 'jantan',
                'ras'           => $ras[array_rand($ras)],
                'tanggal_lahir' => Carbon::now()->subMonths(rand(18, 48))->toDateString(),
                'kategori'      => 'pejantan',
                'status'        => $status,
                'asal'          => rand(0, 1) ? 'lahir_di_kandang' : 'dari_luar',
                'catatan'       => null,
                'kandang_id'    => $i % 2 === 0 ? $kandangB : $defaultKandang,
                'induk_id'      => null,
                'ayah_id'       => null,
            ]);
        }

        // ── Indukan (40 ekor) ───────────────────────────────────────
        for ($i = 1; $i <= 40; $i++) {
            $tag    = 'I-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $status = $i <= 33 ? 'aktif' : ($i <= 38 ? 'terjual' : 'mati');
            if ($i === 39) $status = 'karantina';
            $add([
                'ear_tag_id'    => $tag,
                'nama'          => $i <= 20 ? $namaBetina[$betinaIdx++ % count($namaBetina)] : null,
                'jenis_kelamin' => 'betina',
                'ras'           => $ras[array_rand($ras)],
                'tanggal_lahir' => Carbon::now()->subMonths(rand(24, 72))->toDateString(),
                'kategori'      => 'indukan',
                'status'        => $status,
                'asal'          => rand(0, 1) ? 'lahir_di_kandang' : 'dari_luar',
                'catatan'       => null,
                'kandang_id'    => $status === 'karantina' && $kandangIsolasi
                                    ? $kandangIsolasi
                                    : ($i % 2 === 0 ? $kandangB : $defaultKandang),
                'induk_id'      => null,
                'ayah_id'       => null,
            ]);
        }

        // ── Dara (20 ekor) ──────────────────────────────────────────
        for ($i = 1; $i <= 20; $i++) {
            $tag = 'R-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $add([
                'ear_tag_id'    => $tag,
                'nama'          => null,
                'jenis_kelamin' => 'betina',
                'ras'           => $ras[array_rand($ras)],
                'tanggal_lahir' => Carbon::now()->subMonths(rand(8, 18))->toDateString(),
                'kategori'      => 'dara',
                'status'        => $i <= 17 ? 'aktif' : 'terjual',
                'asal'          => 'lahir_di_kandang',
                'catatan'       => null,
                'kandang_id'    => $i % 2 === 0 ? $kandangB : $defaultKandang,
                'induk_id'      => null,
                'ayah_id'       => null,
            ]);
        }

        // ── Cempe (15 ekor) ─────────────────────────────────────────
        for ($i = 1; $i <= 15; $i++) {
            $jk  = $i % 2 === 0 ? 'betina' : 'jantan';
            $tag = 'A-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $add([
                'ear_tag_id'    => $tag,
                'nama'          => null,
                'jenis_kelamin' => $jk,
                'ras'           => $ras[array_rand($ras)],
                'tanggal_lahir' => Carbon::now()->subMonths(rand(1, 6))->toDateString(),
                'kategori'      => 'cempe',
                'status'        => 'aktif',
                'asal'          => 'lahir_di_kandang',
                'catatan'       => null,
                'kandang_id'    => $defaultKandang,
                'induk_id'      => null,
                'ayah_id'       => null,
            ]);
        }

        if (empty($domba)) {
            $this->command->warn('Semua ear_tag sudah ada, tidak ada data baru.');
            return;
        }

        // Insert dalam batch 50
        foreach (array_chunk($domba, 50) as $chunk) {
            DB::table('domba')->insert($chunk);
        }

        $total = count($domba);
        $this->command->info("✅ DombaSeeder: {$total} ekor domba berhasil ditambahkan.");
        $this->command->info('   P-001..P-025 → Pejantan');
        $this->command->info('   I-001..I-040 → Indukan');
        $this->command->info('   R-001..R-020 → Dara');
        $this->command->info('   A-001..A-015 → Cempe');
    }
}
