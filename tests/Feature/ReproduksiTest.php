<?php

namespace Tests\Feature;

use App\Models\Domba;
use App\Models\Kandang;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReproduksiTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private Domba $pejantan;
    private Domba $indukan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin   = User::factory()->admin()->create();
        $kandang       = Kandang::factory()->create();

        $this->pejantan = Domba::factory()->create([
            'kandang_id'    => $kandang->kandang_id,
            'jenis_kelamin' => 'jantan',
            'kategori'      => 'pejantan',
            'status'        => 'aktif',
        ]);

        $this->indukan = Domba::factory()->create([
            'kandang_id'    => $kandang->kandang_id,
            'jenis_kelamin' => 'betina',
            'kategori'      => 'indukan',
            'status'        => 'aktif',
        ]);
    }

    // ─── INDEX ───────────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_halaman_reproduksi(): void
    {
        $this->actingAs($this->admin)
             ->get(route('reproduksi.index'))
             ->assertOk();
    }

    public function test_tamu_tidak_dapat_mengakses_halaman_reproduksi(): void
    {
        $this->get(route('reproduksi.index'))->assertRedirect('/login');
    }

    // ─── STORE PERKAWINAN ────────────────────────────────────────────────────

    public function test_admin_dapat_mencatat_perkawinan(): void
    {
        $this->actingAs($this->admin)
             ->post(route('reproduksi.store'), [
                 'pejantan_id'        => $this->pejantan->ear_tag_id,
                 'indukan_ids'        => [$this->indukan->ear_tag_id],
                 'tanggal_perkawinan' => today()->toDateString(),
                 'metode'             => 'alami',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('perkawinan', [
            'pejantan_id' => $this->pejantan->ear_tag_id,
            'indukan_id'  => $this->indukan->ear_tag_id,
            'status'      => 'menunggu_konfirmasi',
        ]);
    }

    public function test_store_perkawinan_otomatis_hitung_estimasi_lahir_150_hari(): void
    {
        $tanggalKawin = today()->toDateString();
        $ekspekHpl    = today()->addDays(150)->toDateString();

        $this->actingAs($this->admin)
             ->post(route('reproduksi.store'), [
                 'pejantan_id'        => $this->pejantan->ear_tag_id,
                 'indukan_ids'        => [$this->indukan->ear_tag_id],
                 'tanggal_perkawinan' => $tanggalKawin,
                 'metode'             => 'inseminasi_buatan',
             ]);

        $this->assertDatabaseHas('perkawinan', [
            'pejantan_id'    => $this->pejantan->ear_tag_id,
            'estimasi_lahir' => $ekspekHpl,
        ]);
    }

    public function test_store_perkawinan_gagal_jika_field_wajib_kosong(): void
    {
        $this->actingAs($this->admin)
             ->post(route('reproduksi.store'), [])
             ->assertSessionHasErrors(['pejantan_id', 'indukan_ids', 'tanggal_perkawinan', 'metode']);
    }

    public function test_store_perkawinan_gagal_jika_metode_tidak_valid(): void
    {
        $this->actingAs($this->admin)
             ->post(route('reproduksi.store'), [
                 'pejantan_id'        => $this->pejantan->ear_tag_id,
                 'indukan_ids'        => [$this->indukan->ear_tag_id],
                 'tanggal_perkawinan' => today()->toDateString(),
                 'metode'             => 'metode_tidak_valid',
             ])
             ->assertSessionHasErrors('metode');
    }

    // ─── UPDATE PERKAWINAN ───────────────────────────────────────────────────

    public function test_admin_dapat_memperbarui_data_perkawinan(): void
    {
        $kawinId = DB::table('perkawinan')->insertGetId([
            'pejantan_id'        => $this->pejantan->ear_tag_id,
            'indukan_id'         => $this->indukan->ear_tag_id,
            'user_id'            => $this->admin->user_id,
            'tanggal_perkawinan' => today()->toDateString(),
            'metode'             => 'alami',
            'estimasi_lahir'     => today()->addDays(150)->toDateString(),
            'status'             => 'menunggu_konfirmasi',
            'created_at'         => now(),
            'updated_at'         => now(),
        ], 'kawin_id');

        $this->actingAs($this->admin)
             ->put(route('reproduksi.update', $kawinId), [
                 'tanggal_perkawinan' => today()->toDateString(),
                 'metode'             => 'inseminasi_buatan',
                 'status'             => 'menunggu_konfirmasi',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('perkawinan', [
            'kawin_id' => $kawinId,
            'metode'   => 'inseminasi_buatan',
        ]);
    }

    // ─── DESTROY PERKAWINAN ──────────────────────────────────────────────────

    public function test_admin_dapat_menghapus_perkawinan_tanpa_kelahiran(): void
    {
        $kawinId = DB::table('perkawinan')->insertGetId([
            'pejantan_id'        => $this->pejantan->ear_tag_id,
            'indukan_id'         => $this->indukan->ear_tag_id,
            'user_id'            => $this->admin->user_id,
            'tanggal_perkawinan' => today()->toDateString(),
            'metode'             => 'alami',
            'estimasi_lahir'     => today()->addDays(150)->toDateString(),
            'status'             => 'menunggu_konfirmasi',
            'created_at'         => now(),
            'updated_at'         => now(),
        ], 'kawin_id');

        $this->actingAs($this->admin)
             ->delete(route('reproduksi.destroy', $kawinId))
             ->assertRedirect();

        $this->assertDatabaseMissing('perkawinan', ['kawin_id' => $kawinId]);
    }

    public function test_perkawinan_yang_sudah_punya_kelahiran_tidak_dapat_dihapus(): void
    {
        $kawinId = DB::table('perkawinan')->insertGetId([
            'pejantan_id'        => $this->pejantan->ear_tag_id,
            'indukan_id'         => $this->indukan->ear_tag_id,
            'user_id'            => $this->admin->user_id,
            'tanggal_perkawinan' => today()->toDateString(),
            'metode'             => 'alami',
            'estimasi_lahir'     => today()->addDays(150)->toDateString(),
            'status'             => 'lahir',
            'created_at'         => now(),
            'updated_at'         => now(),
        ], 'kawin_id');

        DB::table('kelahiran')->insert([
            'kawin_id'          => $kawinId,
            'user_id'           => $this->admin->user_id,
            'tanggal_kelahiran' => today()->toDateString(),
            'jml_anak_hidup'    => 1,
            'jml_anak_mati'     => 0,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $this->actingAs($this->admin)
             ->delete(route('reproduksi.destroy', $kawinId))
             ->assertRedirect();

        $this->assertDatabaseHas('perkawinan', ['kawin_id' => $kawinId]);
    }

    // ─── KONFIRMASI KEBUNTINGAN ──────────────────────────────────────────────

    public function test_admin_dapat_konfirmasi_kebuntingan(): void
    {
        $kawinId = DB::table('perkawinan')->insertGetId([
            'pejantan_id'        => $this->pejantan->ear_tag_id,
            'indukan_id'         => $this->indukan->ear_tag_id,
            'user_id'            => $this->admin->user_id,
            'tanggal_perkawinan' => today()->toDateString(),
            'metode'             => 'alami',
            'estimasi_lahir'     => today()->addDays(150)->toDateString(),
            'status'             => 'menunggu_konfirmasi',
            'created_at'         => now(),
            'updated_at'         => now(),
        ], 'kawin_id');

        $this->actingAs($this->admin)
             ->post(route('reproduksi.konfirmasi', $kawinId), [
                 'hasil'             => 'bunting',
                 'metode_konfirmasi' => 'USG',
                 'tgl_konfirmasi'    => today()->toDateString(),
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('perkawinan', [
            'kawin_id' => $kawinId,
            'status'   => 'bunting',
        ]);
    }

    public function test_konfirmasi_gagal_jika_bukan_status_menunggu(): void
    {
        $kawinId = DB::table('perkawinan')->insertGetId([
            'pejantan_id'        => $this->pejantan->ear_tag_id,
            'indukan_id'         => $this->indukan->ear_tag_id,
            'user_id'            => $this->admin->user_id,
            'tanggal_perkawinan' => today()->toDateString(),
            'metode'             => 'alami',
            'estimasi_lahir'     => today()->addDays(150)->toDateString(),
            'status'             => 'bunting', // Sudah dikonfirmasi
            'created_at'         => now(),
            'updated_at'         => now(),
        ], 'kawin_id');

        $this->actingAs($this->admin)
             ->post(route('reproduksi.konfirmasi', $kawinId), [
                 'hasil'             => 'bunting',
                 'metode_konfirmasi' => 'USG',
                 'tgl_konfirmasi'    => today()->toDateString(),
             ])
             ->assertStatus(422);
    }

    // ─── KELAHIRAN ───────────────────────────────────────────────────────────

    public function test_admin_dapat_mencatat_kelahiran(): void
    {
        $kawinId = DB::table('perkawinan')->insertGetId([
            'pejantan_id'        => $this->pejantan->ear_tag_id,
            'indukan_id'         => $this->indukan->ear_tag_id,
            'user_id'            => $this->admin->user_id,
            'tanggal_perkawinan' => today()->subDays(150)->toDateString(),
            'metode'             => 'alami',
            'estimasi_lahir'     => today()->toDateString(),
            'status'             => 'bunting',
            'created_at'         => now(),
            'updated_at'         => now(),
        ], 'kawin_id');

        $this->actingAs($this->admin)
             ->post(route('reproduksi.kelahiran.store', $kawinId), [
                 'tanggal_kelahiran' => today()->toDateString(),
                 'jml_anak_hidup'    => 2,
                 'jml_anak_mati'     => 0,
                 'bobot_rata_rata'   => 3.5,
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('kelahiran', [
            'kawin_id'       => $kawinId,
            'jml_anak_hidup' => 2,
        ]);

        $this->assertDatabaseHas('perkawinan', [
            'kawin_id' => $kawinId,
            'status'   => 'lahir',
        ]);
    }

    public function test_store_kelahiran_gagal_jika_field_wajib_kosong(): void
    {
        $kawinId = DB::table('perkawinan')->insertGetId([
            'pejantan_id'        => $this->pejantan->ear_tag_id,
            'indukan_id'         => $this->indukan->ear_tag_id,
            'user_id'            => $this->admin->user_id,
            'tanggal_perkawinan' => today()->toDateString(),
            'metode'             => 'alami',
            'estimasi_lahir'     => today()->addDays(150)->toDateString(),
            'status'             => 'bunting',
            'created_at'         => now(),
            'updated_at'         => now(),
        ], 'kawin_id');

        $this->actingAs($this->admin)
             ->post(route('reproduksi.kelahiran.store', $kawinId), [])
             ->assertSessionHasErrors(['tanggal_kelahiran', 'jml_anak_hidup', 'jml_anak_mati']);
    }
}
