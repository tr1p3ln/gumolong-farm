<?php

namespace Tests\Feature;

use App\Models\Domba;
use App\Models\Kandang;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MobilePKTest extends TestCase
{
    use DatabaseTransactions;

    private User $pk;
    private Domba $domba;
    private Kandang $kandang;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pk      = User::factory()->pengurusKandang()->create();
        $this->kandang = Kandang::factory()->create();
        $this->domba   = Domba::factory()->create([
            'kandang_id'    => $this->kandang->kandang_id,
            'status'        => 'aktif',
            'jenis_kelamin' => 'betina',
        ]);
    }

    // ─── DASHBOARD ──────────────────────────────────────────────────────────

    public function test_pengurus_kandang_dapat_mengakses_dashboard_pk(): void
    {
        $this->actingAs($this->pk)
             ->get(route('pk.dashboard'))
             ->assertOk();
    }

    public function test_tamu_tidak_dapat_mengakses_dashboard_pk(): void
    {
        $this->get(route('pk.dashboard'))->assertRedirect('/login');
    }

    public function test_admin_tidak_dapat_mengakses_dashboard_pk(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->get(route('pk.dashboard'))
             ->assertForbidden();
    }

    // ─── TUGAS ──────────────────────────────────────────────────────────────

    public function test_pengurus_kandang_dapat_melihat_daftar_tugas(): void
    {
        $this->actingAs($this->pk)
             ->get(route('pk.tugas'))
             ->assertOk();
    }

    // ─── TIMBANGAN ──────────────────────────────────────────────────────────

    public function test_pengurus_kandang_dapat_membuka_halaman_timbangan(): void
    {
        $this->actingAs($this->pk)
             ->get(route('pk.timbangan'))
             ->assertOk();
    }

    public function test_pengurus_kandang_dapat_menyimpan_data_timbangan(): void
    {
        $this->actingAs($this->pk)
             ->post(route('pk.timbangan.store'), [
                 'ear_tag_id'      => $this->domba->ear_tag_id,
                 'berat_kg'        => 28.5,
                 'tanggal_timbang' => today()->toDateString(),
             ])
             ->assertRedirect(route('pk.timbangan'));

        $this->assertDatabaseHas('penimbangan', [
            'ear_tag_id'      => $this->domba->ear_tag_id,
            'berat_kg'        => 28.5,
            'status_validasi' => 'pending',
        ]);
    }

    public function test_store_timbangan_gagal_jika_field_wajib_kosong(): void
    {
        $this->actingAs($this->pk)
             ->post(route('pk.timbangan.store'), [])
             ->assertSessionHasErrors(['ear_tag_id', 'berat_kg', 'tanggal_timbang']);
    }

    public function test_store_timbangan_berstatus_pending_menunggu_validasi(): void
    {
        $this->actingAs($this->pk)
             ->post(route('pk.timbangan.store'), [
                 'ear_tag_id'      => $this->domba->ear_tag_id,
                 'berat_kg'        => 30.0,
                 'tanggal_timbang' => today()->toDateString(),
             ]);

        $timbangan = DB::table('penimbangan')
            ->where('ear_tag_id', $this->domba->ear_tag_id)
            ->latest('created_at')
            ->first();

        $this->assertEquals('pending', $timbangan->status_validasi);
    }

    // ─── KESEHATAN ──────────────────────────────────────────────────────────

    public function test_pengurus_kandang_dapat_membuka_halaman_kesehatan(): void
    {
        $this->actingAs($this->pk)
             ->get(route('pk.kesehatan'))
             ->assertOk();
    }

    public function test_pengurus_kandang_dapat_melaporkan_kondisi_kesehatan_domba(): void
    {
        $this->actingAs($this->pk)
             ->post(route('pk.kesehatan.store'), [
                 'ear_tag_id'        => $this->domba->ear_tag_id,
                 'gejala'            => 'Tidak mau makan sejak pagi',
                 'tingkat_keparahan' => 'ringan',
             ])
             ->assertRedirect(route('pk.dashboard'));

        $this->assertDatabaseHas('medical_record', [
            'ear_tag_id' => $this->domba->ear_tag_id,
            'status'     => 'sakit',
        ]);
    }

    public function test_store_kesehatan_gagal_jika_field_wajib_kosong(): void
    {
        $this->actingAs($this->pk)
             ->post(route('pk.kesehatan.store'), [])
             ->assertSessionHasErrors(['ear_tag_id', 'gejala', 'tingkat_keparahan']);
    }

    public function test_store_kesehatan_gagal_jika_tingkat_keparahan_tidak_valid(): void
    {
        $this->actingAs($this->pk)
             ->post(route('pk.kesehatan.store'), [
                 'ear_tag_id'        => $this->domba->ear_tag_id,
                 'gejala'            => 'Gejala test',
                 'tingkat_keparahan' => 'sangat_parah', // Tidak valid
             ])
             ->assertSessionHasErrors('tingkat_keparahan');
    }

    // ─── KELAHIRAN ──────────────────────────────────────────────────────────

    public function test_pengurus_kandang_dapat_membuka_halaman_kelahiran(): void
    {
        $this->actingAs($this->pk)
             ->get(route('pk.kelahiran'))
             ->assertOk();
    }

    public function test_store_kelahiran_gagal_jika_tidak_ada_perkawinan_aktif(): void
    {
        $this->actingAs($this->pk)
             ->post(route('pk.kelahiran.store'), [
                 'indukan_id'        => $this->domba->ear_tag_id,
                 'tanggal_kelahiran' => today()->toDateString(),
                 'jml_anak_hidup'    => 1,
                 'jml_anak_mati'     => 0,
             ])
             ->assertSessionHasErrors('indukan_id');
    }

    public function test_store_kelahiran_berhasil_jika_ada_perkawinan_aktif(): void
    {
        $pejantan = Domba::factory()->create([
            'kandang_id'    => $this->kandang->kandang_id,
            'jenis_kelamin' => 'jantan',
        ]);

        DB::table('perkawinan')->insert([
            'pejantan_id'        => $pejantan->ear_tag_id,
            'indukan_id'         => $this->domba->ear_tag_id,
            'user_id'            => $this->pk->user_id,
            'tanggal_perkawinan' => today()->subDays(150)->toDateString(),
            'metode'             => 'alami',
            'estimasi_lahir'     => today()->toDateString(),
            'status'             => 'bunting',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $this->actingAs($this->pk)
             ->post(route('pk.kelahiran.store'), [
                 'indukan_id'        => $this->domba->ear_tag_id,
                 'tanggal_kelahiran' => today()->toDateString(),
                 'jml_anak_hidup'    => 2,
                 'jml_anak_mati'     => 0,
             ])
             ->assertRedirect(route('pk.dashboard'));

        $this->assertDatabaseHas('kelahiran', [
            'jml_anak_hidup' => 2,
        ]);
    }
}
