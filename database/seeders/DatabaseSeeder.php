<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed initial data — Default Super Admin & sample master data
     * Run: php artisan db:seed
     */
    public function run(): void
    {
        // 1. Default Users (idempotent — skip if email already exists)
        $users = [
            [
                'nama'     => 'Super Admin Gumolong',
                'email'    => 'admin@gumolong.farm',
                'password' => Hash::make('admin123'),
                'role'     => 'super_admin',
                'nomor_hp' => '081234567890',
            ],
            [
                'nama'     => 'Admin Operasional',
                'email'    => 'operasional@gumolong.farm',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'nomor_hp' => '081234567891',
            ],
            [
                'nama'     => 'Budi Santoso',
                'email'    => 'kandang@gumolong.farm',
                'password' => Hash::make('kandang123'),
                'role'     => 'pengurus_kandang',
                'nomor_hp' => '081234567892',
            ],
        ];

        foreach ($users as $user) {
            $exists = DB::table('user')->where('email', $user['email'])->exists();
            if (! $exists) {
                DB::table('user')->insert(array_merge($user, [
                    'status'     => 'aktif',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]));
            }
        }

        // 2. Sample Kandang
        DB::table('kandang')->insert([
            [
                'nama_kandang' => 'Kandang A - Utama',
                'tipe' => 'utama',
                'kapasitas' => 80,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_kandang' => 'Kandang B - Utama',
                'tipe' => 'utama',
                'kapasitas' => 60,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_kandang' => 'Kandang Isolasi',
                'tipe' => 'isolasi',
                'kapasitas' => 20,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        // 3. Sample Pakan Stok
        DB::table('pakan_stok')->insert([
            [
                'jenis' => 'rumput',
                'nama_pakan' => 'Rumput Gajah',
                'jumlah_stok' => 1200,
                'satuan' => 'kg',
                'stok_minimum' => 200,
                'tanggal_update' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'jenis' => 'konsentrat',
                'nama_pakan' => 'Konsentrat Premium',
                'jumlah_stok' => 500,
                'satuan' => 'kg',
                'stok_minimum' => 100,
                'tanggal_update' => Carbon::today(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        $this->command->info('✅ Users seeded:');
        $this->command->info('   Super Admin  → admin@gumolong.farm       / admin123');
        $this->command->info('   Admin        → operasional@gumolong.farm / admin123');
        $this->command->info('   Pengurus     → kandang@gumolong.farm     / kandang123');
        $this->command->info('✅ Seeded: 3 Kandang (A, B, Isolasi)');
        $this->command->info('✅ Seeded: 2 Pakan Stok (Rumput, Konsentrat)');
    }
}
