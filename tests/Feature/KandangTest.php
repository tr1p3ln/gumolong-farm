<?php

namespace Tests\Feature;

use App\Models\Domba;
use App\Models\Kandang;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KandangTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    // ─── INDEX ───────────────────────────────────────────────────────────────

    public function test_admin_can_view_kandang_index(): void
    {
        $this->actingAs($this->admin)
             ->get(route('kandang.index'))
             ->assertOk();
    }

    public function test_unauthenticated_user_cannot_view_kandang(): void
    {
        $this->get(route('kandang.index'))->assertRedirect('/login');
    }

    public function test_pengurus_kandang_cannot_access_kandang_management(): void
    {
        $pk = User::factory()->pengurusKandang()->create();

        $this->actingAs($pk)
             ->get(route('kandang.index'))
             ->assertRedirect(route('tugas-harian.mobile'));
    }

    // ─── STORE ───────────────────────────────────────────────────────────────

    public function test_admin_can_create_kandang(): void
    {
        $payload = [
            'nama_kandang' => 'Kandang Test ' . uniqid(),
            'tipe'         => 'utama',
            'kapasitas'    => 50,
        ];

        $response = $this->actingAs($this->admin)
                         ->postJson(route('kandang.store'), $payload);

        $response->assertCreated()
                 ->assertJsonPath('success', true);

        $this->assertDatabaseHas('kandang', ['nama_kandang' => $payload['nama_kandang']]);
    }

    public function test_kandang_name_must_be_unique(): void
    {
        $kandang = Kandang::factory()->create();

        $this->actingAs($this->admin)
             ->postJson(route('kandang.store'), [
                 'nama_kandang' => $kandang->nama_kandang,
                 'tipe'         => 'utama',
                 'kapasitas'    => 30,
             ])
             ->assertUnprocessable()
             ->assertJsonValidationErrors('nama_kandang');
    }

    public function test_kandang_requires_all_mandatory_fields(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('kandang.store'), [])
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['nama_kandang', 'tipe', 'kapasitas']);
    }

    public function test_kandang_tipe_must_be_valid(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('kandang.store'), [
                 'nama_kandang' => 'Test ' . uniqid(),
                 'tipe'         => 'invalid_tipe',
                 'kapasitas'    => 30,
             ])
             ->assertUnprocessable()
             ->assertJsonValidationErrors('tipe');
    }

    public function test_kandang_kapasitas_must_be_at_least_1(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('kandang.store'), [
                 'nama_kandang' => 'Test ' . uniqid(),
                 'tipe'         => 'utama',
                 'kapasitas'    => 0,
             ])
             ->assertUnprocessable()
             ->assertJsonValidationErrors('kapasitas');
    }

    // ─── SHOW ────────────────────────────────────────────────────────────────

    public function test_admin_can_get_kandang_detail(): void
    {
        $kandang = Kandang::factory()->create();

        $this->actingAs($this->admin)
             ->getJson(route('kandang.show', $kandang->kandang_id))
             ->assertOk()
             ->assertJsonPath('kandang_id', $kandang->kandang_id);
    }

    public function test_show_returns_404_for_nonexistent_kandang(): void
    {
        $this->actingAs($this->admin)
             ->getJson(route('kandang.show', 99999))
             ->assertNotFound();
    }

    // ─── UPDATE ──────────────────────────────────────────────────────────────

    public function test_admin_can_update_kandang(): void
    {
        $kandang = Kandang::factory()->create(['kapasitas' => 50]);

        $this->actingAs($this->admin)
             ->putJson(route('kandang.update', $kandang->kandang_id), [
                 'nama_kandang' => 'Kandang Updated ' . uniqid(),
                 'tipe'         => 'isolasi',
                 'kapasitas'    => 60,
             ])
             ->assertOk()
             ->assertJsonPath('success', true);
    }

    public function test_cannot_update_capacity_below_active_domba_count(): void
    {
        $kandang = Kandang::factory()->create(['kapasitas' => 50]);

        // Buat 5 domba aktif di kandang ini
        Domba::factory()->count(5)->create(['kandang_id' => $kandang->kandang_id, 'status' => 'aktif']);

        $this->actingAs($this->admin)
             ->putJson(route('kandang.update', $kandang->kandang_id), [
                 'nama_kandang' => $kandang->nama_kandang,
                 'tipe'         => $kandang->tipe,
                 'kapasitas'    => 3,
             ])
             ->assertUnprocessable()
             ->assertJsonPath('success', false);
    }

    public function test_kandang_name_unique_allows_same_name_on_update(): void
    {
        $kandang = Kandang::factory()->create(['kapasitas' => 50]);

        $this->actingAs($this->admin)
             ->putJson(route('kandang.update', $kandang->kandang_id), [
                 'nama_kandang' => $kandang->nama_kandang,
                 'tipe'         => $kandang->tipe,
                 'kapasitas'    => 60,
             ])
             ->assertOk()
             ->assertJsonPath('success', true);
    }

    // ─── DESTROY ─────────────────────────────────────────────────────────────

    public function test_admin_can_delete_empty_kandang(): void
    {
        $kandang = Kandang::factory()->create();

        $this->actingAs($this->admin)
             ->deleteJson(route('kandang.destroy', $kandang->kandang_id))
             ->assertOk()
             ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('kandang', ['kandang_id' => $kandang->kandang_id]);
    }

    public function test_cannot_delete_kandang_with_active_domba(): void
    {
        $kandang = Kandang::factory()->create();
        Domba::factory()->create(['kandang_id' => $kandang->kandang_id, 'status' => 'aktif']);

        $this->actingAs($this->admin)
             ->deleteJson(route('kandang.destroy', $kandang->kandang_id))
             ->assertUnprocessable()
             ->assertJsonPath('success', false);

        $this->assertDatabaseHas('kandang', ['kandang_id' => $kandang->kandang_id]);
    }

    public function test_kepala_kandang_cannot_delete_kandang(): void
    {
        $kk      = User::factory()->kepalaKandang()->create();
        $kandang = Kandang::factory()->create();

        $this->actingAs($kk)
             ->deleteJson(route('kandang.destroy', $kandang->kandang_id))
             ->assertForbidden();
    }
}
