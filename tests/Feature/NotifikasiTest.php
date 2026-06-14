<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NotifikasiTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    private function buatNotifikasi(array $override = []): int
    {
        return DB::table('notifikasi')->insertGetId(array_merge([
            'user_id'           => $this->admin->user_id,
            'tipe'              => 'stok_menipis',
            'pesan'             => 'Stok pakan menipis.',
            'sudah_dibaca'      => false,
            'tanggal_notifikasi'=> now(),
        ], $override), 'notifikasi_id');
    }

    // ─── INDEX ───────────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_halaman_notifikasi(): void
    {
        $this->actingAs($this->admin)
             ->get(route('notifikasi.index'))
             ->assertOk();
    }

    public function test_tamu_tidak_dapat_mengakses_notifikasi(): void
    {
        $this->get(route('notifikasi.index'))->assertRedirect('/login');
    }

    public function test_halaman_notifikasi_dapat_difilter_berdasarkan_tipe(): void
    {
        $this->actingAs($this->admin)
             ->get(route('notifikasi.index') . '?tipe=stok')
             ->assertOk();
    }

    // ─── MARK AS READ ────────────────────────────────────────────────────────

    public function test_admin_dapat_menandai_satu_notifikasi_sebagai_dibaca(): void
    {
        $notifId = $this->buatNotifikasi(['sudah_dibaca' => false]);

        $this->actingAs($this->admin)
             ->postJson(route('notifikasi.read', $notifId))
             ->assertOk()
             ->assertJsonPath('success', true);
    }

    // ─── MARK ALL READ ───────────────────────────────────────────────────────

    public function test_admin_dapat_menandai_semua_notifikasi_sebagai_dibaca(): void
    {
        $this->buatNotifikasi(['sudah_dibaca' => false]);
        $this->buatNotifikasi(['sudah_dibaca' => false]);

        $this->actingAs($this->admin)
             ->postJson(route('notifikasi.read-all'))
             ->assertOk()
             ->assertJsonPath('success', true);
    }

    // ─── UNREAD COUNT ────────────────────────────────────────────────────────

    public function test_admin_dapat_mengambil_jumlah_notifikasi_belum_dibaca(): void
    {
        $this->buatNotifikasi(['sudah_dibaca' => false]);
        $this->buatNotifikasi(['sudah_dibaca' => false]);

        $response = $this->actingAs($this->admin)
             ->getJson(route('notifikasi.unread-count'))
             ->assertOk();

        $this->assertArrayHasKey('count', $response->json());
        $this->assertGreaterThanOrEqual(2, $response->json('count'));
    }

    // ─── DESTROY ─────────────────────────────────────────────────────────────

    public function test_admin_dapat_menghapus_notifikasi_miliknya(): void
    {
        $notifId = $this->buatNotifikasi();

        $this->actingAs($this->admin)
             ->deleteJson(route('notifikasi.destroy', $notifId))
             ->assertOk()
             ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('notifikasi', ['notifikasi_id' => $notifId]);
    }

    public function test_admin_tidak_dapat_menghapus_notifikasi_milik_user_lain(): void
    {
        $userLain = User::factory()->admin()->create();
        $notifId  = DB::table('notifikasi')->insertGetId([
            'user_id'           => $userLain->user_id,
            'tipe'              => 'stok_menipis',
            'pesan'             => 'Notifikasi milik user lain.',
            'sudah_dibaca'      => false,
            'tanggal_notifikasi'=> now(),
        ], 'notifikasi_id');

        $this->actingAs($this->admin)
             ->deleteJson(route('notifikasi.destroy', $notifId));

        // Notifikasi milik user lain tidak terhapus
        $this->assertDatabaseHas('notifikasi', ['notifikasi_id' => $notifId]);
    }
}
