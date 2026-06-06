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
            // ── OBAT (7 item) ────────────────────────────────────────
            [
                'nama_obat' => 'Oxytetracycline HCl 10%',
                'tipe' => 'obat', 'satuan' => 'ml',
                'stok' => 200, 'stok_minimum' => 30,
                'harga_beli' => 15000,
                'tanggal_expired' => Carbon::now()->addMonths(18)->toDateString(),
                'interval_vaksinasi' => null,
                'keterangan' => 'Antibiotik spektrum luas untuk infeksi bakteri gram positif/negatif',
            ],
            [
                'nama_obat' => 'Amoxicillin 500mg',
                'tipe' => 'obat', 'satuan' => 'tablet',
                'stok' => 300, 'stok_minimum' => 50,
                'harga_beli' => 2500,
                'tanggal_expired' => Carbon::now()->addMonths(24)->toDateString(),
                'interval_vaksinasi' => null,
                'keterangan' => 'Antibiotik penisilin untuk infeksi saluran pernapasan dan kulit',
            ],
            [
                'nama_obat' => 'Ivermectin 1%',
                'tipe' => 'obat', 'satuan' => 'ml',
                'stok' => 150, 'stok_minimum' => 20,
                'harga_beli' => 25000,
                'tanggal_expired' => Carbon::now()->addMonths(12)->toDateString(),
                'interval_vaksinasi' => null,
                'keterangan' => 'Antiparasit internal dan eksternal (cacing, kutu, tungau)',
            ],
            [
                'nama_obat' => 'Metronidazole 250mg',
                'tipe' => 'obat', 'satuan' => 'tablet',
                'stok' => 200, 'stok_minimum' => 30,
                'harga_beli' => 1500,
                'tanggal_expired' => Carbon::now()->addMonths(20)->toDateString(),
                'interval_vaksinasi' => null,
                'keterangan' => 'Antiprotozoa untuk diare infektif dan infeksi anaerob',
            ],
            [
                'nama_obat' => 'Albendazole 250mg',
                'tipe' => 'obat', 'satuan' => 'tablet',
                'stok' => 180, 'stok_minimum' => 25,
                'harga_beli' => 2000,
                'tanggal_expired' => Carbon::now()->addMonths(18)->toDateString(),
                'interval_vaksinasi' => null,
                'keterangan' => 'Anthelmin spektrum luas untuk nematoda, cestoda, dan trematoda',
            ],
            [
                'nama_obat' => 'Sulfadimidine 33%',
                'tipe' => 'obat', 'satuan' => 'ml',
                'stok' => 100, 'stok_minimum' => 20,
                'harga_beli' => 12000,
                'tanggal_expired' => Carbon::now()->addMonths(15)->toDateString(),
                'interval_vaksinasi' => null,
                'keterangan' => 'Sulfonamida untuk koksidiosis dan infeksi saluran kemih',
            ],
            [
                'nama_obat' => 'Deksametason injeksi 0,1%',
                'tipe' => 'obat', 'satuan' => 'ml',
                'stok' => 80, 'stok_minimum' => 15,
                'harga_beli' => 20000,
                'tanggal_expired' => Carbon::now()->addMonths(10)->toDateString(),
                'interval_vaksinasi' => null,
                'keterangan' => 'Kortikosteroid anti-inflamasi untuk syok dan peradangan akut',
            ],

            // ── VAKSIN (8 item) ──────────────────────────────────────
            [
                'nama_obat' => 'Vaksin PPR (Peste des Petits Ruminants)',
                'tipe' => 'vaksin', 'satuan' => 'dosis',
                'stok' => 100, 'stok_minimum' => 20,
                'harga_beli' => 35000,
                'tanggal_expired' => Carbon::now()->addMonths(6)->toDateString(),
                'interval_vaksinasi' => 365,
                'keterangan' => 'Vaksin wajib PPR – diberikan sekali atau program tahunan',
            ],
            [
                'nama_obat' => 'Vaksin Anthrax Spore',
                'tipe' => 'vaksin', 'satuan' => 'dosis',
                'stok' => 50, 'stok_minimum' => 10,
                'harga_beli' => 45000,
                'tanggal_expired' => Carbon::now()->addMonths(8)->toDateString(),
                'interval_vaksinasi' => 365,
                'keterangan' => 'Vaksin Anthrax untuk wilayah endemis, program tahunan',
            ],
            [
                'nama_obat' => 'Vaksin Brucellosis Rev-1',
                'tipe' => 'vaksin', 'satuan' => 'dosis',
                'stok' => 40, 'stok_minimum' => 8,
                'harga_beli' => 55000,
                'tanggal_expired' => Carbon::now()->addMonths(4)->toDateString(),
                'interval_vaksinasi' => 365,
                'keterangan' => 'Vaksin brucellosis khusus indukan betina muda (3-6 bulan)',
            ],
            [
                'nama_obat' => 'Vaksin Pasteurellosis',
                'tipe' => 'vaksin', 'satuan' => 'dosis',
                'stok' => 60, 'stok_minimum' => 12,
                'harga_beli' => 30000,
                'tanggal_expired' => Carbon::now()->addMonths(9)->toDateString(),
                'interval_vaksinasi' => 180,
                'keterangan' => 'Mencegah pneumonia Mannheimia haemolytica, interval 6 bulan',
            ],
            [
                'nama_obat' => 'Vaksin Enterotoksemia (Pulpy Kidney)',
                'tipe' => 'vaksin', 'satuan' => 'dosis',
                'stok' => 75, 'stok_minimum' => 15,
                'harga_beli' => 28000,
                'tanggal_expired' => Carbon::now()->addMonths(10)->toDateString(),
                'interval_vaksinasi' => 180,
                'keterangan' => 'Clostridium perfringens tipe D – cempe dan indukan bunting',
            ],
            [
                'nama_obat' => 'Vaksin FMD (Foot and Mouth Disease)',
                'tipe' => 'vaksin', 'satuan' => 'dosis',
                'stok' => 80, 'stok_minimum' => 16,
                'harga_beli' => 40000,
                'tanggal_expired' => Carbon::now()->addMonths(5)->toDateString(),
                'interval_vaksinasi' => 180,
                'keterangan' => 'Vaksin PMK program nasional – wajib 2x/tahun',
            ],
            [
                'nama_obat' => 'Vaksin Tetanus Toksoid',
                'tipe' => 'vaksin', 'satuan' => 'dosis',
                'stok' => 45, 'stok_minimum' => 10,
                'harga_beli' => 32000,
                'tanggal_expired' => Carbon::now()->addMonths(7)->toDateString(),
                'interval_vaksinasi' => 365,
                'keterangan' => 'Pencegahan tetanus pasca luka terbuka, kastrasi, dan kelahiran',
            ],
            [
                'nama_obat' => 'Vaksin Orf (Scabby Mouth)',
                'tipe' => 'vaksin', 'satuan' => 'dosis',
                'stok' => 35, 'stok_minimum' => 8,
                'harga_beli' => 38000,
                'tanggal_expired' => Carbon::now()->addMonths(6)->toDateString(),
                'interval_vaksinasi' => 365,
                'keterangan' => 'Vaksin orf parapox untuk cempe usia 1-2 bulan pertama',
            ],

            // ── VITAMIN (5 item) ─────────────────────────────────────
            [
                'nama_obat' => 'Vitamin B Complex injeksi',
                'tipe' => 'vitamin', 'satuan' => 'ml',
                'stok' => 120, 'stok_minimum' => 20,
                'harga_beli' => 18000,
                'tanggal_expired' => Carbon::now()->addMonths(14)->toDateString(),
                'interval_vaksinasi' => null,
                'keterangan' => 'Suplemen metabolisme energi, saraf, dan nafsu makan',
            ],
            [
                'nama_obat' => 'Vitamin AD3E injeksi',
                'tipe' => 'vitamin', 'satuan' => 'ml',
                'stok' => 90, 'stok_minimum' => 15,
                'harga_beli' => 22000,
                'tanggal_expired' => Carbon::now()->addMonths(16)->toDateString(),
                'interval_vaksinasi' => null,
                'keterangan' => 'Vitamin liposoluble untuk pertumbuhan, reproduksi, dan imunitas',
            ],
            [
                'nama_obat' => 'Mineral Mix Ruminan',
                'tipe' => 'vitamin', 'satuan' => 'tablet',
                'stok' => 250, 'stok_minimum' => 40,
                'harga_beli' => 1200,
                'tanggal_expired' => Carbon::now()->addMonths(24)->toDateString(),
                'interval_vaksinasi' => null,
                'keterangan' => 'Mineral makro & mikro (Ca, P, Mg, Zn, Cu, Se) untuk ruminansia',
            ],
            [
                'nama_obat' => 'Kalsium Glukonat 10% injeksi',
                'tipe' => 'vitamin', 'satuan' => 'ml',
                'stok' => 60, 'stok_minimum' => 12,
                'harga_beli' => 15000,
                'tanggal_expired' => Carbon::now()->addMonths(12)->toDateString(),
                'interval_vaksinasi' => null,
                'keterangan' => 'Suplemen kalsium untuk indukan bunting/laktasi dan milk fever',
            ],
            [
                'nama_obat' => 'Zinc Sulfate 50mg',
                'tipe' => 'vitamin', 'satuan' => 'tablet',
                'stok' => 150, 'stok_minimum' => 25,
                'harga_beli' => 1800,
                'tanggal_expired' => Carbon::now()->addMonths(20)->toDateString(),
                'interval_vaksinasi' => null,
                'keterangan' => 'Suplemen zinc untuk imunitas, kesehatan kuku, dan kulit',
            ],
        ];

        foreach ($items as $item) {
            DB::table('obat_vaksin')->insert(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        $this->command->info('✅ ObatVaksinSeeder: ' . count($items) . ' item (7 obat, 8 vaksin, 5 vitamin).');
    }
}
