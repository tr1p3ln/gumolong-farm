<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObatVaksinSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('obat_vaksin')->count() > 0) {
            $this->command->warn('ObatVaksinSeeder: Data sudah ada, skip.');
            return;
        }

        $now = Carbon::now();

        $items = [
            // ── OBAT ─────────────────────────────────────────────────
            [
                'nama_obat' => 'Oxytetracycline HCl 10%',
                'tipe' => 'obat',
                'satuan' => 'ml',
                'stok' => 200,
                'stok_minimum' => 30,
                'tanggal_expired' => Carbon::now()->addMonths(18)->toDateString(),
            ],
            [
                'nama_obat' => 'Amoxicillin 500mg',
                'tipe' => 'obat',
                'satuan' => 'tablet',
                'stok' => 300,
                'stok_minimum' => 50,
                'tanggal_expired' => Carbon::now()->addMonths(24)->toDateString(),
            ],
            [
                'nama_obat' => 'Ivermectin 1%',
                'tipe' => 'obat',
                'satuan' => 'ml',
                'stok' => 150,
                'stok_minimum' => 20,
                'tanggal_expired' => Carbon::now()->addMonths(12)->toDateString(),
            ],
            [
                'nama_obat' => 'Metronidazole 250mg',
                'tipe' => 'obat',
                'satuan' => 'tablet',
                'stok' => 200,
                'stok_minimum' => 30,
                'tanggal_expired' => Carbon::now()->addMonths(20)->toDateString(),
            ],
            [
                'nama_obat' => 'Albendazole 250mg',
                'tipe' => 'obat',
                'satuan' => 'tablet',
                'stok' => 180,
                'stok_minimum' => 25,
                'tanggal_expired' => Carbon::now()->addMonths(18)->toDateString(),
            ],
            [
                'nama_obat' => 'Sulfadimidine 33%',
                'tipe' => 'obat',
                'satuan' => 'ml',
                'stok' => 100,
                'stok_minimum' => 20,
                'tanggal_expired' => Carbon::now()->addMonths(15)->toDateString(),
            ],
            [
                'nama_obat' => 'Deksametason injeksi 0,1%',
                'tipe' => 'obat',
                'satuan' => 'ml',
                'stok' => 80,
                'stok_minimum' => 15,
                'tanggal_expired' => Carbon::now()->addMonths(10)->toDateString(),
            ],

            // ── VAKSIN ───────────────────────────────────────────────
            [
                'nama_obat' => 'Vaksin PPR (Peste des Petits Ruminants)',
                'tipe' => 'vaksin',
                'satuan' => 'dosis',
                'stok' => 100,
                'stok_minimum' => 20,
                'tanggal_expired' => Carbon::now()->addMonths(6)->toDateString(),
            ],
            [
                'nama_obat' => 'Vaksin Anthrax Spore',
                'tipe' => 'vaksin',
                'satuan' => 'dosis',
                'stok' => 50,
                'stok_minimum' => 10,
                'tanggal_expired' => Carbon::now()->addMonths(8)->toDateString(),
            ],
            [
                'nama_obat' => 'Vaksin Brucellosis Rev-1',
                'tipe' => 'vaksin',
                'satuan' => 'dosis',
                'stok' => 40,
                'stok_minimum' => 8,
                'tanggal_expired' => Carbon::now()->addMonths(4)->toDateString(),
            ],
            [
                'nama_obat' => 'Vaksin Pasteurellosis',
                'tipe' => 'vaksin',
                'satuan' => 'dosis',
                'stok' => 60,
                'stok_minimum' => 12,
                'tanggal_expired' => Carbon::now()->addMonths(9)->toDateString(),
            ],
            [
                'nama_obat' => 'Vaksin Enterotoksemia (Pulpy Kidney)',
                'tipe' => 'vaksin',
                'satuan' => 'dosis',
                'stok' => 75,
                'stok_minimum' => 15,
                'tanggal_expired' => Carbon::now()->addMonths(10)->toDateString(),
            ],
            [
                'nama_obat' => 'Vaksin FMD (Foot and Mouth Disease)',
                'tipe' => 'vaksin',
                'satuan' => 'dosis',
                'stok' => 80,
                'stok_minimum' => 16,
                'tanggal_expired' => Carbon::now()->addMonths(5)->toDateString(),
            ],
            [
                'nama_obat' => 'Vaksin Tetanus Toksoid',
                'tipe' => 'vaksin',
                'satuan' => 'dosis',
                'stok' => 45,
                'stok_minimum' => 10,
                'tanggal_expired' => Carbon::now()->addMonths(7)->toDateString(),
            ],
            [
                'nama_obat' => 'Vaksin Orf (Scabby Mouth)',
                'tipe' => 'vaksin',
                'satuan' => 'dosis',
                'stok' => 35,
                'stok_minimum' => 8,
                'tanggal_expired' => Carbon::now()->addMonths(6)->toDateString(),
            ],
        ];

        foreach ($items as $item) {
            DB::table('obat_vaksin')->insert(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        $this->command->info('ObatVaksinSeeder: ' . count($items) . ' item berhasil ditambahkan (7 obat, 8 vaksin).');
    }
}
