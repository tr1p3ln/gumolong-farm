<?php

namespace Tests\Feature;

use App\Models\Domba;
use App\Models\Kandang;
use App\Models\ObatVaksin;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class KesehatanTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private Domba $domba;
    private Kandang $kandang;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin   = User::factory()->admin()->create();
        $this->kandang = Kandang::factory()->create();
        $this->domba   = Domba::factory()->create([
            'kandang_id'    => $this->kandang->kandang_id,
            'status'        => 'aktif',
        ]);
    }

    // ─── INDEX ───────────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_halaman_kesehatan(): void
    {
        $this->actingAs($this->admin)
             ->get(route('kesehatan.index'))
             ->assertOk();
    }

    public function test_tamu_tidak_dapat_mengakses_halaman_kesehatan(): void
    {
        $this->get(route('kesehatan.index'))->assertRedirect('/login');
    }

    // ─── STORE REKAM MEDIS ───────────────────────────────────────────────────

    public function test_admin_dapat_membuat_rekam_medis(): void
    {
        $this->actingAs($this->admin)
             ->post(route('kesehatan.store'), [
                 'ear_tag_id'    => $this->domba->ear_tag_id,
                 'tanggal_sakit' => today()->toDateString(),
                 'status'        => 'sakit',
                 'gejala'        => 'Demam tinggi dan nafsu makan turun',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('medical_record', [
            'ear_tag_id' => $this->domba->ear_tag_id,
            'status'     => 'sakit',
        ]);
    }

    public function test_store_rekam_medis_gagal_jika_field_wajib_kosong(): void
    {
        $this->actingAs($this->admin)
             ->post(route('kesehatan.store'), [])
             ->assertSessionHasErrors(['ear_tag_id', 'tanggal_sakit', 'status', 'gejala']);
    }

    public function test_store_rekam_medis_gagal_jika_status_tidak_valid(): void
    {
        $this->actingAs($this->admin)
             ->post(route('kesehatan.store'), [
                 'ear_tag_id'    => $this->domba->ear_tag_id,
                 'tanggal_sakit' => today()->toDateString(),
                 'status'        => 'tidak_valid',
                 'gejala'        => 'Gejala test',
             ])
             ->assertSessionHasErrors('status');
    }

    public function test_store_dengan_tandai_karantina_mengubah_status_domba(): void
    {
        $kandangIsolasi = Kandang::factory()->create(['tipe' => 'isolasi']);

        $this->actingAs($this->admin)
             ->post(route('kesehatan.store'), [
                 'ear_tag_id'           => $this->domba->ear_tag_id,
                 'tanggal_sakit'        => today()->toDateString(),
                 'status'               => 'dalam_perawatan',
                 'gejala'               => 'Gejala serius',
                 'tandai_karantina'     => 'ya',
                 'kandang_karantina_id' => $kandangIsolasi->kandang_id,
             ]);

        $this->assertDatabaseHas('domba', [
            'ear_tag_id' => $this->domba->ear_tag_id,
            'status'     => 'karantina',
        ]);
    }

    // ─── UPDATE REKAM MEDIS ──────────────────────────────────────────────────

    public function test_admin_dapat_memperbarui_rekam_medis(): void
    {
        $rekamId = DB::table('medical_record')->insertGetId([
            'ear_tag_id'    => $this->domba->ear_tag_id,
            'tanggal_sakit' => today()->toDateString(),
            'gejala'        => 'Gejala awal',
            'status'        => 'sakit',
            'created_at'    => now(),
            'updated_at'    => now(),
        ], 'rekam_id');

        $this->actingAs($this->admin)
             ->put(route('kesehatan.update', $rekamId), [
                 'tanggal_sakit' => today()->toDateString(),
                 'gejala'        => 'Gejala diperbarui',
                 'status'        => 'sembuh',
                 'tanggal_sembuh'=> today()->toDateString(),
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('medical_record', [
            'rekam_id' => $rekamId,
            'status'   => 'sembuh',
        ]);
    }

    public function test_update_status_sembuh_mengaktifkan_kembali_domba_karantina(): void
    {
        DB::table('domba')->where('ear_tag_id', $this->domba->ear_tag_id)
            ->update(['status' => 'karantina']);

        $rekamId = DB::table('medical_record')->insertGetId([
            'ear_tag_id'    => $this->domba->ear_tag_id,
            'tanggal_sakit' => today()->toDateString(),
            'gejala'        => 'Sakit',
            'status'        => 'dalam_perawatan',
            'created_at'    => now(),
            'updated_at'    => now(),
        ], 'rekam_id');

        $this->actingAs($this->admin)
             ->put(route('kesehatan.update', $rekamId), [
                 'tanggal_sakit' => today()->toDateString(),
                 'gejala'        => 'Sakit',
                 'status'        => 'sembuh',
             ]);

        $this->assertDatabaseHas('domba', [
            'ear_tag_id' => $this->domba->ear_tag_id,
            'status'     => 'aktif',
        ]);
    }

    // ─── DESTROY REKAM MEDIS ─────────────────────────────────────────────────

    public function test_admin_dapat_menghapus_rekam_medis(): void
    {
        $rekamId = DB::table('medical_record')->insertGetId([
            'ear_tag_id'    => $this->domba->ear_tag_id,
            'tanggal_sakit' => today()->toDateString(),
            'gejala'        => 'Gejala test',
            'status'        => 'sakit',
            'created_at'    => now(),
            'updated_at'    => now(),
        ], 'rekam_id');

        $this->actingAs($this->admin)
             ->delete(route('kesehatan.destroy', $rekamId))
             ->assertRedirect();

        $this->assertDatabaseMissing('medical_record', ['rekam_id' => $rekamId]);
    }

    // ─── VAKSINASI ───────────────────────────────────────────────────────────

    public function test_admin_dapat_menyimpan_data_vaksinasi(): void
    {
        $vaksin = ObatVaksin::create([
            'nama_obat'    => 'Vaksin PMK ' . uniqid(),
            'tipe'         => 'vaksin',
            'satuan'       => 'dosis',
            'stok'         => 100,
            'stok_minimum' => 10,
        ]);

        $this->actingAs($this->admin)
             ->post(route('kesehatan.vaksinasi.store'), [
                 'ear_tag_id'        => $this->domba->ear_tag_id,
                 'obat_id'           => $vaksin->obat_id,
                 'tanggal_vaksinasi' => today()->toDateString(),
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('vaksinasi', [
            'ear_tag_id' => $this->domba->ear_tag_id,
            'obat_id'    => $vaksin->obat_id,
        ]);
    }

    public function test_store_vaksinasi_gagal_jika_field_wajib_kosong(): void
    {
        $this->actingAs($this->admin)
             ->post(route('kesehatan.vaksinasi.store'), [])
             ->assertSessionHasErrors(['ear_tag_id', 'obat_id', 'tanggal_vaksinasi']);
    }

    public function test_admin_dapat_menghapus_data_vaksinasi(): void
    {
        $vaksin = ObatVaksin::create([
            'nama_obat'    => 'Vaksin ' . uniqid(),
            'tipe'         => 'vaksin',
            'satuan'       => 'dosis',
            'stok'         => 50,
            'stok_minimum' => 5,
        ]);

        $vaksinId = DB::table('vaksinasi')->insertGetId([
            'ear_tag_id'        => $this->domba->ear_tag_id,
            'obat_id'           => $vaksin->obat_id,
            'tanggal_vaksinasi' => today()->toDateString(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ], 'vaksin_id');

        $this->actingAs($this->admin)
             ->delete(route('kesehatan.vaksinasi.destroy', $vaksinId))
             ->assertRedirect();

        $this->assertDatabaseMissing('vaksinasi', ['vaksin_id' => $vaksinId]);
    }

    // ─── PINDAH KANDANG ──────────────────────────────────────────────────────

    public function test_admin_dapat_memindahkan_domba_karantina_ke_kandang_lain(): void
    {
        DB::table('domba')->where('ear_tag_id', $this->domba->ear_tag_id)
            ->update(['status' => 'karantina']);

        $kandangBaru = Kandang::factory()->create(['tipe' => 'utama']);

        $this->actingAs($this->admin)
             ->patch(route('kesehatan.karantina.pindah', $this->domba->ear_tag_id), [
                 'kandang_id' => $kandangBaru->kandang_id,
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('domba', [
            'ear_tag_id' => $this->domba->ear_tag_id,
            'kandang_id' => $kandangBaru->kandang_id,
        ]);
    }
}
