<?php

namespace Tests\Feature;

use App\Models\Kandang;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use DatabaseTransactions;

    // ─── SUPER ADMIN ─────────────────────────────────────────────────────────

    public function test_super_admin_can_access_dashboard(): void
    {
        $user = User::factory()->superAdmin()->create();

        $this->actingAs($user)->get(route('dashboard'))->assertOk();
    }

    public function test_super_admin_can_access_user_management(): void
    {
        $user = User::factory()->superAdmin()->create();

        $this->actingAs($user)->get(route('users.index'))->assertOk();
    }

    public function test_super_admin_can_access_kandang_management(): void
    {
        $user = User::factory()->superAdmin()->create();

        $this->actingAs($user)->get(route('kandang.index'))->assertOk();
    }

    public function test_super_admin_can_access_template_tugas(): void
    {
        $user = User::factory()->superAdmin()->create();

        $this->actingAs($user)->get(route('template-tugas.index'))->assertOk();
    }

    // ─── ADMIN ───────────────────────────────────────────────────────────────

    public function test_admin_can_access_dashboard(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)->get(route('dashboard'))->assertOk();
    }

    public function test_admin_can_access_domba_index(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)->get(route('domba.index'))->assertOk();
    }

    public function test_admin_can_access_kandang_management(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)->get(route('kandang.index'))->assertOk();
    }

    public function test_admin_can_access_tugas_harian(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)->get(route('tugas-harian.index'))->assertOk();
    }

    // ─── KEPALA KANDANG ───────────────────────────────────────────────────────

    public function test_kepala_kandang_can_access_dashboard(): void
    {
        $user = User::factory()->kepalaKandang()->create();

        $this->actingAs($user)->get(route('dashboard'))->assertOk();
    }

    public function test_kepala_kandang_can_access_domba_index(): void
    {
        $user = User::factory()->kepalaKandang()->create();

        $this->actingAs($user)->get(route('domba.index'))->assertOk();
    }

    public function test_kepala_kandang_can_access_tugas_harian(): void
    {
        $user = User::factory()->kepalaKandang()->create();

        $this->actingAs($user)->get(route('tugas-harian.index'))->assertOk();
    }

    public function test_kepala_kandang_cannot_access_template_tugas(): void
    {
        $user = User::factory()->kepalaKandang()->create();

        $this->actingAs($user)->get(route('template-tugas.index'))->assertForbidden();
    }

    public function test_kepala_kandang_cannot_access_user_management(): void
    {
        $user = User::factory()->kepalaKandang()->create();

        $this->actingAs($user)->get(route('users.index'))->assertForbidden();
    }

    public function test_kepala_kandang_cannot_access_kandang_management(): void
    {
        $user = User::factory()->kepalaKandang()->create();

        $this->actingAs($user)->get(route('kandang.index'))->assertForbidden();
    }

    // ─── PENGURUS KANDANG ─────────────────────────────────────────────────────

    public function test_pengurus_kandang_redirected_from_dashboard(): void
    {
        $user = User::factory()->pengurusKandang()->create();

        $this->actingAs($user)
             ->get(route('dashboard'))
             ->assertRedirect(route('tugas-harian.mobile'));
    }

    public function test_pengurus_kandang_redirected_from_domba_index(): void
    {
        $user = User::factory()->pengurusKandang()->create();

        $this->actingAs($user)
             ->get(route('domba.index'))
             ->assertRedirect(route('tugas-harian.mobile'));
    }

    public function test_pengurus_kandang_redirected_from_kandang_management(): void
    {
        $user = User::factory()->pengurusKandang()->create();

        $this->actingAs($user)
             ->get(route('kandang.index'))
             ->assertRedirect(route('tugas-harian.mobile'));
    }

    public function test_pengurus_kandang_can_access_pk_dashboard(): void
    {
        $user = User::factory()->pengurusKandang()->create();

        $this->actingAs($user)
             ->get(route('pk.dashboard'))
             ->assertOk();
    }

    public function test_pengurus_kandang_can_access_mobile_tugas(): void
    {
        $user = User::factory()->pengurusKandang()->create();

        $this->actingAs($user)
             ->get(route('pk.tugas'))
             ->assertOk();
    }

    public function test_pengurus_kandang_accessing_kk_routes_redirected_to_mobile(): void
    {
        $user = User::factory()->pengurusKandang()->create();

        // Middleware mengarahkan pengurus_kandang ke mobile, bukan 403
        $this->actingAs($user)
             ->get(route('kk.dashboard'))
             ->assertRedirect(route('tugas-harian.mobile'));
    }

    public function test_kepala_kandang_cannot_access_pk_routes(): void
    {
        $user = User::factory()->kepalaKandang()->create();

        $this->actingAs($user)
             ->get(route('pk.dashboard'))
             ->assertForbidden();
    }

    public function test_kepala_kandang_can_access_kk_dashboard(): void
    {
        $user = User::factory()->kepalaKandang()->create();

        $this->actingAs($user)
             ->get(route('kk.dashboard'))
             ->assertOk();
    }

    // ─── UNAUTHENTICATED ──────────────────────────────────────────────────────

    public function test_unauthenticated_redirected_from_all_protected_routes(): void
    {
        $routes = [
            route('dashboard'),
            route('domba.index'),
            route('tugas-harian.index'),
            route('kandang.index'),
            route('template-tugas.index'),
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertRedirect('/login');
        }
    }
}
