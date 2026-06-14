<?php

namespace Tests\Feature;

use App\Models\Domba;
use App\Models\Kandang;
use App\Models\Penimbangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TrackingPertumbuhanTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private Domba $domba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $kandang     = Kandang::factory()->create();
        $this->domba = Domba::factory()->create([
            'kandang_id' => $kandang->kandang_id,
            'status'     => 'aktif',
        ]);
    }

    // ─── INDEX ───────────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_halaman_tracking_pertumbuhan(): void
    {
        $this->actingAs($this->admin)
             ->get(route('tracking-pertumbuhan.index'))
             ->assertOk();
    }

    public function test_tamu_tidak_dapat_mengakses_tracking_pertumbuhan(): void
    {
        $this->get(route('tracking-pertumbuhan.index'))->assertRedirect('/login');
    }

    public function test_halaman_mendukung_filter_kategori(): void
    {
        $this->actingAs($this->admin)
             ->get(route('tracking-pertumbuhan.index') . '?kategori=cempe')
             ->assertOk();
    }

    public function test_halaman_mendukung_pencarian(): void
    {
        $this->actingAs($this->admin)
             ->get(route('tracking-pertumbuhan.index') . '?search=J-')
             ->assertOk();
    }

    // ─── SHOW ────────────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_detail_pertumbuhan_domba(): void
    {
        $this->actingAs($this->admin)
             ->get(route('tracking-pertumbuhan.show', $this->domba->ear_tag_id))
             ->assertOk();
    }

    public function test_show_mengembalikan_404_untuk_domba_yang_tidak_ada(): void
    {
        $this->actingAs($this->admin)
             ->get(route('tracking-pertumbuhan.show', 'X-INVALID'))
             ->assertNotFound();
    }

    // ─── STORE PENIMBANGAN ───────────────────────────────────────────────────

    public function test_admin_dapat_menyimpan_data_penimbangan(): void
    {
        $this->actingAs($this->admin)
             ->post(route('tracking-pertumbuhan.penimbangan.store', $this->domba->ear_tag_id), [
                 'tanggal_timbang' => today()->toDateString(),
                 'berat_kg'        => 30.5,
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('penimbangan', [
            'ear_tag_id' => $this->domba->ear_tag_id,
            'berat_kg'   => 30.5,
        ]);
    }

    public function test_store_penimbangan_menghitung_adg_otomatis(): void
    {
        // Data penimbangan sebelumnya
        Penimbangan::create([
            'ear_tag_id'      => $this->domba->ear_tag_id,
            'tanggal_timbang' => today()->subDays(10)->toDateString(),
            'berat_kg'        => 25.0,
        ]);

        // Penimbangan baru
        $this->actingAs($this->admin)
             ->post(route('tracking-pertumbuhan.penimbangan.store', $this->domba->ear_tag_id), [
                 'tanggal_timbang' => today()->toDateString(),
                 'berat_kg'        => 27.0, // naik 2 kg dalam 10 hari → ADG = 0.2
             ]);

        $penimbangan = Penimbangan::where('ear_tag_id', $this->domba->ear_tag_id)
            ->orderByDesc('tanggal_timbang')
            ->first();

        $this->assertNotNull($penimbangan->adg);
        $this->assertEquals(0.2, (float) $penimbangan->adg);
    }

    public function test_store_penimbangan_pertama_tidak_memiliki_adg(): void
    {
        $this->actingAs($this->admin)
             ->post(route('tracking-pertumbuhan.penimbangan.store', $this->domba->ear_tag_id), [
                 'tanggal_timbang' => today()->toDateString(),
                 'berat_kg'        => 20.0,
             ]);

        $penimbangan = Penimbangan::where('ear_tag_id', $this->domba->ear_tag_id)->first();
        $this->assertNull($penimbangan->adg);
    }

    public function test_store_penimbangan_gagal_jika_field_wajib_kosong(): void
    {
        $this->actingAs($this->admin)
             ->post(route('tracking-pertumbuhan.penimbangan.store', $this->domba->ear_tag_id), [])
             ->assertSessionHasErrors(['tanggal_timbang', 'berat_kg']);
    }

    public function test_store_penimbangan_gagal_jika_berat_negatif(): void
    {
        $this->actingAs($this->admin)
             ->post(route('tracking-pertumbuhan.penimbangan.store', $this->domba->ear_tag_id), [
                 'tanggal_timbang' => today()->toDateString(),
                 'berat_kg'        => -5.0,
             ])
             ->assertSessionHasErrors('berat_kg');
    }
}
