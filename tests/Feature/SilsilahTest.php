<?php

namespace Tests\Feature;

use App\Models\Domba;
use App\Models\Kandang;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SilsilahTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private Domba $domba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $kandang     = Kandang::factory()->create();
        $this->domba = Domba::factory()->create(['kandang_id' => $kandang->kandang_id]);
    }

    // ─── INDEX ───────────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_halaman_silsilah(): void
    {
        $this->actingAs($this->admin)
             ->get(route('silsilah.index'))
             ->assertOk();
    }

    public function test_tamu_tidak_dapat_mengakses_halaman_silsilah(): void
    {
        $this->get(route('silsilah.index'))->assertRedirect('/login');
    }

    public function test_halaman_silsilah_mendukung_pencarian(): void
    {
        $this->actingAs($this->admin)
             ->get(route('silsilah.index') . '?search=B-')
             ->assertOk();
    }

    public function test_halaman_silsilah_mendukung_filter_kategori(): void
    {
        $this->actingAs($this->admin)
             ->get(route('silsilah.index') . '?kategori=indukan')
             ->assertOk();
    }

    // ─── SHOW ────────────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_halaman_detail_silsilah(): void
    {
        $this->actingAs($this->admin)
             ->get(route('silsilah.show', $this->domba->ear_tag_id))
             ->assertOk();
    }

    public function test_admin_dapat_mengambil_data_pedigree_sebagai_json(): void
    {
        $response = $this->actingAs($this->admin)
             ->getJson(route('silsilah.show', $this->domba->ear_tag_id))
             ->assertOk();

        $response->assertJsonStructure([
            'domba',
            'pedigree',
            'coi',
            'coi_persen',
            'status_inbreeding',
        ]);
    }

    public function test_show_mengembalikan_404_untuk_domba_tidak_ada(): void
    {
        $this->actingAs($this->admin)
             ->getJson(route('silsilah.show', 'X-TIDAK-ADA'))
             ->assertNotFound();
    }

    // ─── CEK INBREEDING ──────────────────────────────────────────────────────

    public function test_admin_dapat_cek_inbreeding_dua_domba(): void
    {
        $kandang  = Kandang::factory()->create();
        $pejantan = Domba::factory()->create([
            'kandang_id'    => $kandang->kandang_id,
            'jenis_kelamin' => 'jantan',
        ]);
        $indukan  = Domba::factory()->create([
            'kandang_id'    => $kandang->kandang_id,
            'jenis_kelamin' => 'betina',
        ]);

        $this->actingAs($this->admin)
             ->postJson(route('silsilah.cek-inbreeding'), [
                 'induk_id'    => $indukan->ear_tag_id,
                 'pejantan_id' => $pejantan->ear_tag_id,
             ])
             ->assertOk()
             ->assertJsonStructure(['coi', 'coi_persen', 'status', 'rekomendasi', 'aman']);
    }

    public function test_cek_inbreeding_gagal_jika_field_kosong(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('silsilah.cek-inbreeding'), [])
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['induk_id', 'pejantan_id']);
    }

    public function test_domba_tanpa_leluhur_bersama_memiliki_coi_nol(): void
    {
        $kandang  = Kandang::factory()->create();
        $pejantan = Domba::factory()->create(['kandang_id' => $kandang->kandang_id]);
        $indukan  = Domba::factory()->create(['kandang_id' => $kandang->kandang_id]);

        $response = $this->actingAs($this->admin)
             ->postJson(route('silsilah.cek-inbreeding'), [
                 'induk_id'    => $indukan->ear_tag_id,
                 'pejantan_id' => $pejantan->ear_tag_id,
             ])
             ->assertOk();

        $this->assertEquals(0.0, $response->json('coi'));
        $this->assertTrue($response->json('aman'));
    }

    // ─── REKOMENDASI PEJANTAN ────────────────────────────────────────────────

    public function test_admin_dapat_mendapat_rekomendasi_pejantan(): void
    {
        $kandang = Kandang::factory()->create();
        $indukan = Domba::factory()->create(['kandang_id' => $kandang->kandang_id]);

        $this->actingAs($this->admin)
             ->getJson(route('silsilah.rekomendasi-pejantan') . '?induk_id=' . $indukan->ear_tag_id)
             ->assertOk()
             ->assertJsonStructure(['induk_id', 'rekomendasi']);
    }

    public function test_rekomendasi_pejantan_gagal_tanpa_induk_id(): void
    {
        $this->actingAs($this->admin)
             ->getJson(route('silsilah.rekomendasi-pejantan'))
             ->assertUnprocessable()
             ->assertJsonValidationErrors('induk_id');
    }
}
