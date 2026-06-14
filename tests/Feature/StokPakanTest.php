<?php

namespace Tests\Feature;

use App\Models\Domba;
use App\Models\Kandang;
use App\Models\PakanStok;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StokPakanTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private PakanStok $pakan;
    private Domba $domba;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $kandang     = Kandang::factory()->create();
        $this->domba = Domba::factory()->create(['kandang_id' => $kandang->kandang_id, 'status' => 'aktif']);
        $this->pakan = PakanStok::create([
            'jenis'         => 'rumput',
            'nama_pakan'    => 'Rumput Gajah Test',
            'jumlah_stok'   => 100.0,
            'satuan'        => 'kg',
            'stok_minimum'  => 10.0,
            'tanggal_update'=> now(),
        ]);
    }

    // ─── INDEX ───────────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_halaman_stok_pakan(): void
    {
        $this->actingAs($this->admin)
             ->get(route('stok-pakan.index'))
             ->assertOk();
    }

    public function test_tamu_tidak_dapat_mengakses_stok_pakan(): void
    {
        $this->get(route('stok-pakan.index'))->assertRedirect('/login');
    }

    public function test_pengurus_kandang_tidak_dapat_mengakses_stok_pakan(): void
    {
        $pk = User::factory()->pengurusKandang()->create();

        $this->actingAs($pk)
             ->get(route('stok-pakan.index'))
             ->assertRedirect(route('tugas-harian.mobile'));
    }

    // ─── CATAT MASUK ─────────────────────────────────────────────────────────

    public function test_admin_dapat_mencatat_stok_masuk(): void
    {
        $stokAwal = $this->pakan->jumlah_stok;

        $this->actingAs($this->admin)
             ->post(route('stok-pakan.masuk'), [
                 'pakan_id'   => $this->pakan->pakan_id,
                 'jumlah'     => 50.0,
                 'keterangan' => 'Pembelian dari supplier',
             ])
             ->assertRedirect(route('stok-pakan.index'));

        $this->assertDatabaseHas('pakan_stok', [
            'pakan_id'    => $this->pakan->pakan_id,
            'jumlah_stok' => $stokAwal + 50.0,
        ]);
    }

    public function test_catat_masuk_gagal_jika_pakan_id_tidak_valid(): void
    {
        $this->actingAs($this->admin)
             ->post(route('stok-pakan.masuk'), [
                 'pakan_id' => 99999,
                 'jumlah'   => 10.0,
             ])
             ->assertSessionHasErrors('pakan_id');
    }

    public function test_catat_masuk_gagal_jika_jumlah_nol(): void
    {
        $this->actingAs($this->admin)
             ->post(route('stok-pakan.masuk'), [
                 'pakan_id' => $this->pakan->pakan_id,
                 'jumlah'   => 0,
             ])
             ->assertSessionHasErrors('jumlah');
    }

    // ─── CATAT KELUAR ────────────────────────────────────────────────────────

    public function test_admin_dapat_mencatat_pemberian_pakan_ke_domba(): void
    {
        $stokAwal = $this->pakan->jumlah_stok;

        $this->actingAs($this->admin)
             ->post(route('stok-pakan.keluar'), [
                 'pakan_id'          => $this->pakan->pakan_id,
                 'ear_tag_id'        => $this->domba->ear_tag_id,
                 'jumlah'            => 5.0,
                 'tanggal_pemberian' => today()->toDateString(),
             ])
             ->assertRedirect(route('stok-pakan.index'));

        $this->assertDatabaseHas('pakan_stok', [
            'pakan_id'    => $this->pakan->pakan_id,
            'jumlah_stok' => $stokAwal - 5.0,
        ]);
    }

    public function test_catat_keluar_gagal_jika_jumlah_melebihi_stok(): void
    {
        $this->actingAs($this->admin)
             ->post(route('stok-pakan.keluar'), [
                 'pakan_id'   => $this->pakan->pakan_id,
                 'ear_tag_id' => $this->domba->ear_tag_id,
                 'jumlah'     => 999.0, // Lebih dari stok (100 kg)
             ])
             ->assertSessionHasErrors();
    }

    public function test_catat_keluar_gagal_jika_ear_tag_tidak_valid(): void
    {
        $this->actingAs($this->admin)
             ->post(route('stok-pakan.keluar'), [
                 'pakan_id'   => $this->pakan->pakan_id,
                 'ear_tag_id' => 'X-INVALID',
                 'jumlah'     => 5.0,
             ])
             ->assertSessionHasErrors('ear_tag_id');
    }

    public function test_catat_keluar_mencatat_riwayat_ke_tabel_pemberian_pakan(): void
    {
        $this->actingAs($this->admin)
             ->post(route('stok-pakan.keluar'), [
                 'pakan_id'          => $this->pakan->pakan_id,
                 'ear_tag_id'        => $this->domba->ear_tag_id,
                 'jumlah'            => 3.0,
                 'tanggal_pemberian' => today()->toDateString(),
             ]);

        $this->assertDatabaseHas('pemberian_pakan', [
            'pakan_id'   => $this->pakan->pakan_id,
            'ear_tag_id' => $this->domba->ear_tag_id,
        ]);
    }
}
