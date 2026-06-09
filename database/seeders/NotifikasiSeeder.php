<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotifikasiSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('notifikasi')->count() > 0) {
            $this->command->warn('NotifikasiSeeder: Data sudah ada, skip.');
            return;
        }

        $userIds = DB::table('user')
            ->whereIn('role', ['super_admin', 'admin', 'kepala_kandang'])
            ->pluck('user_id')
            ->toArray();

        $dombaTags = DB::table('domba')
            ->where('status', 'aktif')
            ->orderBy('ear_tag_id')
            ->limit(10)
            ->pluck('ear_tag_id')
            ->toArray();

        if (empty($userIds)) {
            $this->command->error('Tidak ada user. Jalankan DatabaseSeeder dulu.');
            return;
        }

        $now = Carbon::now();

        // HPL dinamis
        $hpl1 = Carbon::now()->addDays(3)->format('d M Y');
        $hpl2 = Carbon::now()->addDays(7)->format('d M Y');
        $hpl3 = Carbon::now()->addDays(1)->format('d M Y');

        $notifData = [
            // ── stok_menipis (5) ──────────────────────────────────────
            ['tipe' => 'stok_menipis', 'pesan' => 'Stok Konsentrat Premium hampir habis (sisa 45 kg, minimum 100 kg). Segera lakukan pemesanan.',         'tag' => null, 'dibaca' => false],
            ['tipe' => 'stok_menipis', 'pesan' => 'Stok Rumput Gajah menipis (sisa 175 kg, minimum 200 kg). Cari pasokan sebelum kehabisan.',             'tag' => null, 'dibaca' => false],
            ['tipe' => 'stok_menipis', 'pesan' => 'Stok Ivermectin 1% kritis (sisa 18 ml, minimum 20 ml). Hubungi supplier obat hewan segera.',          'tag' => null, 'dibaca' => true],
            ['tipe' => 'stok_menipis', 'pesan' => 'Stok Amoxicillin 500mg menipis (sisa 28 tablet, minimum 50 tablet). Segera restok.',                  'tag' => null, 'dibaca' => false],
            ['tipe' => 'stok_menipis', 'pesan' => 'Stok Ampas Tahu Segar menipis (sisa 32 kg, minimum 40 kg). Hubungi produsen tofu lokal.',             'tag' => null, 'dibaca' => true],

            // ── expired (5) ───────────────────────────────────────────
            ['tipe' => 'expired', 'pesan' => 'Vaksin Brucellosis Rev-1 akan kadaluarsa dalam 7 hari. Segera gunakan atau musnahkan sesuai prosedur.',    'tag' => null, 'dibaca' => false],
            ['tipe' => 'expired', 'pesan' => 'Vaksin FMD batch bulan ini akan kadaluarsa dalam 14 hari. Prioritaskan vaksinasi segera.',                 'tag' => null, 'dibaca' => false],
            ['tipe' => 'expired', 'pesan' => 'Deksametason injeksi 0,1% kadaluarsa bulan depan. Rotasi stok FIFO dan cek penggunaan.',                  'tag' => null, 'dibaca' => true],
            ['tipe' => 'expired', 'pesan' => 'Vaksin Orf (Scabby Mouth) mendekati kadaluarsa (10 hari). Rencanakan vaksinasi cempe segera.',            'tag' => null, 'dibaca' => false],
            ['tipe' => 'expired', 'pesan' => 'Vaksin PPR akan expired 21 hari lagi. Masukkan ke jadwal vaksinasi batch berikutnya.',                    'tag' => null, 'dibaca' => true],

            // ── hpl (5) ───────────────────────────────────────────────
            ['tipe' => 'hpl', 'pesan' => "I-005 diperkirakan melahirkan {$hpl1} (HPL 3 hari lagi). Siapkan kandang persalinan dan perlengkapan.",       'tag' => $dombaTags[0] ?? null, 'dibaca' => false],
            ['tipe' => 'hpl', 'pesan' => "I-012 mendekati HPL {$hpl2} (7 hari lagi). Pindahkan ke kandang persalinan hari ini.",                       'tag' => $dombaTags[1] ?? null, 'dibaca' => false],
            ['tipe' => 'hpl', 'pesan' => "I-018 memasuki masa kritis HPL {$hpl3} (besok). Pantau setiap 2 jam, siagakan petugas.",                     'tag' => $dombaTags[2] ?? null, 'dibaca' => true],
            ['tipe' => 'hpl', 'pesan' => 'I-023 telah melewati estimasi lahir 2 hari. Segera hubungi dokter hewan untuk penanganan.',                   'tag' => $dombaTags[3] ?? null, 'dibaca' => false],
            ['tipe' => 'hpl', 'pesan' => 'I-031 HPL besok. Siapkan: tali, sarung tangan steril, yodium, suntikan oksitosin.',                          'tag' => $dombaTags[4] ?? null, 'dibaca' => false],

            // ── vaksin (5) ────────────────────────────────────────────
            ['tipe' => 'vaksin', 'pesan' => 'Jadwal ulang vaksinasi PPR untuk I-003 sudah jatuh tempo hari ini. Segera dijadwalkan ulang.',             'tag' => $dombaTags[0] ?? null, 'dibaca' => false],
            ['tipe' => 'vaksin', 'pesan' => 'P-007 belum menerima vaksinasi Enterotoksemia (terakhir 183 hari lalu, interval 180 hari).',               'tag' => $dombaTags[1] ?? null, 'dibaca' => false],
            ['tipe' => 'vaksin', 'pesan' => 'Kelompok dara R-001 s/d R-010 jadwal vaksinasi Pasteurellosis bulan ini. Koordinasikan dengan drh.',      'tag' => null,                   'dibaca' => true],
            ['tipe' => 'vaksin', 'pesan' => 'A-005 dan A-006 (cempe 2 bulan) waktunya vaksinasi Orf perdana. Dosis: 1 dosis IM.',                      'tag' => $dombaTags[5] ?? null, 'dibaca' => false],
            ['tipe' => 'vaksin', 'pesan' => 'Vaksinasi FMD massal untuk seluruh kandang dijadwalkan minggu ini sesuai program pemerintah.',             'tag' => null,                   'dibaca' => true],

            // ── adg_rendah (5) ────────────────────────────────────────
            ['tipe' => 'adg_rendah', 'pesan' => 'ADG I-015 sangat rendah (58 g/hari, target ≥120 g/hari). Periksa kondisi kesehatan dan pola pakan.', 'tag' => $dombaTags[0] ?? null, 'dibaca' => false],
            ['tipe' => 'adg_rendah', 'pesan' => 'ADG P-003 di bawah rata-rata pejantan (75 g/hari). Evaluasi rasio pakan dan kondisi kandang.',        'tag' => $dombaTags[1] ?? null, 'dibaca' => false],
            ['tipe' => 'adg_rendah', 'pesan' => 'ADG R-009 turun 3 bulan berturut-turut. Kemungkinan parasit internal — jadwalkan pemeriksaan feses.', 'tag' => $dombaTags[2] ?? null, 'dibaca' => true],
            ['tipe' => 'adg_rendah', 'pesan' => 'Rata-rata ADG Kandang B lebih rendah 30% dari Kandang A. Evaluasi manajemen pakan dan kepadatan.',    'tag' => null,                   'dibaca' => false],
            ['tipe' => 'adg_rendah', 'pesan' => 'A-003 (cempe 3 bulan) ADG hanya 45 g/hari — sangat rendah. Perhatian segera diperlukan.',             'tag' => $dombaTags[3] ?? null, 'dibaca' => false],
        ];

        $records = [];
        foreach ($notifData as $i => $notif) {
            $records[] = [
                'user_id'            => $userIds[$i % count($userIds)],
                'ear_tag_id'         => $notif['tag'],
                'tipe'               => $notif['tipe'],
                'pesan'              => $notif['pesan'],
                'sudah_dibaca'       => $notif['dibaca'],
                'tanggal_notifikasi' => Carbon::now()->subHours(rand(1, 72)),
            ];
        }

        DB::table('notifikasi')->insert($records);

        $this->command->info('✅ NotifikasiSeeder: ' . count($records) . ' notifikasi (stok_menipis×5, expired×5, hpl×5, vaksin×5, adg_rendah×5).');
    }
}
