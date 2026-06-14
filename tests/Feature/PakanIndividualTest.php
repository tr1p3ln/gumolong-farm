<?php

namespace Tests\Feature;

use App\Models\Domba;
use App\Models\Kandang;
use App\Models\PakanStok;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PakanIndividualTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private Domba $domba;
    private PakanStok $pakan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $kandang     = Kandang::factory()->create();
        $this->domba = Domba::factory()->create([
            'kandang_id' => $kandang->kandang_id,
            'status'     => 'aktif',
        ]);
        $this->pakan = PakanStok::create([
            'jenis'         => 'konsentrat',
            'nama_pakan'    => 'Konsentrat Test ' . uniqid(),
            'jumlah_stok'   => 200.0,
            'satuan'        => 'kg',
            'stok_minimum'  => 20.0,
            'tanggal_update'=> now(),
        ]);
    }

    // ─── INDEX ───────────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_halaman_pakan_individual(): void
    {
        $this->actingAs($this->admin)
             ->get(route('pakan-individual.index'))
             ->assertOk();
    }

    public function test_tamu_tidak_dapat_mengakses_pakan_individual(): void
    {
        $this->get(route('pakan-individual.index'))->assertRedirect('/login');
    }

    public function test_halaman_mendukung_filter_kategori(): void
    {
        $this->actingAs($this->admin)
             ->get(route('pakan-individual.index') . '?kategori=indukan')
             ->assertOk();
    }

    // ─── STORE PEMBERIAN ────────────────────────────────────────────────────

    public function test_admin_dapat_mencatat_pemberian_pakan_individual(): void
    {
        $stokAwal = $this->pakan->jumlah_stok;

        $this->actingAs($this->admin)
             ->postJson(route('pakan-individual.store'), [
                 'ear_tag_id'        => $this->domba->ear_tag_id,
                 'pakan_id'          => $this->pakan->pakan_id,
                 'tanggal_pemberian' => today()->toDateString(),
                 'sesi'              => 'pagi',
                 'jumlah_gram'       => 500,
             ])
             ->assertOk()
             ->assertJsonPath('success', true);

        $this->assertDatabaseHas('pemberian_pakan', [
            'ear_tag_id' => $this->domba->ear_tag_id,
            'pakan_id'   => $this->pakan->pakan_id,
            'sesi'       => 'pagi',
        ]);
    }

    public function test_store_gagal_jika_stok_tidak_cukup(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('pakan-individual.store'), [
                 'ear_tag_id'        => $this->domba->ear_tag_id,
                 'pakan_id'          => $this->pakan->pakan_id,
                 'tanggal_pemberian' => today()->toDateString(),
                 'sesi'              => 'pagi',
                 'jumlah_gram'       => 99999999, // Jauh melebihi stok
             ])
             ->assertStatus(422)
             ->assertJsonPath('success', false);
    }

    public function test_store_gagal_jika_field_wajib_kosong(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('pakan-individual.store'), [])
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['ear_tag_id', 'pakan_id', 'tanggal_pemberian', 'sesi', 'jumlah_gram']);
    }

    public function test_store_gagal_jika_sesi_tidak_valid(): void
    {
        $this->actingAs($this->admin)
             ->postJson(route('pakan-individual.store'), [
                 'ear_tag_id'        => $this->domba->ear_tag_id,
                 'pakan_id'          => $this->pakan->pakan_id,
                 'tanggal_pemberian' => today()->toDateString(),
                 'sesi'              => 'malam', // Tidak valid
                 'jumlah_gram'       => 300,
             ])
             ->assertUnprocessable()
             ->assertJsonValidationErrors('sesi');
    }

    // ─── SEARCH DOMBA ───────────────────────────────────────────────────────

    public function test_search_domba_mengembalikan_array_kosong_jika_query_kosong(): void
    {
        $this->actingAs($this->admin)
             ->getJson(route('pakan-individual.search-domba') . '?q=')
             ->assertOk()
             ->assertJson([]);
    }

    public function test_search_domba_mengembalikan_hasil_yang_sesuai(): void
    {
        $this->actingAs($this->admin)
             ->getJson(route('pakan-individual.search-domba') . '?q=' . substr($this->domba->ear_tag_id, 0, 2))
             ->assertOk()
             ->assertJsonIsArray();
    }

    // ─── STATS ──────────────────────────────────────────────────────────────

    public function test_admin_dapat_mengambil_statistik_fcr_domba(): void
    {
        $this->actingAs($this->admin)
             ->getJson(route('pakan-individual.stats', $this->domba->ear_tag_id))
             ->assertOk()
             ->assertJsonStructure([
                 'fcr',
                 'total_pakan_30hr',
                 'rata_pakan_harian_gr',
                 'kenaikan_bb',
             ]);
    }
}
