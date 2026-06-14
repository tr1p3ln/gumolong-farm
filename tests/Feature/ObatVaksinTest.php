<?php

namespace Tests\Feature;

use App\Models\ObatVaksin;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ObatVaksinTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private ObatVaksin $obat;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->obat  = ObatVaksin::create([
            'nama_obat'    => 'Obat Test ' . uniqid(),
            'tipe'         => 'obat',
            'satuan'       => 'ml',
            'stok'         => 100,
            'stok_minimum' => 10,
        ]);
    }

    // ─── INDEX ───────────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_halaman_obat_vaksin(): void
    {
        $this->actingAs($this->admin)
             ->get(route('obat-vaksin.index'))
             ->assertOk();
    }

    public function test_tamu_tidak_dapat_mengakses_obat_vaksin(): void
    {
        $this->get(route('obat-vaksin.index'))->assertRedirect('/login');
    }

    // ─── STORE ───────────────────────────────────────────────────────────────

    public function test_admin_dapat_menambah_obat_vaksin(): void
    {
        $nama = 'Vaksin PMK Test ' . uniqid();

        $this->actingAs($this->admin)
             ->post(route('obat-vaksin.store'), [
                 'nama_obat'    => $nama,
                 'tipe'         => 'vaksin',
                 'satuan'       => 'dosis',
                 'stok'         => 50,
                 'stok_minimum' => 5,
             ])
             ->assertRedirect(route('obat-vaksin.index'));

        $this->assertDatabaseHas('obat_vaksin', ['nama_obat' => $nama]);
    }

    public function test_store_gagal_jika_field_wajib_kosong(): void
    {
        $this->actingAs($this->admin)
             ->post(route('obat-vaksin.store'), [])
             ->assertSessionHasErrors(['nama_obat', 'tipe', 'satuan', 'stok', 'stok_minimum']);
    }

    public function test_store_gagal_jika_tipe_tidak_valid(): void
    {
        $this->actingAs($this->admin)
             ->post(route('obat-vaksin.store'), [
                 'nama_obat'    => 'Test',
                 'tipe'         => 'tidak_valid',
                 'satuan'       => 'ml',
                 'stok'         => 10,
                 'stok_minimum' => 2,
             ])
             ->assertSessionHasErrors('tipe');
    }

    public function test_store_gagal_jika_satuan_tidak_valid(): void
    {
        $this->actingAs($this->admin)
             ->post(route('obat-vaksin.store'), [
                 'nama_obat'    => 'Test',
                 'tipe'         => 'obat',
                 'satuan'       => 'liter', // Tidak valid
                 'stok'         => 10,
                 'stok_minimum' => 2,
             ])
             ->assertSessionHasErrors('satuan');
    }

    // ─── SHOW ────────────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_detail_obat_dalam_json(): void
    {
        $this->actingAs($this->admin)
             ->getJson(route('obat-vaksin.show', $this->obat->obat_id))
             ->assertOk()
             ->assertJsonPath('obat_id', $this->obat->obat_id)
             ->assertJsonPath('nama_obat', $this->obat->nama_obat);
    }

    public function test_show_mengembalikan_404_untuk_obat_yang_tidak_ada(): void
    {
        $this->actingAs($this->admin)
             ->getJson(route('obat-vaksin.show', 99999))
             ->assertNotFound();
    }

    // ─── UPDATE ──────────────────────────────────────────────────────────────

    public function test_admin_dapat_memperbarui_obat_vaksin(): void
    {
        $namaBaru = 'Nama Obat Diperbarui ' . uniqid();

        $this->actingAs($this->admin)
             ->put(route('obat-vaksin.update', $this->obat->obat_id), [
                 'nama_obat'    => $namaBaru,
                 'tipe'         => 'obat',
                 'satuan'       => 'tablet',
                 'stok'         => 80,
                 'stok_minimum' => 8,
             ])
             ->assertRedirect(route('obat-vaksin.index'));

        $this->assertDatabaseHas('obat_vaksin', [
            'obat_id'  => $this->obat->obat_id,
            'nama_obat' => $namaBaru,
        ]);
    }

    // ─── DESTROY ─────────────────────────────────────────────────────────────

    public function test_admin_dapat_menghapus_obat_vaksin(): void
    {
        $obatId = $this->obat->obat_id;

        $this->actingAs($this->admin)
             ->delete(route('obat-vaksin.destroy', $obatId))
             ->assertRedirect(route('obat-vaksin.index'));

        $this->assertDatabaseMissing('obat_vaksin', ['obat_id' => $obatId]);
    }

    // ─── SEARCH REKAM ────────────────────────────────────────────────────────

    public function test_search_rekam_mengembalikan_array_kosong_jika_query_kosong(): void
    {
        $this->actingAs($this->admin)
             ->getJson(route('obat-vaksin.searchRekam') . '?q=')
             ->assertOk()
             ->assertJson([]);
    }

    public function test_search_rekam_mengembalikan_hasil_json(): void
    {
        $this->actingAs($this->admin)
             ->getJson(route('obat-vaksin.searchRekam') . '?q=J-')
             ->assertOk()
             ->assertJsonIsArray();
    }
}
