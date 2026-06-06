<?php

namespace Tests\Feature;

use App\Models\Domba;
use App\Models\Kandang;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DombaTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $superAdmin;
    private Kandang $kandang;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin      = User::factory()->admin()->create();
        $this->superAdmin = User::factory()->superAdmin()->create();
        $this->kandang    = Kandang::factory()->create();
    }

    // ─── INDEX ───────────────────────────────────────────────────────────────

    public function test_admin_can_view_domba_index(): void
    {
        $this->actingAs($this->admin)
             ->get(route('domba.index'))
             ->assertOk();
    }

    public function test_unauthenticated_cannot_view_domba(): void
    {
        $this->get(route('domba.index'))->assertRedirect('/login');
    }

    public function test_pengurus_kandang_cannot_view_domba_index(): void
    {
        $pk = User::factory()->pengurusKandang()->create();

        $this->actingAs($pk)
             ->get(route('domba.index'))
             ->assertRedirect(route('tugas-harian.mobile'));
    }

    // ─── GENERATE EAR TAG ─────────────────────────────────────────────────────

    public function test_generate_ear_tag_returns_jantan_prefix(): void
    {
        $response = $this->actingAs($this->admin)
                         ->getJson(route('domba.generate-ear-tag') . '?jenis_kelamin=jantan');

        $response->assertOk()
                 ->assertJsonPath('jenis_kelamin', 'jantan');

        $earTag = $response->json('ear_tag');
        $this->assertStringStartsWith('J-', $earTag);
    }

    public function test_generate_ear_tag_returns_betina_prefix(): void
    {
        $response = $this->actingAs($this->admin)
                         ->getJson(route('domba.generate-ear-tag') . '?jenis_kelamin=betina');

        $response->assertOk()
                 ->assertJsonPath('jenis_kelamin', 'betina');

        $earTag = $response->json('ear_tag');
        $this->assertStringStartsWith('B-', $earTag);
    }

    public function test_generate_ear_tag_fails_for_invalid_gender(): void
    {
        $this->actingAs($this->admin)
             ->getJson(route('domba.generate-ear-tag') . '?jenis_kelamin=invalid')
             ->assertUnprocessable();
    }

    // ─── STORE ───────────────────────────────────────────────────────────────

    public function test_admin_can_create_domba(): void
    {
        $payload = [
            'jenis_kelamin' => 'jantan',
            'ras'           => 'Merino',
            'kategori'      => 'pejantan',
            'kandang_id'    => $this->kandang->kandang_id,
        ];

        $response = $this->actingAs($this->admin)
                         ->postJson(route('domba.store'), $payload);

        $response->assertCreated()
                 ->assertJsonPath('success', true);

        $earTagId = $response->json('ear_tag_id');
        $this->assertDatabaseHas('domba', ['ear_tag_id' => $earTagId]);
    }

    public function test_create_domba_auto_generates_ear_tag(): void
    {
        $response = $this->actingAs($this->admin)
                         ->postJson(route('domba.store'), [
                             'jenis_kelamin' => 'betina',
                             'ras'           => 'Dorper',
                             'kategori'      => 'indukan',
                             'kandang_id'    => $this->kandang->kandang_id,
                         ]);

        $response->assertCreated();
        $earTag = $response->json('ear_tag_id');
        $this->assertMatchesRegularExpression('/^B-\d{3}$/', $earTag);
    }

    public function test_create_domba_with_initial_weight(): void
    {
        $response = $this->actingAs($this->admin)
                         ->postJson(route('domba.store'), [
                             'jenis_kelamin'        => 'jantan',
                             'ras'                  => 'Garut',
                             'kategori'             => 'cempe',
                             'kandang_id'           => $this->kandang->kandang_id,
                             'berat_awal'           => 25.5,
                             'tanggal_timbang_awal' => today()->toDateString(),
                         ]);

        $response->assertCreated();
        $earTagId = $response->json('ear_tag_id');
        $this->assertDatabaseHas('penimbangan', ['ear_tag_id' => $earTagId, 'berat_kg' => 25.5]);
    }

    public function test_create_domba_requires_mandatory_fields(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('domba.store'), [])
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['jenis_kelamin', 'ras', 'kategori', 'kandang_id']);
    }

    public function test_create_domba_requires_valid_kandang(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('domba.store'), [
                 'jenis_kelamin' => 'jantan',
                 'ras'           => 'Merino',
                 'kategori'      => 'pejantan',
                 'kandang_id'    => 99999,
             ])
             ->assertUnprocessable()
             ->assertJsonValidationErrors('kandang_id');
    }

    public function test_create_domba_validates_jenis_kelamin(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('domba.store'), [
                 'jenis_kelamin' => 'tidak_valid',
                 'ras'           => 'Merino',
                 'kategori'      => 'pejantan',
                 'kandang_id'    => $this->kandang->kandang_id,
             ])
             ->assertUnprocessable()
             ->assertJsonValidationErrors('jenis_kelamin');
    }

    public function test_create_domba_validates_kategori(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('domba.store'), [
                 'jenis_kelamin' => 'jantan',
                 'ras'           => 'Merino',
                 'kategori'      => 'salah',
                 'kandang_id'    => $this->kandang->kandang_id,
             ])
             ->assertUnprocessable()
             ->assertJsonValidationErrors('kategori');
    }

    // ─── SHOW ────────────────────────────────────────────────────────────────

    public function test_admin_can_view_domba_detail(): void
    {
        $domba = Domba::factory()->create(['kandang_id' => $this->kandang->kandang_id]);

        $this->actingAs($this->admin)
             ->getJson(route('domba.show', $domba->ear_tag_id))
             ->assertOk()
             ->assertJsonPath('success', true)
             ->assertJsonPath('data.ear_tag_id', $domba->ear_tag_id);
    }

    public function test_show_returns_404_for_nonexistent_domba(): void
    {
        $this->actingAs($this->admin)
             ->getJson(route('domba.show', 'X-999'))
             ->assertNotFound();
    }

    // ─── UPDATE ──────────────────────────────────────────────────────────────

    public function test_admin_can_update_domba(): void
    {
        $domba = Domba::factory()->create(['kandang_id' => $this->kandang->kandang_id]);

        $this->actingAs($this->admin)
             ->putJson(route('domba.update', $domba->ear_tag_id), [
                 'ras'        => 'Suffolk',
                 'kategori'   => 'indukan',
                 'status'     => 'aktif',
                 'kandang_id' => $this->kandang->kandang_id,
             ])
             ->assertOk()
             ->assertJsonPath('success', true);

        $this->assertDatabaseHas('domba', [
            'ear_tag_id' => $domba->ear_tag_id,
            'ras'        => 'Suffolk',
        ]);
    }

    public function test_update_cannot_change_ear_tag_id(): void
    {
        $domba = Domba::factory()->create(['kandang_id' => $this->kandang->kandang_id]);
        $originalEarTag = $domba->ear_tag_id;

        $this->actingAs($this->admin)
             ->putJson(route('domba.update', $domba->ear_tag_id), [
                 'ras'        => 'Merino',
                 'kandang_id' => $this->kandang->kandang_id,
             ]);

        // ear_tag_id tidak berubah
        $this->assertDatabaseHas('domba', ['ear_tag_id' => $originalEarTag]);
    }

    // ─── DESTROY ─────────────────────────────────────────────────────────────

    public function test_super_admin_can_soft_delete_domba(): void
    {
        $domba = Domba::factory()->create(['kandang_id' => $this->kandang->kandang_id]);

        $this->actingAs($this->superAdmin)
             ->deleteJson(route('domba.destroy', $domba->ear_tag_id))
             ->assertOk()
             ->assertJsonPath('success', true);

        $this->assertSoftDeleted('domba', ['ear_tag_id' => $domba->ear_tag_id]);
    }

    public function test_admin_cannot_delete_domba(): void
    {
        $domba = Domba::factory()->create(['kandang_id' => $this->kandang->kandang_id]);

        $this->actingAs($this->admin)
             ->deleteJson(route('domba.destroy', $domba->ear_tag_id))
             ->assertForbidden()
             ->assertJsonPath('success', false);

        $this->assertDatabaseHas('domba', ['ear_tag_id' => $domba->ear_tag_id]);
    }

    // ─── SEARCH FILTER ───────────────────────────────────────────────────────

    public function test_domba_index_filters_by_kategori(): void
    {
        $this->actingAs($this->admin)
             ->get(route('domba.index') . '?kategori=cempe')
             ->assertOk();
    }

    public function test_domba_index_filters_by_status(): void
    {
        $this->actingAs($this->admin)
             ->get(route('domba.index') . '?status=aktif')
             ->assertOk();
    }

    public function test_domba_index_supports_search(): void
    {
        $this->actingAs($this->admin)
             ->get(route('domba.index') . '?search=garut')
             ->assertOk();
    }
}
