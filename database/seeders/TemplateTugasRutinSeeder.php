<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateTugasRutinSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('template_tugas_rutin')->count() > 0) {
            $this->command->warn('TemplateTugasRutinSeeder: Data sudah ada, skip.');
            return;
        }

        $kandangIds = DB::table('kandang')->pluck('kandang_id')->toArray();
        $pkId       = DB::table('user')->where('role', 'pengurus_kandang')->value('user_id');

        if (empty($kandangIds)) {
            $this->command->error('Tidak ada kandang. Jalankan DatabaseSeeder dulu.');
            return;
        }

        $now = Carbon::now();

        $templates = [
            ['judul' => 'Bersih kandang pagi',              'deskripsi' => 'Sapu dan buang kotoran dari seluruh petak kandang',                       'waktu' => '06:00:00', 'prioritas' => 'tinggi',  'aktif' => true],
            ['judul' => 'Pakan pagi (rumput segar)',         'deskripsi' => 'Berikan 2 kg rumput segar per ekor ternak dewasa',                        'waktu' => '07:00:00', 'prioritas' => 'tinggi',  'aktif' => true],
            ['judul' => 'Pakan sore (konsentrat)',           'deskripsi' => 'Berikan 300 g konsentrat + 200 g ampas tahu per ekor',                    'waktu' => '15:00:00', 'prioritas' => 'tinggi',  'aktif' => true],
            ['judul' => 'Observasi visual ternak',           'deskripsi' => 'Catat tanda penyakit: nafsu makan, postur, feses, suhu',                  'waktu' => '08:00:00', 'prioritas' => 'tinggi',  'aktif' => true],
            ['judul' => 'Isi dan bersihkan air minum',       'deskripsi' => 'Kuras, bilas, dan isi ulang seluruh tempat minum',                        'waktu' => '06:30:00', 'prioritas' => 'sedang',  'aktif' => true],
            ['judul' => 'Sanitasi peralatan kandang',        'deskripsi' => 'Cuci ember, papan makan, selang dengan sabun atau disinfektan',           'waktu' => '09:00:00', 'prioritas' => 'sedang',  'aktif' => true],
            ['judul' => 'Timbang cempe mingguan',            'deskripsi' => 'Timbang semua cempe, catat ke sistem, hitung ADG',                        'waktu' => '10:00:00', 'prioritas' => 'sedang',  'aktif' => true],
            ['judul' => 'Cek stok pakan harian',             'deskripsi' => 'Catat sisa stok rumput dan konsentrat di gudang',                         'waktu' => '16:00:00', 'prioritas' => 'sedang',  'aktif' => true],
            ['judul' => 'Laporan harian ke kepala kandang',  'deskripsi' => 'Kirim rekap kondisi ternak, insiden, dan tugas selesai',                  'waktu' => '17:00:00', 'prioritas' => 'sedang',  'aktif' => true],
            ['judul' => 'Bersih kandang sore',               'deskripsi' => 'Bersihkan kotoran sore hari sebelum ternak beristirahat malam',           'waktu' => '16:30:00', 'prioritas' => 'sedang',  'aktif' => true],
            ['judul' => 'Cek keamanan kandang malam',        'deskripsi' => 'Pastikan pintu terkunci, tidak ada ternak lepas, lampu aktif',            'waktu' => '18:00:00', 'prioritas' => 'rendah',  'aktif' => true],
            ['judul' => 'Timbang indukan bulanan',           'deskripsi' => 'Timbang seluruh indukan aktif, catat tren berat badan',                   'waktu' => '10:00:00', 'prioritas' => 'sedang',  'aktif' => true],
            ['judul' => 'Observasi kandang kawin',           'deskripsi' => 'Catat perilaku perkawinan dan pasangan yang teridentifikasi',             'waktu' => '09:30:00', 'prioritas' => 'sedang',  'aktif' => true],
            ['judul' => 'Desinfeksi mingguan kandang',       'deskripsi' => 'Semprot desinfektan ke lantai, dinding, dan perlengkapan kandang',         'waktu' => '11:00:00', 'prioritas' => 'sedang',  'aktif' => true],
            ['judul' => 'Update rekam medis ternak sakit',   'deskripsi' => 'Perbarui status dan perkembangan ternak sakit di sistem',                 'waktu' => '12:00:00', 'prioritas' => 'tinggi',  'aktif' => true],
            ['judul' => 'Pemberian silase sore (suplemen)',  'deskripsi' => 'Berikan 400 g silase jagung sebagai suplemen serat untuk ternak dewasa',  'waktu' => '15:30:00', 'prioritas' => 'rendah',  'aktif' => false],
        ];

        $records = [];
        foreach ($templates as $i => $tmpl) {
            $records[] = [
                'judul'        => $tmpl['judul'],
                'deskripsi'    => $tmpl['deskripsi'],
                'kandang_id'   => $kandangIds[$i % count($kandangIds)],
                'user_id'      => $pkId,
                'waktu_default' => $tmpl['waktu'],
                'prioritas'    => $tmpl['prioritas'],
                'is_active'    => $tmpl['aktif'],
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        DB::table('template_tugas_rutin')->insert($records);

        $this->command->info('✅ TemplateTugasRutinSeeder: ' . count($records) . ' template (15 aktif, 1 nonaktif).');
    }
}
