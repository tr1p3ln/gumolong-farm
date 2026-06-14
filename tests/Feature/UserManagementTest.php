<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use DatabaseTransactions;

    private User $superAdmin;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->superAdmin()->create();
        $this->admin      = User::factory()->admin()->create();
    }

    // ─── INDEX ───────────────────────────────────────────────────────────────

    public function test_super_admin_dapat_melihat_daftar_pengguna(): void
    {
        $this->actingAs($this->superAdmin)
             ->get(route('users.index'))
             ->assertOk();
    }

    public function test_admin_dapat_melihat_daftar_pengguna(): void
    {
        $this->actingAs($this->admin)
             ->get(route('users.index'))
             ->assertOk();
    }

    public function test_kepala_kandang_tidak_dapat_mengakses_manajemen_pengguna(): void
    {
        $kk = User::factory()->kepalaKandang()->create();

        $this->actingAs($kk)
             ->get(route('users.index'))
             ->assertForbidden();
    }

    // ─── STORE ───────────────────────────────────────────────────────────────

    public function test_super_admin_dapat_membuat_pengguna_baru(): void
    {
        $email = 'newuser-' . uniqid() . '@example.com';

        $this->actingAs($this->superAdmin)
             ->post(route('users.store'), [
                 'nama'     => 'Pengguna Baru Test',
                 'email'    => $email,
                 'password' => 'Password123',
                 'role'     => 'admin',
                 'status'   => 'aktif',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('user', ['email' => $email]);
    }

    public function test_store_pengguna_gagal_jika_field_wajib_kosong(): void
    {
        $this->actingAs($this->superAdmin)
             ->post(route('users.store'), [])
             ->assertSessionHasErrors(['nama', 'email', 'password', 'role', 'status']);
    }

    public function test_store_pengguna_gagal_jika_email_sudah_terdaftar(): void
    {
        $this->actingAs($this->superAdmin)
             ->post(route('users.store'), [
                 'nama'     => 'Duplikat',
                 'email'    => $this->admin->email,
                 'password' => 'Password123',
                 'role'     => 'admin',
                 'status'   => 'aktif',
             ])
             ->assertSessionHasErrors('email');
    }

    public function test_admin_biasa_tidak_dapat_membuat_akun_super_admin(): void
    {
        $this->actingAs($this->admin)
             ->post(route('users.store'), [
                 'nama'     => 'Super Admin Baru',
                 'email'    => 'superadmin-new-' . uniqid() . '@example.com',
                 'password' => 'Password123',
                 'role'     => 'super_admin',
                 'status'   => 'aktif',
             ])
             ->assertForbidden();
    }

    // ─── UPDATE ──────────────────────────────────────────────────────────────

    public function test_super_admin_dapat_memperbarui_data_pengguna(): void
    {
        $target = User::factory()->admin()->create();

        $this->actingAs($this->superAdmin)
             ->put(route('users.update', $target->user_id), [
                 'nama'   => 'Nama Diperbarui',
                 'email'  => $target->email,
                 'role'   => 'admin',
                 'status' => 'aktif',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('user', [
            'user_id' => $target->user_id,
            'nama'    => 'Nama Diperbarui',
        ]);
    }

    public function test_pengguna_tidak_dapat_menonaktifkan_akun_sendiri(): void
    {
        $this->actingAs($this->superAdmin)
             ->put(route('users.update', $this->superAdmin->user_id), [
                 'nama'   => $this->superAdmin->nama,
                 'email'  => $this->superAdmin->email,
                 'role'   => 'super_admin',
                 'status' => 'nonaktif',
             ])
             ->assertSessionHasErrors('status');
    }

    // ─── TOGGLE STATUS ───────────────────────────────────────────────────────

    public function test_super_admin_dapat_mengubah_status_aktif_pengguna(): void
    {
        $target = User::factory()->admin()->create(['status' => 'aktif']);

        $this->actingAs($this->superAdmin)
             ->patchJson(route('users.toggle-status', $target->user_id))
             ->assertOk()
             ->assertJsonPath('data.status', 'nonaktif');
    }

    public function test_tidak_dapat_toggle_status_akun_sendiri(): void
    {
        $this->actingAs($this->superAdmin)
             ->patch(route('users.toggle-status', $this->superAdmin->user_id))
             ->assertSessionHasErrors('toggle');
    }

    public function test_admin_tidak_dapat_mengubah_status_akun_super_admin(): void
    {
        $this->actingAs($this->admin)
             ->patch(route('users.toggle-status', $this->superAdmin->user_id))
             ->assertForbidden();
    }

    // ─── DESTROY ─────────────────────────────────────────────────────────────

    public function test_super_admin_dapat_menghapus_pengguna(): void
    {
        $target = User::factory()->admin()->create();
        $userId = $target->user_id;

        $this->actingAs($this->superAdmin)
             ->delete(route('users.destroy', $userId))
             ->assertRedirect();

        $this->assertDatabaseMissing('user', ['user_id' => $userId]);
    }

    public function test_admin_tidak_dapat_menghapus_pengguna(): void
    {
        $target = User::factory()->pengurusKandang()->create();

        $this->actingAs($this->admin)
             ->delete(route('users.destroy', $target->user_id))
             ->assertForbidden();
    }

    public function test_super_admin_tidak_dapat_menghapus_akun_sendiri(): void
    {
        $this->actingAs($this->superAdmin)
             ->delete(route('users.destroy', $this->superAdmin->user_id))
             ->assertSessionHasErrors('delete');
    }

    // ─── RESET PASSWORD ──────────────────────────────────────────────────────

    public function test_super_admin_dapat_mereset_password_pengguna(): void
    {
        $target = User::factory()->admin()->create();

        $this->actingAs($this->superAdmin)
             ->patch(route('users.reset-password', $target->user_id), [
                 'password'              => 'NewPassword123',
                 'password_confirmation' => 'NewPassword123',
             ])
             ->assertRedirect();
    }

    public function test_admin_tidak_dapat_mereset_password_super_admin(): void
    {
        $this->actingAs($this->admin)
             ->patch(route('users.reset-password', $this->superAdmin->user_id), [
                 'password'              => 'NewPassword123',
                 'password_confirmation' => 'NewPassword123',
             ])
             ->assertForbidden();
    }
}
