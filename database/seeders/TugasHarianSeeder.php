<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TugasHarianSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('tugas_harian')->count() > 0) {
            $this->command->warn('TugasHarianSeeder: Data sudah ada, skip.');
            return;
        }

        $kandangIds = DB::table('kandang')->pluck('kandang_id')->toArray();
        $pkId       = DB::table('user')->where('role', 'pengurus_kandang')->value('user_id');
        $kkId       = DB::table('user')->where('role', 'kepala_kandang')->value('user_id');

        if (empty($kandangIds)) {
            $this->command->error('Tidak ada kandang. Jalankan DatabaseSeeder dulu.');
            return;
        }

        $now = Carbon::now();

        $daftarTugas = [
            // Tugas rutin harian
            ['judul' => 'Pembersihan kandang pagi',             'deskripsi' => 'Sapu dan buang kotoran, ganti alas kandang',                            'prioritas' => 'tinggi', 'tipe' => 'rutin'],
            ['judul' => 'Pemberian pakan pagi (rumput)',        'deskripsi' => 'Berikan rumput segar 2 kg/ekor untuk semua ternak',                     'prioritas' => 'tinggi', 'tipe' => 'rutin'],
            ['judul' => 'Pemberian pakan sore (konsentrat)',    'deskripsi' => 'Berikan 300 g konsentrat + 200 g ampas tahu/ekor',                      'prioritas' => 'tinggi', 'tipe' => 'rutin'],
            ['judul' => 'Pengecekan kesehatan harian',          'deskripsi' => 'Observasi visual semua ternak, catat yang tampak sakit',                'prioritas' => 'tinggi', 'tipe' => 'rutin'],
            ['judul' => 'Pengisian dan pembersihan air minum',  'deskripsi' => 'Cuci dan isi ulang seluruh tempat minum kandang',                       'prioritas' => 'sedang', 'tipe' => 'rutin'],
            ['judul' => 'Sanitasi peralatan pakan',             'deskripsi' => 'Cuci ember, bak pakan, dan selang air dengan disinfektan',              'prioritas' => 'sedang', 'tipe' => 'rutin'],
            ['judul' => 'Penimbangan mingguan cempe',           'deskripsi' => 'Timbang semua cempe dan catat berat ke sistem',                         'prioritas' => 'sedang', 'tipe' => 'rutin'],
            ['judul' => 'Pengecekan stok pakan harian',         'deskripsi' => 'Hitung dan catat sisa stok rumput dan konsentrat di gudang',           'prioritas' => 'sedang', 'tipe' => 'rutin'],
            ['judul' => 'Laporan harian ke kepala kandang',     'deskripsi' => 'Kirim rekap kondisi ternak dan tugas selesai via sistem',              'prioritas' => 'sedang', 'tipe' => 'rutin'],
            ['judul' => 'Pembersihan kandang sore',             'deskripsi' => 'Bersihkan kotoran sore hari sebelum ternak istirahat malam',           'prioritas' => 'sedang', 'tipe' => 'rutin'],
            // Tugas kondisional
            ['judul' => 'Vaksinasi PPR batch pejantan',         'deskripsi' => 'Vaksinasi PPR untuk P-001 s/d P-010, siapkan vaksin dan jarum',        'prioritas' => 'tinggi', 'tipe' => 'kondisional'],
            ['judul' => 'Pengobatan domba I-015 (scabies)',     'deskripsi' => 'Oleskan Ivermectin topikal dan semprot kandang isolasi',                'prioritas' => 'tinggi', 'tipe' => 'kondisional'],
            ['judul' => 'Pindah indukan bunting ke kandang persalinan', 'deskripsi' => 'Pindahkan I-008, I-012, I-020 yang HPL <7 hari',              'prioritas' => 'tinggi', 'tipe' => 'kondisional'],
            ['judul' => 'Perbaikan pagar kandang B',            'deskripsi' => 'Ganti kawat berduri rusak di sudut barat kandang B',                   'prioritas' => 'sedang', 'tipe' => 'kondisional'],
            ['judul' => 'Desinfeksi menyeluruh kandang isolasi','deskripsi' => 'Semprot Virkon-S seluruh kandang isolasi pasca kasus PMK',             'prioritas' => 'tinggi', 'tipe' => 'kondisional'],
            ['judul' => 'Pencatatan data perkawinan bulan ini', 'deskripsi' => 'Update rekap perkawinan, estimasi lahir, dan status kebuntingan',       'prioritas' => 'sedang', 'tipe' => 'kondisional'],
            ['judul' => 'Pemotongan kuku pejantan aktif',       'deskripsi' => 'Potong dan bersihkan kuku seluruh pejantan, cek tanda foot rot',        'prioritas' => 'rendah', 'tipe' => 'kondisional'],
            ['judul' => 'Pemantauan intensif indukan HPL dekat','deskripsi' => 'Pantau 2 jam sekali indukan yang estimasi lahir 3 hari lagi',          'prioritas' => 'tinggi', 'tipe' => 'kondisional'],
            ['judul' => 'Pembuatan dan pengisian silase jagung', 'deskripsi' => 'Proses 300 kg jagung menjadi silase, masukkan ke silo plastik',        'prioritas' => 'sedang', 'tipe' => 'kondisional'],
            ['judul' => 'Pemberian vitamin B pada cempe lemas', 'deskripsi' => 'Injeksi IM Vitamin B Complex 2 ml untuk A-003, A-007, A-011',          'prioritas' => 'sedang', 'tipe' => 'kondisional'],
        ];

        $statuses = ['selesai', 'selesai', 'selesai', 'dalam_proses', 'belum', 'dilewati'];
        $records  = [];

        foreach ($daftarTugas as $i => $tugas) {
            $kandangId = $kandangIds[$i % count($kandangIds)];
            $tanggal   = Carbon::now()->subDays(rand(0, 10));
            $status    = $statuses[$i % count($statuses)];

            $records[] = [
                'judul'                => $tugas['judul'],
                'deskripsi'            => $tugas['deskripsi'],
                'kandang_id'           => $kandangId,
                'user_id'              => $pkId,
                'assigned_by'          => $kkId,
                'tanggal'              => $tanggal->toDateString(),
                'tipe'                 => $tugas['tipe'],
                'prioritas'            => $tugas['prioritas'],
                'status'               => $status,
                'waktu_mulai'          => in_array($status, ['selesai', 'dalam_proses']) ? '07:00:00' : null,
                'waktu_selesai'        => $status === 'selesai' ? '09:00:00' : null,
                'catatan_penyelesaian' => $status === 'selesai' ? 'Selesai dikerjakan sesuai prosedur standar' : null,
                'created_at'           => $now,
                'updated_at'           => $now,
            ];
        }

        DB::table('tugas_harian')->insert($records);

        $this->command->info('✅ TugasHarianSeeder: ' . count($records) . ' tugas harian (10 rutin + 10 kondisional).');
    }
}
