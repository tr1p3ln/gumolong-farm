<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Run: php artisan db:seed
     * Fresh run: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        // ── 1. Users (idempotent via email check) ─────────────────────
        $users = [
            ['nama' => 'Super Admin Gumolong', 'email' => 'admin@gumolong.farm',        'password' => Hash::make('admin123'),   'role' => 'super_admin',       'nomor_hp' => '081234567890'],
            ['nama' => 'Admin Operasional',    'email' => 'operasional@gumolong.farm',  'password' => Hash::make('admin123'),   'role' => 'admin',             'nomor_hp' => '081234567891'],
            ['nama' => 'Siti Rahayu',          'email' => 'kepala@gumolong.farm',       'password' => Hash::make('kepala123'),  'role' => 'kepala_kandang',    'nomor_hp' => '081234567892'],
            ['nama' => 'Budi Santoso',         'email' => 'kandang@gumolong.farm',      'password' => Hash::make('kandang123'), 'role' => 'pengurus_kandang',  'nomor_hp' => '081234567893'],
        ];

        foreach ($users as $user) {
            if (! DB::table('user')->where('email', $user['email'])->exists()) {
                DB::table('user')->insert(array_merge($user, [
                    'status'     => 'aktif',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]));
            }
        }

        // ── 2. Kandang (skip jika sudah ada) ──────────────────────────
        if (DB::table('kandang')->count() === 0) {
            DB::table('kandang')->insert([
                ['nama_kandang' => 'Kandang A - Utama',   'tipe' => 'utama',      'kapasitas' => 80, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['nama_kandang' => 'Kandang B - Utama',   'tipe' => 'utama',      'kapasitas' => 60, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['nama_kandang' => 'Kandang Isolasi',      'tipe' => 'isolasi',    'kapasitas' => 20, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['nama_kandang' => 'Kandang Persalinan',   'tipe' => 'persalinan', 'kapasitas' => 10, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['nama_kandang' => 'Kandang Kawin',        'tipe' => 'kawin',      'kapasitas' => 15, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ]);
        }

        // ── 3. Pakan Stok awal (skip jika sudah ada) ──────────────────
        if (DB::table('pakan_stok')->count() === 0) {
            DB::table('pakan_stok')->insert([
                [
                    'jenis' => 'rumput', 'nama_pakan' => 'Rumput Gajah',
                    'jumlah_stok' => 1200, 'satuan' => 'kg', 'stok_minimum' => 200,
                    'tanggal_update' => Carbon::today(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                ],
                [
                    'jenis' => 'konsentrat', 'nama_pakan' => 'Konsentrat Premium',
                    'jumlah_stok' => 500, 'satuan' => 'kg', 'stok_minimum' => 100,
                    'tanggal_update' => Carbon::today(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                ],
            ]);
        }

        $this->command->info('✅ Users seeded:');
        $this->command->info('   Super Admin      → admin@gumolong.farm       / admin123');
        $this->command->info('   Admin            → operasional@gumolong.farm / admin123');
        $this->command->info('   Kepala Kandang   → kepala@gumolong.farm      / kepala123');
        $this->command->info('   Pengurus Kandang → kandang@gumolong.farm     / kandang123');

        // ── 4. Child Seeders (urutan sesuai dependency) ───────────────
        $this->call([
            DombaSeeder::class,            // domba (butuh kandang)
            ObatVaksinSeeder::class,       // obat_vaksin
            PakanStokSeeder::class,        // pakan_stok tambahan
            PenimbanganSeeder::class,      // penimbangan (butuh domba, user)
            MedicalRecordSeeder::class,    // medical_record + pemakaian_obat (butuh domba, obat)
            VaksinasiSeeder::class,        // vaksinasi (butuh domba, obat vaksin)
            PerkawinanSeeder::class,       // perkawinan (butuh domba pejantan+indukan, user)
            KelahiranSeeder::class,        // kelahiran + anak_lahir (butuh perkawinan)
            PemberianPakanSeeder::class,   // pemberian_pakan (butuh pakan_stok, domba, user)
            TugasHarianSeeder::class,      // tugas_harian (butuh kandang, user)
            TemplateTugasRutinSeeder::class, // template_tugas_rutin (butuh kandang, user)
            NotifikasiSeeder::class,       // notifikasi (butuh user, domba)
        ]);
    }
}
