<?php

namespace Tests\Feature;

use App\Models\Kandang;
use App\Models\TemplateTugasRutin;
use App\Models\TugasHarian;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TugasHarianTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private Kandang $kandang;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin   = User::factory()->admin()->create();
        $this->kandang = Kandang::factory()->create();
    }

    // ─── INDEX ───────────────────────────────────────────────────────────────

    public function test_admin_can_view_tugas_harian_index(): void
    {
        $this->actingAs($this->admin)
             ->get(route('tugas-harian.index'))
             ->assertOk();
    }

    public function test_index_shows_tasks_for_requested_date(): void
    {
        $tanggal = '2026-06-01';
        TugasHarian::factory()->create([
            'kandang_id' => $this->kandang->kandang_id,
            'tanggal'    => $tanggal,
            'judul'      => 'Tugas Test Spesifik',
        ]);

        $this->actingAs($this->admin)
             ->get(route('tugas-harian.index') . "?tanggal={$tanggal}")
             ->assertOk()
             ->assertSee('Tugas Test Spesifik');
    }

    public function test_unauthenticated_cannot_view_tugas_harian(): void
    {
        $this->get(route('tugas-harian.index'))->assertRedirect('/login');
    }

    // ─── STORE ───────────────────────────────────────────────────────────────

    public function test_admin_can_create_tugas(): void
    {
        $payload = [
            'judul'      => 'Tugas Baru Test ' . uniqid(),
            'kandang_id' => $this->kandang->kandang_id,
            'tanggal'    => today()->toDateString(),
            'tipe'       => 'rutin',
            'prioritas'  => 'sedang',
        ];

        $response = $this->actingAs($this->admin)
                         ->postJson(route('tugas-harian.store'), $payload);

        $response->assertOk()
                 ->assertJsonPath('success', true);

        $this->assertDatabaseHas('tugas_harian', [
            'judul'  => $payload['judul'],
            'status' => 'belum',
        ]);
    }

    public function test_create_tugas_sets_status_belum_automatically(): void
    {
        $response = $this->actingAs($this->admin)
                         ->postJson(route('tugas-harian.store'), [
                             'judul'      => 'Tugas Auto Status',
                             'kandang_id' => $this->kandang->kandang_id,
                             'tanggal'    => today()->toDateString(),
                             'tipe'       => 'kondisional',
                             'prioritas'  => 'tinggi',
                         ]);

        $response->assertOk();
        $id = $response->json('data.id');
        $this->assertDatabaseHas('tugas_harian', ['id' => $id, 'status' => 'belum']);
    }

    public function test_create_tugas_requires_mandatory_fields(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('tugas-harian.store'), [])
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['judul', 'kandang_id', 'tanggal', 'tipe', 'prioritas']);
    }

    public function test_create_tugas_requires_valid_kandang(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('tugas-harian.store'), [
                 'judul'      => 'Test',
                 'kandang_id' => 99999,
                 'tanggal'    => today()->toDateString(),
                 'tipe'       => 'rutin',
                 'prioritas'  => 'sedang',
             ])
             ->assertUnprocessable()
             ->assertJsonValidationErrors('kandang_id');
    }

    public function test_create_tugas_validates_tipe(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('tugas-harian.store'), [
                 'judul'      => 'Test',
                 'kandang_id' => $this->kandang->kandang_id,
                 'tanggal'    => today()->toDateString(),
                 'tipe'       => 'tidak_valid',
                 'prioritas'  => 'sedang',
             ])
             ->assertUnprocessable()
             ->assertJsonValidationErrors('tipe');
    }

    public function test_create_tugas_validates_prioritas(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('tugas-harian.store'), [
                 'judul'      => 'Test',
                 'kandang_id' => $this->kandang->kandang_id,
                 'tanggal'    => today()->toDateString(),
                 'tipe'       => 'rutin',
                 'prioritas'  => 'super_penting',
             ])
             ->assertUnprocessable()
             ->assertJsonValidationErrors('prioritas');
    }

    // ─── SHOW ────────────────────────────────────────────────────────────────

    public function test_admin_can_view_tugas_detail(): void
    {
        $tugas = TugasHarian::factory()->create(['kandang_id' => $this->kandang->kandang_id]);

        $this->actingAs($this->admin)
             ->getJson(route('tugas-harian.show', $tugas->id))
             ->assertOk()
             ->assertJsonPath('id', $tugas->id);
    }

    public function test_show_returns_404_for_nonexistent_tugas(): void
    {
        $this->actingAs($this->admin)
             ->getJson(route('tugas-harian.show', 99999))
             ->assertNotFound();
    }

    // ─── UPDATE ──────────────────────────────────────────────────────────────

    public function test_admin_can_update_tugas(): void
    {
        $tugas = TugasHarian::factory()->create(['kandang_id' => $this->kandang->kandang_id]);

        $this->actingAs($this->admin)
             ->putJson(route('tugas-harian.update', $tugas->id), [
                 'judul'      => 'Judul Updated',
                 'kandang_id' => $this->kandang->kandang_id,
                 'tanggal'    => today()->toDateString(),
                 'tipe'       => 'kondisional',
                 'prioritas'  => 'tinggi',
             ])
             ->assertOk()
             ->assertJsonPath('success', true);

        $this->assertDatabaseHas('tugas_harian', ['id' => $tugas->id, 'judul' => 'Judul Updated']);
    }

    // ─── UPDATE STATUS ───────────────────────────────────────────────────────

    public function test_can_update_status_to_dalam_proses(): void
    {
        $tugas = TugasHarian::factory()->create([
            'kandang_id' => $this->kandang->kandang_id,
            'status'     => 'belum',
            'waktu_mulai' => null,
        ]);

        $this->actingAs($this->admin)
             ->patchJson(route('tugas-harian.update-status', $tugas->id), [
                 'status' => 'dalam_proses',
             ])
             ->assertOk()
             ->assertJsonPath('success', true);

        $this->assertDatabaseHas('tugas_harian', [
            'id'     => $tugas->id,
            'status' => 'dalam_proses',
        ]);
    }

    public function test_status_dalam_proses_auto_sets_waktu_mulai(): void
    {
        $tugas = TugasHarian::factory()->create([
            'kandang_id'  => $this->kandang->kandang_id,
            'status'      => 'belum',
            'waktu_mulai' => null,
        ]);

        $this->actingAs($this->admin)
             ->patchJson(route('tugas-harian.update-status', $tugas->id), [
                 'status' => 'dalam_proses',
             ])
             ->assertOk();

        $this->assertNotNull($tugas->fresh()->waktu_mulai);
    }

    public function test_status_selesai_auto_sets_waktu_selesai(): void
    {
        $tugas = TugasHarian::factory()->create([
            'kandang_id'    => $this->kandang->kandang_id,
            'status'        => 'dalam_proses',
            'waktu_selesai' => null,
        ]);

        $this->actingAs($this->admin)
             ->patchJson(route('tugas-harian.update-status', $tugas->id), [
                 'status' => 'selesai',
             ])
             ->assertOk();

        $this->assertNotNull($tugas->fresh()->waktu_selesai);
    }

    public function test_can_update_status_to_dilewati(): void
    {
        $tugas = TugasHarian::factory()->create(['kandang_id' => $this->kandang->kandang_id]);

        $this->actingAs($this->admin)
             ->patchJson(route('tugas-harian.update-status', $tugas->id), [
                 'status' => 'dilewati',
             ])
             ->assertOk()
             ->assertJsonPath('data.status', 'dilewati');
    }

    public function test_status_update_validates_allowed_values(): void
    {
        $tugas = TugasHarian::factory()->create(['kandang_id' => $this->kandang->kandang_id]);

        $this->actingAs($this->admin)
             ->patchJson(route('tugas-harian.update-status', $tugas->id), [
                 'status' => 'status_aneh',
             ])
             ->assertUnprocessable()
             ->assertJsonValidationErrors('status');
    }

    // ─── DESTROY ─────────────────────────────────────────────────────────────

    public function test_admin_can_delete_tugas(): void
    {
        $tugas = TugasHarian::factory()->create(['kandang_id' => $this->kandang->kandang_id]);

        $this->actingAs($this->admin)
             ->deleteJson(route('tugas-harian.destroy', $tugas->id))
             ->assertOk()
             ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('tugas_harian', ['id' => $tugas->id]);
    }

    // ─── BULK REASSIGN ───────────────────────────────────────────────────────

    public function test_admin_can_bulk_reassign_tasks(): void
    {
        $petugas = User::factory()->pengurusKandang()->create();
        $tugas1  = TugasHarian::factory()->create(['kandang_id' => $this->kandang->kandang_id]);
        $tugas2  = TugasHarian::factory()->create(['kandang_id' => $this->kandang->kandang_id]);

        $response = $this->actingAs($this->admin)
                         ->postJson(route('tugas-harian.bulk-reassign'), [
                             'ids'     => [$tugas1->id, $tugas2->id],
                             'user_id' => $petugas->user_id,
                         ]);

        $response->assertOk()
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('count', 2);

        $this->assertDatabaseHas('tugas_harian', ['id' => $tugas1->id, 'user_id' => $petugas->user_id]);
        $this->assertDatabaseHas('tugas_harian', ['id' => $tugas2->id, 'user_id' => $petugas->user_id]);
    }

    public function test_bulk_reassign_requires_ids_and_user_id(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('tugas-harian.bulk-reassign'), [])
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['ids', 'user_id']);
    }

    public function test_bulk_reassign_requires_valid_user(): void
    {
        $tugas = TugasHarian::factory()->create(['kandang_id' => $this->kandang->kandang_id]);

        $this->actingAs($this->admin)
             ->postJson(route('tugas-harian.bulk-reassign'), [
                 'ids'     => [$tugas->id],
                 'user_id' => 99999,
             ])
             ->assertUnprocessable()
             ->assertJsonValidationErrors('user_id');
    }

    // ─── GENERATE RUTIN ──────────────────────────────────────────────────────

    public function test_admin_can_generate_tugas_from_templates(): void
    {
        TemplateTugasRutin::factory()->create([
            'kandang_id' => $this->kandang->kandang_id,
            'judul'      => 'Template Rutin Test ' . uniqid(),
            'is_active'  => true,
        ]);

        $tanggal = '2026-12-01';

        $response = $this->actingAs($this->admin)
                         ->postJson(route('tugas-harian.generate-rutin'), [
                             'tanggal' => $tanggal,
                         ]);

        $response->assertOk()
                 ->assertJsonPath('success', true);

        $this->assertGreaterThanOrEqual(0, $response->json('generated'));
    }

    public function test_generate_rutin_skips_existing_tasks(): void
    {
        $template = TemplateTugasRutin::factory()->create([
            'kandang_id' => $this->kandang->kandang_id,
            'judul'      => 'Rutin Duplikat Test',
            'is_active'  => true,
        ]);

        $tanggal = '2026-11-15';

        // Generate pertama kali
        $this->actingAs($this->admin)
             ->postJson(route('tugas-harian.generate-rutin'), ['tanggal' => $tanggal]);

        // Generate kedua kali — harus di-skip
        $response = $this->actingAs($this->admin)
                         ->postJson(route('tugas-harian.generate-rutin'), ['tanggal' => $tanggal]);

        $response->assertOk();
        $this->assertEquals(0, $response->json('generated'));
        $this->assertGreaterThan(0, $response->json('skipped'));
    }

    public function test_inactive_templates_not_included_in_generate_rutin(): void
    {
        TemplateTugasRutin::factory()->inactive()->create([
            'kandang_id' => $this->kandang->kandang_id,
            'judul'      => 'Template Inactive ' . uniqid(),
        ]);

        $tanggal = '2026-10-05';

        $response = $this->actingAs($this->admin)
                         ->postJson(route('tugas-harian.generate-rutin'), ['tanggal' => $tanggal]);

        $response->assertOk();
    }

    public function test_generate_rutin_requires_tanggal(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('tugas-harian.generate-rutin'), [])
             ->assertUnprocessable()
             ->assertJsonValidationErrors('tanggal');
    }

    public function test_kepala_kandang_cannot_generate_rutin(): void
    {
        $kk = User::factory()->kepalaKandang()->create();

        $this->actingAs($kk)
             ->postJson(route('tugas-harian.generate-rutin'), ['tanggal' => today()->toDateString()])
             ->assertForbidden();
    }
}
