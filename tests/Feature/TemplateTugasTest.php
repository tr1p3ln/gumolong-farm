<?php

namespace Tests\Feature;

use App\Models\Kandang;
use App\Models\TemplateTugasRutin;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TemplateTugasTest extends TestCase
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

    public function test_admin_can_view_template_index(): void
    {
        $this->actingAs($this->admin)
             ->get(route('template-tugas.index'))
             ->assertOk();
    }

    public function test_unauthenticated_cannot_view_templates(): void
    {
        $this->get(route('template-tugas.index'))->assertRedirect('/login');
    }

    public function test_kepala_kandang_cannot_access_template_management(): void
    {
        $kk = User::factory()->kepalaKandang()->create();

        $this->actingAs($kk)
             ->get(route('template-tugas.index'))
             ->assertForbidden();
    }

    // ─── STORE ───────────────────────────────────────────────────────────────

    public function test_admin_can_create_template(): void
    {
        $payload = [
            'judul'      => 'Template Test ' . uniqid(),
            'kandang_id' => $this->kandang->kandang_id,
            'prioritas'  => 'sedang',
        ];

        $response = $this->actingAs($this->admin)
                         ->postJson(route('template-tugas.store'), $payload);

        $response->assertOk()
                 ->assertJsonPath('success', true);

        $this->assertDatabaseHas('template_tugas_rutin', ['judul' => $payload['judul']]);
    }

    public function test_create_template_sets_is_active_true_by_default(): void
    {
        $response = $this->actingAs($this->admin)
                         ->postJson(route('template-tugas.store'), [
                             'judul'      => 'Template Default Active',
                             'kandang_id' => $this->kandang->kandang_id,
                             'prioritas'  => 'rendah',
                         ]);

        $response->assertOk();
        $id = $response->json('data.id');
        $this->assertDatabaseHas('template_tugas_rutin', ['id' => $id, 'is_active' => true]);
    }

    public function test_create_template_requires_mandatory_fields(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('template-tugas.store'), [])
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['judul', 'kandang_id', 'prioritas']);
    }

    public function test_create_template_requires_valid_kandang(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('template-tugas.store'), [
                 'judul'      => 'Test',
                 'kandang_id' => 99999,
                 'prioritas'  => 'sedang',
             ])
             ->assertUnprocessable()
             ->assertJsonValidationErrors('kandang_id');
    }

    public function test_create_template_validates_waktu_format(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('template-tugas.store'), [
                 'judul'         => 'Test',
                 'kandang_id'    => $this->kandang->kandang_id,
                 'prioritas'     => 'sedang',
                 'waktu_default' => 'bukan-waktu',
             ])
             ->assertUnprocessable()
             ->assertJsonValidationErrors('waktu_default');
    }

    // ─── UPDATE ──────────────────────────────────────────────────────────────

    public function test_admin_can_update_template(): void
    {
        $template = TemplateTugasRutin::factory()->create(['kandang_id' => $this->kandang->kandang_id]);

        $this->actingAs($this->admin)
             ->putJson(route('template-tugas.update', $template->id), [
                 'judul'      => 'Judul Template Updated',
                 'kandang_id' => $this->kandang->kandang_id,
                 'prioritas'  => 'tinggi',
             ])
             ->assertOk()
             ->assertJsonPath('success', true);

        $this->assertDatabaseHas('template_tugas_rutin', [
            'id'    => $template->id,
            'judul' => 'Judul Template Updated',
        ]);
    }

    public function test_update_returns_404_for_nonexistent_template(): void
    {
        $this->actingAs($this->admin)
             ->putJson(route('template-tugas.update', 99999), [
                 'judul'      => 'Test',
                 'kandang_id' => $this->kandang->kandang_id,
                 'prioritas'  => 'sedang',
             ])
             ->assertNotFound();
    }

    // ─── TOGGLE ──────────────────────────────────────────────────────────────

    public function test_admin_can_toggle_template_to_inactive(): void
    {
        $template = TemplateTugasRutin::factory()->create([
            'kandang_id' => $this->kandang->kandang_id,
            'is_active'  => true,
        ]);

        $this->actingAs($this->admin)
             ->patchJson(route('template-tugas.toggle', $template->id))
             ->assertOk()
             ->assertJsonPath('success', true)
             ->assertJsonPath('data.is_active', false);
    }

    public function test_admin_can_toggle_template_back_to_active(): void
    {
        $template = TemplateTugasRutin::factory()->inactive()->create([
            'kandang_id' => $this->kandang->kandang_id,
        ]);

        $this->actingAs($this->admin)
             ->patchJson(route('template-tugas.toggle', $template->id))
             ->assertOk()
             ->assertJsonPath('data.is_active', true);
    }

    // ─── DESTROY ─────────────────────────────────────────────────────────────

    public function test_admin_can_delete_template(): void
    {
        $template = TemplateTugasRutin::factory()->create(['kandang_id' => $this->kandang->kandang_id]);

        $this->actingAs($this->admin)
             ->deleteJson(route('template-tugas.destroy', $template->id))
             ->assertOk()
             ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('template_tugas_rutin', ['id' => $template->id]);
    }

    public function test_delete_returns_404_for_nonexistent_template(): void
    {
        $this->actingAs($this->admin)
             ->deleteJson(route('template-tugas.destroy', 99999))
             ->assertNotFound();
    }
}
