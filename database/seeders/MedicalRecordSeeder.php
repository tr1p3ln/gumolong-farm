<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicalRecordSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('medical_record')->count() > 0) {
            $this->command->warn('MedicalRecordSeeder: Data sudah ada, skip.');
            return;
        }

        $dombaTags = DB::table('domba')
            ->where('status', 'aktif')
            ->pluck('ear_tag_id')
            ->toArray();

        $obatIds = DB::table('obat_vaksin')
            ->where('tipe', 'obat')
            ->pluck('obat_id')
            ->toArray();

        if (empty($dombaTags)) {
            $this->command->error('Tidak ada domba. Jalankan DombaSeeder dulu.');
            return;
        }

        if (empty($obatIds)) {
            $this->command->error('Tidak ada obat. Jalankan ObatVaksinSeeder dulu.');
            return;
        }

        $now = Carbon::now();

        $kasus = [
            ['gejala' => 'Nafsu makan menurun drastis, perut kembung, gelisah, tidak mau berdiri',    'diagnosa' => 'Bloat / Tympani Rumen',                   'status' => 'sembuh'],
            ['gejala' => 'Gatal-gatal hebat, keropeng tebal di leher dan muka, bulu rontok',           'diagnosa' => 'Scabies (Kudis)',                          'status' => 'sembuh'],
            ['gejala' => 'Tubuh kurus, feses berlendir, selaput mata pucat',                           'diagnosa' => 'Cacingan (Haemonchosis)',                   'status' => 'sembuh'],
            ['gejala' => 'Diare cair kekuningan, lemas, dehidrasi, mata cekung',                       'diagnosa' => 'Diare Infektif (E. coli)',                  'status' => 'sembuh'],
            ['gejala' => 'Suhu tubuh 40,5°C, tidak mau makan, napas cepat',                           'diagnosa' => 'Demam Akut – Pasteurellosis',               'status' => 'sembuh'],
            ['gejala' => 'Mata merah, berair, fotofobia, kornea keruh',                                'diagnosa' => 'Pink Eye (Keratokonjungtivitis Infectiosa)', 'status' => 'sembuh'],
            ['gejala' => 'Ambing bengkak, panas, susu kekuningan bernanah',                            'diagnosa' => 'Mastitis Akut',                            'status' => 'sembuh'],
            ['gejala' => 'Pincang kaki belakang, kuku merah bengkak berbau busuk',                     'diagnosa' => 'Foot Rot (Busuk Kuku)',                    'status' => 'sembuh'],
            ['gejala' => 'Sariawan mulut, lepuh pada kaki, air liur berlebih',                         'diagnosa' => 'PMK (Penyakit Mulut dan Kuku)',             'status' => 'dalam_perawatan'],
            ['gejala' => 'Batuk produktif, sesak napas, sekret hidung purulent',                       'diagnosa' => 'Pneumonia Bakterial',                      'status' => 'dalam_perawatan'],
            ['gejala' => 'Berputar-putar, kehilangan keseimbangan, gejala saraf progresif',            'diagnosa' => 'Gid / Sturdy (Coenurosis)',                 'status' => 'dalam_perawatan'],
            ['gejala' => 'Luka bernanah besar di kaki belakang, tidak mau disentuh',                   'diagnosa' => 'Abses Subkutan',                           'status' => 'sembuh'],
            ['gejala' => 'Produksi susu turun 70%, indukan lemas, bergetar pasca melahirkan',          'diagnosa' => 'Hipokalsemia (Milk Fever)',                 'status' => 'sembuh'],
            ['gejala' => 'Kelenjar limfe leher membesar berisi nanah, tidak demam',                    'diagnosa' => 'Caseous Lymphadenitis (CLA)',              'status' => 'dalam_perawatan'],
            ['gejala' => 'Bulu kusam, anemia berat, lemas ekstrem, feses gelap',                       'diagnosa' => 'Anemia Hemolitik – Haemonchosis Kronis',   'status' => 'sembuh'],
            ['gejala' => 'Cempe gemetar, kejang, gigi gemeretak, tidak mau menyusu',                   'diagnosa' => 'Hipoglikemia Neonatal',                    'status' => 'sembuh'],
            ['gejala' => 'Lecet merah tebal di bibir dan hidung, berlendir',                           'diagnosa' => 'Orf (Scabby Mouth / Contagious Ecthyma)',  'status' => 'sembuh'],
            ['gejala' => 'Anak domba tidak bisa berdiri, sendi bengkak, demam tinggi',                 'diagnosa' => 'Polyarthritis Neonatal (Joint Ill)',       'status' => 'mati'],
            ['gejala' => 'Perut keras, tidak bisa kentut, gelisah mengguling-guling',                  'diagnosa' => 'Impaksi Rumen',                            'status' => 'sembuh'],
            ['gejala' => 'Kurus progresif, diare kronis 3 minggu, tidak respons pengobatan',           'diagnosa' => 'Paratuberkulosis (Johne\'s Disease)',       'status' => 'mati'],
        ];

        shuffle($dombaTags);
        $medRecords   = [];
        $pemakaianObat = [];

        foreach ($kasus as $i => $kasus_item) {
            $tag          = $dombaTags[$i % count($dombaTags)];
            $tanggalSakit = Carbon::now()->subDays(rand(10, 180));
            $tanggalSembuh = in_array($kasus_item['status'], ['sembuh', 'mati'])
                ? $tanggalSakit->copy()->addDays(rand(3, 14))->toDateString()
                : null;

            $medRecords[] = [
                'ear_tag_id'     => $tag,
                'tanggal_sakit'  => $tanggalSakit->toDateString(),
                'gejala'         => $kasus_item['gejala'],
                'diagnosa'       => $kasus_item['diagnosa'],
                'tanggal_sembuh' => $tanggalSembuh,
                'status'         => $kasus_item['status'],
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        DB::table('medical_record')->insert($medRecords);

        // Ambil rekam_id yang baru dibuat
        $rekamIds = DB::table('medical_record')
            ->orderBy('rekam_id', 'desc')
            ->limit(count($medRecords))
            ->pluck('rekam_id')
            ->toArray();

        $caraPemberian = ['oral', 'injeksi IM', 'injeksi SC', 'topikal', 'intravena'];

        foreach ($rekamIds as $idx => $rekamId) {
            // 1-2 pemakaian obat per rekam medis
            $jumlahObat = rand(1, 2);
            $obatSample = array_rand(array_flip($obatIds), min($jumlahObat, count($obatIds)));
            $obatSample = is_array($obatSample) ? $obatSample : [$obatSample];

            foreach ($obatSample as $obatId) {
                $pemakaianObat[] = [
                    'rekam_id'       => $rekamId,
                    'obat_id'        => $obatId,
                    'jumlah'         => rand(1, 5),
                    'tanggal_pakai'  => Carbon::now()->subDays(rand(1, 10))->toDateString(),
                    'cara_pemberian' => $caraPemberian[array_rand($caraPemberian)],
                    'catatan'        => null,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }
        }

        if (! empty($pemakaianObat)) {
            DB::table('pemakaian_obat')->insert($pemakaianObat);
        }

        $this->command->info('✅ MedicalRecordSeeder: ' . count($medRecords) . ' rekam medis + ' . count($pemakaianObat) . ' pemakaian obat.');
    }
}
