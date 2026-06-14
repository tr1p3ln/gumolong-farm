<?php

namespace Tests\Feature;

use App\Models\Domba;
use App\Models\Kandang;
use App\Models\TugasHarian;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MobileKKTest extends TestCase
{
    use DatabaseTransactions;

    private User $kk;
    private Kandang $kandang;
    private Domba $domba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kk      = User::factory()->kepalaKandang()->create();
        $this->kandang = Kandang::factory()->create();
        $this->domba   = Domba::factory()->create([
            'kandang_id' => $this->kandang->kandang_id,
            'status'     => 'aktif',
        ]);
    }

    // ─── DASHBOARD ──────────────────────────────────────────────────────────

    public function test_kepala_kandang_dapat_mengakses_dashboard_kk(): void
    {
        $this->actingAs($this->kk)
             ->get(route('kk.dashboard'))
             ->assertOk();
    }

    public function test_tamu_tidak_dapat_mengakses_dashboard_kk(): void
    {
        $this->get(route('kk.dashboard'))->assertRedirect('/login');
    }

    public function test_pengurus_kandang_tidak_dapat_mengakses_dashboard_kk(): void
    {
        $pk = User::factory()->pengurusKandang()->create();

        $this->actingAs($pk)
             ->get(route('kk.dashboard'))
             ->assertRedirect(route('tugas-harian.mobile'));
    }

    // ─── MONITOR TUGAS ──────────────────────────────────────────────────────

    public function test_kepala_kandang_dapat_memonitor_tugas_harian(): void
    {
        $this->actingAs($this->kk)
             ->get(route('kk.monitor-tugas'))
             ->assertOk();
    }

    // ─── KESEHATAN ──────────────────────────────────────────────────────────

    public function test_kepala_kandang_dapat_melihat_daftar_laporan_kesehatan(): void
    {
        $this->actingAs($this->kk)
             ->get(route('kk.kesehatan'))
             ->assertOk();
    }

    public function test_kepala_kandang_dapat_konfirmasi_status_kesehatan(): void
    {
        $rekamId = DB::table('medical_record')->insertGetId([
            'ear_tag_id'    => $this->domba->ear_tag_id,
            'tanggal_sakit' => today()->toDateString(),
            'gejala'        => 'Demam',
            'status'        => 'sakit',
            'created_at'    => now(),
            'updated_at'    => now(),
        ], 'rekam_id');

        $this->actingAs($this->kk)
             ->post(route('kk.kesehatan.konfirmasi', $rekamId), [
                 'action' => 'dalam_perawatan',
             ])
             ->assertRedirect(route('kk.kesehatan'));

        $this->assertDatabaseHas('medical_record', [
            'rekam_id' => $rekamId,
            'status'   => 'dalam_perawatan',
        ]);
    }

    public function test_konfirmasi_kesehatan_gagal_jika_action_tidak_valid(): void
    {
        $rekamId = DB::table('medical_record')->insertGetId([
            'ear_tag_id'    => $this->domba->ear_tag_id,
            'tanggal_sakit' => today()->toDateString(),
            'gejala'        => 'Test',
            'status'        => 'sakit',
            'created_at'    => now(),
            'updated_at'    => now(),
        ], 'rekam_id');

        $this->actingAs($this->kk)
             ->post(route('kk.kesehatan.konfirmasi', $rekamId), [
                 'action' => 'action_tidak_valid',
             ])
             ->assertSessionHasErrors('action');
    }

    // ─── REPRODUKSI ──────────────────────────────────────────────────────────

    public function test_kepala_kandang_dapat_melihat_halaman_reproduksi(): void
    {
        $this->actingAs($this->kk)
             ->get(route('kk.reproduksi'))
             ->assertOk();
    }

    public function test_kepala_kandang_dapat_konfirmasi_kebuntingan(): void
    {
        $pejantan = Domba::factory()->create([
            'kandang_id'    => $this->kandang->kandang_id,
            'jenis_kelamin' => 'jantan',
        ]);

        $kawinId = DB::table('perkawinan')->insertGetId([
            'pejantan_id'        => $pejantan->ear_tag_id,
            'indukan_id'         => $this->domba->ear_tag_id,
            'user_id'            => $this->kk->user_id,
            'tanggal_perkawinan' => today()->subDays(30)->toDateString(),
            'metode'             => 'alami',
            'estimasi_lahir'     => today()->addDays(120)->toDateString(),
            'status'             => 'menunggu_konfirmasi',
            'created_at'         => now(),
            'updated_at'         => now(),
        ], 'kawin_id');

        $this->actingAs($this->kk)
             ->post(route('kk.reproduksi.konfirmasi', $kawinId), [
                 'status' => 'bunting',
             ])
             ->assertRedirect(route('kk.reproduksi'));

        $this->assertDatabaseHas('perkawinan', [
            'kawin_id' => $kawinId,
            'status'   => 'bunting',
        ]);
    }

    public function test_konfirmasi_kebuntingan_gagal_jika_status_tidak_valid(): void
    {
        $kawinId = DB::table('perkawinan')->insertGetId([
            'pejantan_id'        => $this->domba->ear_tag_id,
            'indukan_id'         => $this->domba->ear_tag_id,
            'user_id'            => $this->kk->user_id,
            'tanggal_perkawinan' => today()->toDateString(),
            'metode'             => 'alami',
            'estimasi_lahir'     => today()->addDays(150)->toDateString(),
            'status'             => 'menunggu_konfirmasi',
            'created_at'         => now(),
            'updated_at'         => now(),
        ], 'kawin_id');

        $this->actingAs($this->kk)
             ->post(route('kk.reproduksi.konfirmasi', $kawinId), [
                 'status' => 'tidak_valid',
             ])
             ->assertSessionHasErrors('status');
    }

    // ─── VALIDASI TIMBANGAN ──────────────────────────────────────────────────

    public function test_kepala_kandang_dapat_melihat_halaman_validasi_timbangan(): void
    {
        $this->actingAs($this->kk)
             ->get(route('kk.validasi-timbangan'))
             ->assertOk();
    }

    public function test_kepala_kandang_dapat_memvalidasi_data_timbangan(): void
    {
        $timbanganId = DB::table('penimbangan')->insertGetId([
            'ear_tag_id'      => $this->domba->ear_tag_id,
            'tanggal_timbang' => today()->toDateString(),
            'berat_kg'        => 35.0,
            'status_validasi' => 'pending',
            'created_at'      => now(),
            'updated_at'      => now(),
        ], 'timbangan_id');

        $this->actingAs($this->kk)
             ->post(route('kk.validasi-timbangan.process', $timbanganId), [
                 'action' => 'valid',
             ])
             ->assertRedirect(route('kk.validasi-timbangan'));

        $this->assertDatabaseHas('penimbangan', [
            'timbangan_id'   => $timbanganId,
            'status_validasi'=> 'valid',
        ]);
    }

    public function test_kepala_kandang_dapat_menolak_data_timbangan(): void
    {
        $timbanganId = DB::table('penimbangan')->insertGetId([
            'ear_tag_id'      => $this->domba->ear_tag_id,
            'tanggal_timbang' => today()->toDateString(),
            'berat_kg'        => 99.0,
            'status_validasi' => 'pending',
            'created_at'      => now(),
            'updated_at'      => now(),
        ], 'timbangan_id');

        $this->actingAs($this->kk)
             ->post(route('kk.validasi-timbangan.process', $timbanganId), [
                 'action' => 'ditolak',
             ])
             ->assertRedirect(route('kk.validasi-timbangan'));

        $this->assertDatabaseHas('penimbangan', [
            'timbangan_id'   => $timbanganId,
            'status_validasi'=> 'ditolak',
        ]);
    }

    public function test_process_validasi_gagal_jika_action_tidak_valid(): void
    {
        $timbanganId = DB::table('penimbangan')->insertGetId([
            'ear_tag_id'      => $this->domba->ear_tag_id,
            'tanggal_timbang' => today()->toDateString(),
            'berat_kg'        => 30.0,
            'status_validasi' => 'pending',
            'created_at'      => now(),
            'updated_at'      => now(),
        ], 'timbangan_id');

        $this->actingAs($this->kk)
             ->post(route('kk.validasi-timbangan.process', $timbanganId), [
                 'action' => 'asal_approve', // Tidak valid
             ])
             ->assertSessionHasErrors('action');
    }
}
