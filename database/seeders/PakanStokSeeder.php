<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PakanStokSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $items = [
            ['jenis' => 'rumput',     'nama_pakan' => 'Rumput Raja (King Grass)',   'jumlah_stok' => 800,  'satuan' => 'kg', 'stok_minimum' => 150],
            ['jenis' => 'rumput',     'nama_pakan' => 'Rumput Odot (Mott Dwarf)',   'jumlah_stok' => 600,  'satuan' => 'kg', 'stok_minimum' => 100],
            ['jenis' => 'rumput',     'nama_pakan' => 'Rumput Brachiaria',          'jumlah_stok' => 400,  'satuan' => 'kg', 'stok_minimum' => 80],
            ['jenis' => 'silase',     'nama_pakan' => 'Silase Jagung',              'jumlah_stok' => 500,  'satuan' => 'kg', 'stok_minimum' => 100],
            ['jenis' => 'silase',     'nama_pakan' => 'Silase Sorghum',             'jumlah_stok' => 300,  'satuan' => 'kg', 'stok_minimum' => 60],
            ['jenis' => 'ampas_tahu', 'nama_pakan' => 'Ampas Tahu Segar',           'jumlah_stok' => 200,  'satuan' => 'kg', 'stok_minimum' => 40],
            ['jenis' => 'ampas_tahu', 'nama_pakan' => 'Ampas Tahu Fermentasi',      'jumlah_stok' => 150,  'satuan' => 'kg', 'stok_minimum' => 30],
            ['jenis' => 'konsentrat', 'nama_pakan' => 'Dedak Padi Halus',           'jumlah_stok' => 450,  'satuan' => 'kg', 'stok_minimum' => 90],
            ['jenis' => 'konsentrat', 'nama_pakan' => 'Bungkil Kedelai',            'jumlah_stok' => 180,  'satuan' => 'kg', 'stok_minimum' => 35],
            ['jenis' => 'konsentrat', 'nama_pakan' => 'Molases (Tetes Tebu)',       'jumlah_stok' => 120,  'satuan' => 'kg', 'stok_minimum' => 25],
        ];

        $added = 0;
        foreach ($items as $item) {
            $exists = DB::table('pakan_stok')->where('nama_pakan', $item['nama_pakan'])->exists();
            if (! $exists) {
                DB::table('pakan_stok')->insert(array_merge($item, [
                    'tanggal_update' => Carbon::today(),
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]));
                $added++;
            }
        }

        $this->command->info("✅ PakanStokSeeder: {$added} item pakan stok ditambahkan.");
    }
}
